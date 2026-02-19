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
            <div class="stat-value">{{ $todayCalls }}</div>
            <div class="stat-label">Calls Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ gmdate('H:i:s', $todayDuration) }}</div>
            <div class="stat-label">Total Duration</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="queue-count">{{ $pendingQueue }}</div>
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
                        <div id="local-video" style="width: 100%; height: 100%;"></div>
                        <span class="video-label">You</span>
                    </div>
                    <div class="video-wrapper">
                        <div id="remote-video" style="width: 100%; height: 100%;"></div>
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
                        <input type="file" id="file-input" style="display:none" onchange="handleFileSelect(this)">
                        <button class="btn btn-outline-secondary rounded-circle" onclick="document.getElementById('file-input').click()" title="Attach file" style="padding: 10px;">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="text" id="chat-input" placeholder="Type a message..." onkeypress="handleChatKeypress(event)">
                        <button class="btn btn-primary rounded-circle" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <!-- File preview -->
                    <div id="file-preview" style="display:none; padding: 10px 15px; border-top: 1px solid #e9ecef; background: #f8f9fa;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file mr-2"></i>
                                <span id="file-name" style="font-size: 13px;"></span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary mr-1" id="upload-btn" onclick="uploadFile()">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="clearFile()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // State - Using separate variables for proper track management
    let client = null;
    let localAudioTrack = null;
    let localVideoTrack = null;
    let localScreenTrack = null;
    let remoteTracks = {};
    let channel = null;
    let uid = null;
    let isMuted = false;
    let isCameraOff = false;
    let isScreenSharing = false;
    let sessionId = null;
    let queuePollingInterval = null;

    // Start queue status polling
    function startQueuePolling() {
        queuePollingInterval = setInterval(async () => {
            try {
                const response = await fetch('/video/agent/queue-status');
                const data = await response.json();
                
                if (data.pending_queue !== undefined) {
                    const queueCountElement = document.getElementById('queue-count');
                    if (queueCountElement) {
                        queueCountElement.textContent = data.pending_queue;
                    }
                }
            } catch (error) {
                console.error('Error polling queue status:', error);
            }
        }, 3000);
    }

    // Start polling when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startQueuePolling();
    });

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
        
        // Create local audio and video tracks
        [localAudioTrack, localVideoTrack] = await AgoraRTC.createMicrophoneAndCameraTracks();
        localVideoTrack.play('local-video');
        
        await client.publish([localAudioTrack, localVideoTrack]);
        
        console.log('Agent published local tracks');
        
        document.getElementById('next-call-section').style.display = 'none';
        document.getElementById('call-interface').style.display = 'block';
        document.getElementById('remote-label').textContent = data.customer_name || 'Customer';

        // Load existing chat messages
        await loadChatMessages();

        // Start polling for new messages
        startChatPolling();
    }

    // Load Chat Messages
    async function loadChatMessages() {
        if (!sessionId) return;

        try {
            const response = await fetch(`/video/chat/messages?session_id=${sessionId}`);
            const data = await response.json();

            if (data.success) {
                const messagesDiv = document.getElementById('chat-messages');
                messagesDiv.innerHTML = '';

                data.messages.forEach(msg => {
                    const type = msg.sender_type === 'agent' ? 'sent' : 'received';
                    addChatMessage(msg.message, type, msg.sender_name);
                });
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    // Poll for new messages
    function startChatPolling() {
        setInterval(async () => {
            if (!sessionId) return;

            try {
                const response = await fetch(`/video/chat/messages?session_id=${sessionId}`);
                const data = await response.json();

                if (data.success) {
                    const messagesDiv = document.getElementById('chat-messages');
                    const currentMessages = messagesDiv.querySelectorAll('.chat-message');
                    const currentCount = currentMessages.length;

                    if (data.messages.length > currentCount) {
                        data.messages.slice(currentCount).forEach(msg => {
                            const type = msg.sender_type === 'agent' ? 'sent' : 'received';
                            addChatMessage(msg.message, type, msg.sender_name);
                        });
                    }
                }
            } catch (error) {
                console.error('Error polling chat:', error);
            }
        }, 2000);
    }

    // Send Chat Message
    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (message && sessionId) {
            try {
                const response = await fetch('/video/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        message: message
                    })
                });

                const data = await response.json();

                if (data.success) {
                    addChatMessage(message, 'sent', data.message.sender_name);
                    input.value = '';
                } else {
                    console.error('Failed to send message:', data.error);
                    alert('Failed to send message: ' + data.error);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error sending message: ' + error.message);
            }
        }
    }

    function addChatMessage(message, type, senderName = null) {
        const messagesDiv = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;

        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        // Convert URLs to clickable links
        const formattedMessage = escapeHtml(message).replace(
            /(https?:\/\/[^\s<]+)/g, 
            '<a href="$1" target="_blank" style="color: inherit; text-decoration: underline;">$1</a>'
        );

        if (senderName) {
            messageDiv.innerHTML = `<strong style="font-size: 11px; display: block; margin-bottom: 3px;">${senderName} • ${time}</strong>${formattedMessage}`;
        } else {
            messageDiv.innerHTML = `${formattedMessage}<span style="font-size: 10px; display: block; margin-top: 3px; opacity: 0.7;">${time}</span>`;
        }

        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function initAgora(appId) {
        client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });
        
        // Handle remote user publishing (video/audio/screen)
        client.on('user-published', async (user, mediaType) => {
            console.log('Remote user published:', user.uid, mediaType);
            
            await client.subscribe(user, mediaType);
            
            if (mediaType === 'video') {
                remoteTracks[user.uid] = user.videoTrack;
                user.videoTrack.play('remote-video');
                // Update label to indicate screen or video
                document.getElementById('remote-label').textContent = 'Customer (Video/Screen)';
            }
            if (mediaType === 'audio') {
                remoteTracks[user.uid] = user.audioTrack;
                user.audioTrack.play();
            }
        });

        client.on('user-unpublished', (user, mediaType) => {
            console.log('Remote user unpublished:', user.uid, mediaType);
            if (mediaType === 'video') {
                const remoteContainer = document.getElementById('remote-video');
                remoteContainer.innerHTML = '';
            }
            delete remoteTracks[user.uid];
        });

        client.on('user-left', async (user) => {
            console.log('Remote user left:', user.uid);
            delete remoteTracks[user.uid];
            await endCall();
        });
    }

    function toggleMic() {
        if (localAudioTrack) {
            isMuted = !isMuted;
            localAudioTrack.setEnabled(!isMuted);
            document.getElementById('mic-btn').classList.toggle('active', !isMuted);
        }
    }

    function toggleCamera() {
        if (localVideoTrack) {
            isCameraOff = !isCameraOff;
            localVideoTrack.setEnabled(!isCameraOff);
            document.getElementById('camera-btn').classList.toggle('active', !isCameraOff);
        }
    }

    // Screen Share - FIXED IMPLEMENTATION
    async function toggleScreenShare() {
        const screenBtn = document.getElementById('screen-btn');
        
        if (isScreenSharing) {
            // === STOP SCREEN SHARING ===
            try {
                // 1. Unpublish screen track first
                if (localScreenTrack) {
                    await client.unpublish(localScreenTrack);
                    localScreenTrack.close();
                    localScreenTrack = null;
                }
                
                // 2. Re-publish camera video track
                if (localVideoTrack) {
                    await client.publish(localVideoTrack);
                    localVideoTrack.play('local-video');
                }
                
                screenBtn.classList.remove('active');
                isScreenSharing = false;
                console.log('Screen sharing stopped');
            } catch (error) {
                console.error('Error stopping screen share:', error);
            }
        } else {
            // === START SCREEN SHARING ===
            try {
                // 1. Create screen video track
                localScreenTrack = await AgoraRTC.createScreenVideoTrack({
                    encoderConfig: '1080p_1',
                    optimizationMode: 'detail'
                }, 'auto');
                
                // Handle case where screenTrack might be an array (with audio)
                let screenVideoTrack = Array.isArray(localScreenTrack) ? localScreenTrack[0] : localScreenTrack;
                
                // 2. Unpublish camera video track
                if (localVideoTrack) {
                    await client.unpublish(localVideoTrack);
                }
                
                // 3. Publish screen track
                await client.publish(screenVideoTrack);
                
                // 4. Play screen locally
                screenVideoTrack.play('local-video');
                
                // 5. Handle "track-ended" event (user clicks browser's "Stop sharing" button)
                screenVideoTrack.on('track-ended', async () => {
                    console.log('Screen sharing ended by user from browser UI');
                    await stopScreenShareInternal();
                });
                
                // Update the reference if it's a single track
                if (!Array.isArray(localScreenTrack)) {
                    localScreenTrack = screenVideoTrack;
                }
                
                screenBtn.classList.add('active');
                isScreenSharing = true;
                console.log('Screen sharing started');
                
            } catch (error) {
                console.error('Screen share error:', error);
                alert('Screen sharing failed: ' + error.message);
                isScreenSharing = false;
                screenBtn.classList.remove('active');
                
                // Re-publish camera if screen share failed
                if (localVideoTrack) {
                    await client.publish(localVideoTrack);
                    localVideoTrack.play('local-video');
                }
            }
        }
    }

    // Internal function to stop screen share (called from track-ended event)
    async function stopScreenShareInternal() {
        const screenBtn = document.getElementById('screen-btn');
        
        try {
            if (localScreenTrack) {
                // Handle array case
                let trackToClose = Array.isArray(localScreenTrack) ? localScreenTrack[0] : localScreenTrack;
                await client.unpublish(trackToClose);
                trackToClose.close();
                localScreenTrack = null;
            }
            
            if (localVideoTrack) {
                await client.publish(localVideoTrack);
                localVideoTrack.play('local-video');
            }
            
            screenBtn.classList.remove('active');
            isScreenSharing = false;
            console.log('Screen sharing stopped (internal)');
        } catch (error) {
            console.error('Error in stopScreenShareInternal:', error);
        }
    }

    function handleChatKeypress(event) {
        if (event.key === 'Enter') sendMessage();
    }

    // File Upload Functions
    let selectedFile = null;

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        
        if (file.size > 10 * 1024 * 1024) {
            alert('File too large (max 10MB)');
            input.value = '';
            return;
        }
        
        selectedFile = file;
        document.getElementById('file-preview').style.display = 'block';
        document.getElementById('file-name').textContent = file.name;
    }

    function clearFile() {
        selectedFile = null;
        document.getElementById('file-input').value = '';
        document.getElementById('file-preview').style.display = 'none';
    }

    async function uploadFile() {
        if (!selectedFile || !sessionId) {
            alert('Please select a file');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('session_id', sessionId);
        
        const btn = document.getElementById('upload-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        btn.disabled = true;
        
        try {
            const response = await fetch('/video/chat/upload-file', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                addChatMessage(data.message.message, 'sent', data.message.sender_name);
                clearFile();
            } else {
                alert('Upload failed: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            alert('Upload error: ' + error.message);
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
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
        
        // Cleanup all tracks
        if (localAudioTrack) {
            localAudioTrack.close();
            localAudioTrack = null;
        }
        if (localVideoTrack) {
            localVideoTrack.close();
            localVideoTrack = null;
        }
        if (localScreenTrack) {
            localScreenTrack.close();
            localScreenTrack = null;
        }
        
        if (client) {
            await client.leave();
        }
        
        location.reload();
    }
</script>
@endpush
