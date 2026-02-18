@extends('layouts.receptionist')

@section('title', 'Agent Dashboard - Video Call')

@push('styles')
<script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.22.0.js"></script>
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
            <div class="stat-value" id="today-calls">{{ $todayCalls }}</div>
            <div class="stat-label">Calls Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="today-duration">{{ gmdate('H:i:s', $todayDuration) }}</div>
            <div class="stat-label">Total Duration</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="pending-queue">{{ $pendingQueue }}</div>
            <div class="stat-label">Waiting in Queue</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="avg-rating">{{ number_format($agent->average_rating, 1) }}</div>
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
    <div class="status-card mb-4" id="next-call-section" style="display: {{ $agent->isAvailable() ? 'block' : 'none' }}">
        <div class="text-center">
            <h4><i class="fas fa-phone-alt"></i> Next Customer Waiting</h4>
            <p class="text-muted">Take the next customer from the queue</p>
            <button class="btn btn-success btn-lg px-5" onclick="startCall()">
                <i class="fas fa-phone-alt"></i> Take Call
            </button>
        </div>
    </div>

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

@push('scripts')
<script>
    // Real-time queue polling - update every 3 seconds
    let pollInterval = null;
    
    function startPolling() {
        // Update immediately
        fetchAgentStats();
        
        // Then poll every 3 seconds
        pollInterval = setInterval(fetchAgentStats, 3000);
    }
    
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }
    
    async function fetchAgentStats() {
        try {
            const response = await fetch('/video/agent/stats');
            const data = await response.json();
            
            if (data.error) {
                console.error('Error fetching stats:', data.error);
                return;
            }
            
            // Update stats on the page
            document.getElementById('today-calls').textContent = data.today_calls;
            document.getElementById('today-duration').textContent = data.today_duration;
            document.getElementById('pending-queue').textContent = data.pending_queue;
            document.getElementById('avg-rating').textContent = data.average_rating;
            
            // Update status indicator
            document.getElementById('status-indicator').className = `status-indicator status-${data.agent_status}`;
            document.getElementById('status-text').textContent = data.agent_status;
            
            // Show/hide next call section based on availability and queue
            const nextCallSection = document.getElementById('next-call-section');
            if (nextCallSection) {
                nextCallSection.style.display = (data.is_available && data.pending_queue > 0) ? 'block' : 'none';
            }
        } catch (error) {
            console.error('Error polling agent stats:', error);
        }
    }
    
    // Start polling when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startPolling();
    });
    
    // Stop polling when leaving page
    window.addEventListener('beforeunload', function() {
        stopPolling();
    });

    let client = null;
    let localTracks = [];
    let remoteTracks = {};
    let channel = null;
    let uid = null;
    let isMuted = false;
    let isCameraOff = false;
    let isScreenSharing = false;
    let sessionId = null;

    // Set Status
    async function setStatus(status) {
        try {
            const response = await fetch('/video/agent/status', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status })
            });
            
            const data = await response.json();
            
            document.getElementById('status-indicator').className = `status-indicator status-${status}`;
            document.getElementById('status-text').textContent = status;
            
            if (data.call_started) {
                startCallWithData(data);
            }
        } catch (error) {
            console.error('Error setting status:', error);
        }
    }

    // Start Call
    async function startCall() {
        try {
            const response = await fetch('/video/agent/start-call', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.channel) {
                startCallWithData(data);
            } else {
                alert(data.error || 'No customers in queue');
            }
        } catch (error) {
            console.error('Error starting call:', error);
        }
    }

    async function startCallWithData(data) {
        console.log('Agent joining channel with data:', data);
        
        if (!data.app_id) {
            alert('Error: App ID is missing from server response');
            return;
        }
        
        await initAgora(data.app_id);
        
        channel = data.channel;
        uid = data.uid;
        sessionId = data.session_id;
        
        await client.join(data.app_id, channel, data.token, uid);
        
        localTracks = await AgoraRTC.createMicrophoneAndCameraTracks();
        localTracks[1].play('local-video');
        
        await client.publish(localTracks);
        
        document.getElementById('next-call-section').style.display = 'none';
        document.getElementById('call-interface').style.display = 'block';
        document.getElementById('remote-label').textContent = data.customer_name || 'Customer';
    }

    async function initAgora(appId) {
        client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });
        
        client.on('user-published', async (user, mediaType) => {
            await client.subscribe(user, mediaType);
            if (mediaType === 'video') {
                remoteTracks[user.uid] = user.videoTrack;
                user.videoTrack.play('remote-video');
            }
            if (mediaType === 'audio') {
                remoteTracks[user.uid] = user.audioTrack;
                user.audioTrack.play();
            }
        });

        client.on('user-unpublished', (user) => {
            delete remoteTracks[user.uid];
        });

        client.on('user-left', async (user) => {
            delete remoteTracks[user.uid];
            await endCall();
        });
    }

    function toggleMic() {
        if (localTracks[0]) {
            isMuted = !isMuted;
            localTracks[0].setEnabled(!isMuted);
            document.getElementById('mic-btn').classList.toggle('active', !isMuted);
        }
    }

    function toggleCamera() {
        if (localTracks[1]) {
            isCameraOff = !isCameraOff;
            localTracks[1].setEnabled(!isCameraOff);
            document.getElementById('camera-btn').classList.toggle('active', !isCameraOff);
        }
    }

    async function toggleScreenShare() {
        if (isScreenSharing) {
            if (localTracks[2]) {
                localTracks[2].close();
                localTracks.pop();
                await client.unpublish(localTracks[2]);
            }
            await client.publish(localTracks[1]);
            localTracks[1].play('local-video');
            isScreenSharing = false;
        } else {
            try {
                const screenTrack = await AgoraRTC.createScreenVideoTrack();
                await client.unpublish(localTracks[1]);
                await client.publish(screenTrack);
                screenTrack.play('local-video');
                localTracks.push(screenTrack);
                isScreenSharing = true;
            } catch (error) {
                console.error('Screen share error:', error);
            }
        }
    }

    function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (message) {
            addChatMessage(message, 'sent');
            console.log('Sending:', message);
            input.value = '';
        }
    }

    function addChatMessage(message, type) {
        const div = document.createElement('div');
        div.className = `chat-message ${type}`;
        div.textContent = message;
        document.getElementById('chat-messages').appendChild(div);
    }

    function handleChatKeypress(event) {
        if (event.key === 'Enter') sendMessage();
    }

    async function endCall() {
        try {
            await fetch('/video/end-call', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ session_id: sessionId })
            });
        } catch (error) {
            console.error('Error ending call:', error);
        }
        
        localTracks.forEach(track => track.close());
        if (client) await client.leave();
        
        location.reload();
    }
</script>
@endpush
