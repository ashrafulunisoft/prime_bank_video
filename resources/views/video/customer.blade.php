    @extends('layouts.receptionist')

@section('title', 'Video Call - Customer Support')

@push('styles')
<script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.22.0.js" onerror="console.error('Failed to load Agora SDK')"></script>
<script>
    window.checkAgoraLoaded = function() {
        if (typeof AgoraRTC === 'undefined') {
            console.error('AgoraRTC is not loaded!');
            return false;
        }
        console.log('AgoraRTC loaded successfully');
        return true;
    };
</script>
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
    
    .queue-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
    }
    
    .queue-position {
        font-size: 72px;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Queue Status -->
    <div id="queue-status" class="queue-info mb-4" style="display: none;">
        <h3>Please wait...</h3>
        <p>You are in the queue</p>
        <div class="queue-position" id="queue-position">#0</div>
        <p class="mt-3">An agent will be with you shortly</p>
        <button class="btn btn-light mt-3" onclick="cancelQueue()">Cancel Request</button>
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
                        <span class="video-label" id="remote-label">Waiting for agent...</span>
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
                        <i class="fas fa-comments"></i> Chat
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

    <!-- Request Button -->
    <div id="request-section" class="text-center py-5">
        <h2><i class="fas fa-video"></i> Video Call Support</h2>
        <p class="text-white">Connect with a customer care representative via video call</p>
        <button class="btn btn-primary btn-lg px-5" onclick="requestCall()">
            <i class="fas fa-phone-alt"></i> Start Video Call
        </button>
   </div>
@endsection

@push('scripts')
<script>
    // State
    let client = null;
    let localTracks = [];
    let remoteTracks = {};
    let channel = null;
    let uid = null;
    let isMuted = false;
    let isCameraOff = false;
    let isScreenSharing = false;
    let sessionId = null;
    let queueId = null;
    let callTimer = null;

    // Initialize
    async function initAgora() {
        const agoraAppId = '{{ config("services.agora.app_id") }}';
        client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });
        
        client.on('user-published', async (user, mediaType) => {
            await client.subscribe(user, mediaType);
            if (mediaType === 'video') {
                remoteTracks[user.uid] = user.videoTrack;
                user.videoTrack.play('remote-video');
                document.getElementById('remote-label').textContent = 'Agent';
            }
            if (mediaType === 'audio') {
                remoteTracks[user.uid] = user.audioTrack;
                user.audioTrack.play();
            }
        });

        client.on('user-unpublished', (user) => {
            delete remoteTracks[user.uid];
        });

        client.on('user-left', (user) => {
            delete remoteTracks[user.uid];
            showFeedback();
        });
    }

    // Request Call
    async function requestCall() {
        // Check if Agora SDK is loaded
        if (typeof AgoraRTC === 'undefined') {
            alert('Video call SDK not loaded. Please refresh the page or check your internet connection.');
            console.error('AgoraRTC is undefined. SDK failed to load from CDN.');
            return;
        }
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            console.log('Requesting call... CSRF token exists:', !!csrfToken);
            
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }
            
            const response = await fetch('/video/request-call', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            const responseText = await response.text();
            console.log('Response text preview:', responseText.substring(0, 500));
            
            // Check if response is HTML (error page)
            if (responseText.trim().startsWith('<!DOCTYPE') || responseText.trim().startsWith('<html')) {
                alert('Server returned HTML error page (Status: ' + response.status + '). Check console for details.');
                console.error('Full HTML response:', responseText);
                return;
            }
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                alert('Server returned invalid JSON. Check console for details.');
                console.error('JSON parse error:', e);
                console.error('Response text:', responseText);
                return;
            }
            
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            if (data.type === 'redirect') {
                // Redirect to OTP verification page
                window.location.href = data.redirect_url;
            } else if (data.type === 'connect') {
                await joinChannel(data);
            } else if (data.type === 'queue') {
                queueId = data.queue_id;
                showQueueStatus(data.position);
                startQueuePolling();
            } else {
                alert('Unexpected response: ' + JSON.stringify(data));
            }
        } catch (error) {
            console.error('Error requesting call:', error);
            alert('Failed to request call: ' + error.message);
        }
    }

    // Join Channel
    async function joinChannel(data) {
        console.log('Joining channel with data:', data);
        console.log('App ID:', data.app_id);
        console.log('Channel:', data.channel);
        console.log('UID:', data.uid);
        console.log('Token:', data.token ? 'present' : 'missing');
        
        await initAgora();
        
        channel = data.channel;
        uid = data.uid;
        sessionId = data.session_id;
        
        if (!data.app_id) {
            alert('Error: App ID is missing from server response');
            console.error('Missing app_id in data:', data);
            return;
        }
        
        await client.join(data.app_id, channel, data.token, uid);
        
        // Create local tracks
        localTracks = await AgoraRTC.createMicrophoneAndCameraTracks();
        
        // Play local video
        localTracks[1].play('local-video');
        
        // Publish tracks
        await client.publish(localTracks);
        
        // Show call interface
        document.getElementById('request-section').style.display = 'none';
        document.getElementById('call-interface').style.display = 'block';
        
        // Start timer
        startCallTimer();
    }

    // Queue Polling
    function startQueuePolling() {
        setInterval(async () => {
            try {
                const response = await fetch(`/video/queue-status?queue_id=${queueId}`);
                const data = await response.json();
                
                if (data.type === 'connect') {
                    await joinChannel(data);
                    stopQueuePolling();
                }
            } catch (error) {
                console.error('Queue check error:', error);
            }
        }, 3000);
    }

    let queuePollingInterval = null;
    function stopQueuePolling() {
        if (queuePollingInterval) {
            clearInterval(queuePollingInterval);
            queuePollingInterval = null;
        }
    }

    // Show Queue Status
    function showQueueStatus(position) {
        document.getElementById('request-section').style.display = 'none';
        document.getElementById('queue-status').style.display = 'block';
        document.getElementById('queue-position').textContent = `#${position}`;
    }

    // Cancel Queue
    async function cancelQueue() {
        try {
            await fetch('/video/cancel-queue', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            document.getElementById('queue-status').style.display = 'none';
            document.getElementById('request-section').style.display = 'block';
        } catch (error) {
            console.error('Error canceling queue:', error);
        }
    }

    // Toggle Mic
    function toggleMic() {
        if (localTracks[0]) {
            if (isMuted) {
                localTracks[0].setEnabled(true);
                document.getElementById('mic-btn').classList.add('active');
            } else {
                localTracks[0].setEnabled(false);
                document.getElementById('mic-btn').classList.remove('active');
            }
            isMuted = !isMuted;
        }
    }

    // Toggle Camera
    function toggleCamera() {
        if (localTracks[1]) {
            if (isCameraOff) {
                localTracks[1].setEnabled(true);
                document.getElementById('camera-btn').classList.add('active');
            } else {
                localTracks[1].setEnabled(false);
                document.getElementById('camera-btn').classList.remove('active');
            }
            isCameraOff = !isCameraOff;
        }
    }

    // Screen Share
    async function toggleScreenShare() {
        if (isScreenSharing) {
            // Stop screen share
            if (localTracks[2]) {
                localTracks[2].close();
                localTracks.pop();
                await client.unpublish(localTracks[2]);
            }
            
            // Re-publish camera
            if (localTracks[1]) {
                await client.publish(localTracks[1]);
            }
            
            // Play camera
            localTracks[1].play('local-video');
            document.getElementById('screen-btn').classList.remove('active');
            isScreenSharing = false;
        } else {
            try {
                // Create screen track
                const screenTrack = await AgoraRTC.createScreenVideoTrack();
                
                // Unpublish camera
                await client.unpublish(localTracks[1]);
                
                // Publish screen
                await client.publish(screenTrack);
                
                // Play screen
                screenTrack.play('local-video');
                localTracks.push(screenTrack);
                
                document.getElementById('screen-btn').classList.add('active');
                isScreenSharing = true;
            } catch (error) {
                console.error('Screen share error:', error);
            }
        }
    }

    // Send Chat Message
    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        
        if (message) {
            // Add to UI
            addChatMessage(message, 'sent');
            
            // Send via RTM (placeholder - implement with Agora RTM)
            console.log('Sending message:', message);
            
            input.value = '';
        }
    }

    function addChatMessage(message, type) {
        const messagesDiv = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;
        messageDiv.textContent = message;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function handleChatKeypress(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    }

    // End Call
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
            
            // Cleanup
            localTracks.forEach(track => track.close());
            if (channel) {
                await client.leave();
            }
            
            showFeedback();
        } catch (error) {
            console.error('Error ending call:', error);
        }
    }

    // Show Feedback
    function showFeedback() {
        if (callTimer) {
            clearInterval(callTimer);
        }
        
        window.location.href = `/video/feedback?session_id=${sessionId}`;
    }

    // Call Timer
    function startCallTimer() {
        let seconds = 0;
        callTimer = setInterval(() => {
            seconds++;
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            console.log(`Call duration: ${hours}:${minutes}:${secs}`);
        }, 1000);
    }
</script>
@endpush
