@extends('layouts.receptionist')

@section('title', 'Agent Dashboard - Video Call')

@push('styles')
<style>
    .video-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        height: calc(100vh - 200px);
        min-height: 500px;
    }
    
    .video-wrapper {
        position: relative;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .video-wrapper video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .video-label {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
    }
    
    .controls {
        display: flex;
        justify-content: center;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-top: 20px;
    }
    
    .control-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        font-size: 24px;
        transition: all 0.3s;
    }
    
    .control-btn.end-call {
        background: #dc3545;
        color: white;
    }
    
    .control-btn.active {
        background: #28a745;
        color: white;
    }
    
    .control-btn.inactive {
        background: #6c757d;
        color: white;
    }
    
    .chat-panel {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        font-weight: bold;
    }
    
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
    }
    
    .chat-message {
        margin-bottom: 10px;
        padding: 10px 15px;
        border-radius: 20px;
        max-width: 80%;
    }
    
    .chat-message.sent {
        background: #007bff;
        color: white;
        margin-left: auto;
    }
    
    .chat-message.received {
        background: #e9ecef;
    }
    
    .chat-input {
        padding: 15px;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 10px;
    }
    
    .chat-input input {
        flex: 1;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 25px;
        outline: none;
    }
    
    .status-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .status-indicator {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }
    
    .status-free { background: #28a745; }
    .status-busy { background: #dc3545; }
    .status-offline { background: #6c757d; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    
    .stat-value {
        font-size: 36px;
        font-weight: bold;
        color: #007bff;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $todayCalls }}</div>
            <div class="stat-label">Calls Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ gmdate('H:i:s', $todayDuration) }}</div>
            <div class="stat-label">Total Duration</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $pendingQueue }}</div>
            <div class="stat-label">Waiting in Queue</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($agent->average_rating, 1) }}</div>
            <div class="stat-label">Avg Rating</div>
        </div>
    </div>

    <!-- Status Toggle -->
    <div class="status-card mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">Your Status</h5>
                <p class="mb-0 text-muted">Set your availability for taking calls</p>
            </div>
            <div class="btn-group">
                <button class="btn btn-outline-success" onclick="setStatus('free')">Free</button>
                <button class="btn btn-outline-danger" onclick="setStatus('busy')">Busy</button>
                <button class="btn btn-outline-secondary" onclick="setStatus('offline')">Offline</button>
            </div>
        </div>
        <div class="mt-3">
            <span class="status-indicator status-{{ $agent->status }}" id="status-indicator"></span>
            <span id="status-text" class="text-capitalize">{{ $agent->status }}</span>
        </div>
    </div>

    <!-- Take Next Call -->
    @if($agent->isAvailable())
    <div class="status-card mb-4" id="next-call-section">
        <div class="text-center">
            <h4><i class="fas fa-phone-alt"></i> Next Customer Waiting</h4>
            <p class="text-muted">Take the next customer from the queue</p>
            <button class="btn btn-success btn-lg px-5" onclick="startCall()">
                <i class="fas fa-phone-alt"></i> Take Call
            </button>
        </div>
    </div>
    @endif

    <!-- Call Interface -->
    <div id="call-interface" style="display: none;">
        <div class="row">
            <div class="col-lg-9">
                <div class="video-container">
                    <div class="video-wrapper">
                        <video id="local-video" autoplay muted playsinline></video>
                        <span class="video-label">You</span>
                    </div>
                    <div class="video-wrapper">
                        <video id="remote-video" autoplay playsinline></video>
                        <span class="video-label" id="remote-label">Customer</span>
                    </div>
                </div>
                
                <div class="controls">
                    <button class="control-btn active" id="mic-btn" onclick="toggleMic()">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button class="control-btn active" id="camera-btn" onclick="toggleCamera()">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="control-btn" id="screen-btn" onclick="toggleScreenShare()">
                        <i class="fas fa-desktop"></i>
                    </button>
                    <button class="control-btn end-call" onclick="endCall()">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="chat-panel" style="height: calc(100vh - 200px);">
                    <div class="chat-header">
                        <i class="fas fa-comments"></i> Chat with Customer
                    </div>
                    <div class="chat-messages" id="chat-messages"></div>
                    <div class="chat-input">
                        <input type="text" id="chat-input" placeholder="Type a message..." onkeypress="handleChatKeypress(event)">
                        <button class="btn btn-primary rounded-circle" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
