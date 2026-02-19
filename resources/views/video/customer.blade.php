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
    
    .fullscreen-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: rgba(0,0,0,0.7);
        color: white;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .fullscreen-btn:hover {
        background: rgba(40, 167, 69, 0.9);
        transform: scale(1.1);
    }
    
    .exit-fullscreen-btn {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        cursor: pointer;
        font-size: 24px;
        z-index: 10000;
        display: none;
        transition: all 0.3s;
    }
    
    .exit-fullscreen-btn.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .exit-fullscreen-btn:hover {
        background: #dc3545;
        transform: scale(1.1);
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
                    <div class="video-wrapper" id="local-video-wrapper">
                        <button class="fullscreen-btn" onclick="toggleFullscreen('local-video-wrapper')" title="Full screen">
                            <i class="fas fa-expand"></i>
                        </button>
                        <div id="local-video" style="width: 100%; height: 100%;"></div>
                        <span class="video-label">You</span>
                    </div>
                    <div class="video-wrapper" id="remote-video-wrapper">
                        <button class="fullscreen-btn" onclick="toggleFullscreen('remote-video-wrapper')" title="Full screen">
                            <i class="fas fa-expand"></i>
                        </button>
                        <div id="remote-video" style="width: 100%; height: 100%;"></div>
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
                
                <!-- Exit Fullscreen Button -->
                <button class="exit-fullscreen-btn" id="exit-fullscreen-btn" onclick="exitFullscreen()" title="Exit Fullscreen">
                    <i class="fas fa-compress"></i>
                </button>
            </div>
            
            <div class="col-lg-3">
                <div class="chat-panel" style="height: calc(100vh - 200px);">
                    <div class="chat-header">
                        <i class="fas fa-comments"></i> Chat
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
    let queueId = null;
    let callTimer = null;
    let queuePollingInterval = null;

    // Initialize Agora Client
    async function initAgora() {
        const agoraAppId = '{{ config("services.agora.app_id") }}';
        client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });
        
        // Handle remote user publishing (video/audio/screen)
        client.on('user-published', async (user, mediaType) => {
            console.log('Remote user published:', user.uid, mediaType);
            
            await client.subscribe(user, mediaType);
            
            if (mediaType === 'video') {
                remoteTracks[user.uid] = user.videoTrack;
                user.videoTrack.play('remote-video');
                document.getElementById('remote-label').textContent = 'Agent (Screen or Video)';
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

        client.on('user-left', (user) => {
            console.log('Remote user left:', user.uid);
            delete remoteTracks[user.uid];
            showFeedback();
        });
    }

    // Request Call
    async function requestCall() {
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
        
        await initAgora();
        
        channel = data.channel;
        uid = data.uid;
        sessionId = data.session_id;
        
        if (!data.app_id) {
            alert('Error: App ID is missing from server response');
            console.error('Missing app_id in data:', data);
            return;
        }
        
        // Join the channel
        await client.join(data.app_id, channel, data.token, uid);
        
        // Create local audio and video tracks
        [localAudioTrack, localVideoTrack] = await AgoraRTC.createMicrophoneAndCameraTracks();
        
        // Play local video
        localVideoTrack.play('local-video');
        
        // Publish audio and video tracks
        await client.publish([localAudioTrack, localVideoTrack]);
        
        console.log('Published local tracks');
        
        // Show call interface and hide request/queue sections
        document.getElementById('request-section').style.display = 'none';
        document.getElementById('queue-status').style.display = 'none';
        document.getElementById('call-interface').style.display = 'block';

        // Load existing chat messages
        await loadChatMessages();

        // Start polling for new messages
        startChatPolling();

        // Start timer
        startCallTimer();
    }

    // Queue Polling
    function startQueuePolling() {
        queuePollingInterval = setInterval(async () => {
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
        if (localAudioTrack) {
            if (isMuted) {
                localAudioTrack.setEnabled(true);
                document.getElementById('mic-btn').classList.add('active');
            } else {
                localAudioTrack.setEnabled(false);
                document.getElementById('mic-btn').classList.remove('active');
            }
            isMuted = !isMuted;
        }
    }

    // Toggle Camera
    function toggleCamera() {
        if (localVideoTrack) {
            if (isCameraOff) {
                localVideoTrack.setEnabled(true);
                document.getElementById('camera-btn').classList.add('active');
            } else {
                localVideoTrack.setEnabled(false);
                document.getElementById('camera-btn').classList.remove('active');
            }
            isCameraOff = !isCameraOff;
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

    // Get Chat Messages
    async function loadChatMessages() {
        if (!sessionId) return;

        try {
            const response = await fetch(`/video/chat/messages?session_id=${sessionId}`);
            const data = await response.json();

            if (data.success) {
                const messagesDiv = document.getElementById('chat-messages');
                messagesDiv.innerHTML = '';

                data.messages.forEach(msg => {
                    const type = msg.sender_type === 'customer' ? 'sent' : 'received';
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
                            const type = msg.sender_type === 'customer' ? 'sent' : 'received';
                            addChatMessage(msg.message, type, msg.sender_name);
                        });
                    }
                }
            } catch (error) {
                console.error('Error polling chat:', error);
            }
        }, 2000);
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
        } catch (error) {
            console.error('Error ending call:', error);
        }
        
        // Exit fullscreen if active
        exitFullscreen();
        
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
        
        showFeedback();
    }
    
    // Fullscreen Functions
    let isFullscreen = false;
    let fullscreenTarget = null;
    
    function toggleFullscreen(elementId) {
        const element = document.getElementById(elementId);
        const exitBtn = document.getElementById('exit-fullscreen-btn');
        
        if (!isFullscreen) {
            // Enter fullscreen
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
            
            isFullscreen = true;
            fullscreenTarget = elementId;
            exitBtn.classList.add('active');
        }
    }
    
    function exitFullscreen() {
        const exitBtn = document.getElementById('exit-fullscreen-btn');
        
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        
        isFullscreen = false;
        fullscreenTarget = null;
        exitBtn.classList.remove('active');
    }
    
    // Listen for fullscreen change events
    document.addEventListener('fullscreenchange', function() {
        const exitBtn = document.getElementById('exit-fullscreen-btn');
        if (!document.fullscreenElement) {
            isFullscreen = false;
            fullscreenTarget = null;
            exitBtn.classList.remove('active');
        }
    });
    
    document.addEventListener('webkitfullscreenchange', function() {
        const exitBtn = document.getElementById('exit-fullscreen-btn');
        if (!document.webkitFullscreenElement) {
            isFullscreen = false;
            fullscreenTarget = null;
            exitBtn.classList.remove('active');
        }
    });

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
</script>
@endpush
