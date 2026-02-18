# Video Calling Feature - Complete Task List
## Prime Bank Video System

**Created:** February 17, 2026
**Status:** Planning Phase
**Technology Stack:** Laravel 12, Livewire, Agora SDK

---

## 📋 Executive Summary

Implement a real-time video calling feature connecting customers with bank agents for service inquiries, account support, and virtual consultations using Agora RTC (Real-Time Communication) SDK.

---

## 🎯 Phase 1: Database & Backend Setup

### Task 1.1: Database Schema Design
- [ ] **Create video_calls table**
  ```sql
  CREATE TABLE video_calls (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      customer_id BIGINT UNSIGNED NOT NULL,
      agent_id BIGINT UNSIGNED NULL,
      queue_position INT DEFAULT 0,
      status ENUM('pending', 'in_queue', 'calling', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
      channel_name VARCHAR(255) UNIQUE NOT NULL,
      token VARCHAR(500) NULL,
      started_at TIMESTAMP NULL,
      ended_at TIMESTAMP NULL,
      duration INT DEFAULT 0 COMMENT 'Duration in seconds',
      customer_rating TINYINT NULL COMMENT '1-5 stars',
      customer_feedback TEXT NULL,
      notes TEXT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_customer (customer_id),
      INDEX idx_agent (agent_id),
      INDEX idx_status (status),
      INDEX idx_channel (channel_name)
  );
  ```

- [ ] **Create video_queue table**
  ```sql
  CREATE TABLE video_queue (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      call_id BIGINT UNSIGNED NOT NULL,
      customer_id BIGINT UNSIGNED NOT NULL,
      priority INT DEFAULT 5 COMMENT '1=high, 5=low',
      estimated_wait_time INT DEFAULT 0 COMMENT 'seconds',
      joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      called_at TIMESTAMP NULL,
      INDEX idx_priority (priority),
      INDEX idx_customer (customer_id),
      FOREIGN KEY (call_id) REFERENCES video_calls(id) ON DELETE CASCADE
  );
  ```

- [ ] **Create video_call_logs table**
  ```sql
  CREATE TABLE video_call_logs (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      call_id BIGINT UNSIGNED NOT NULL,
      event_type ENUM('join', 'leave', 'mute', 'unmute', 'screen_share', 'error', 'reconnect') NOT NULL,
      user_id BIGINT UNSIGNED NOT NULL,
      user_type ENUM('customer', 'agent') NOT NULL,
      metadata JSON NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (call_id) REFERENCES video_calls(id) ON DELETE CASCADE
  );
  ```

- [ ] **Add columns to users table**
  - `is_available_for_video` BOOLEAN DEFAULT TRUE
  - `video_calls_handled` INT DEFAULT 0
  - `average_rating` DECIMAL(3,2) DEFAULT NULL

### Task 1.2: Laravel Models & Migrations
- [ ] **Create VideoCall Model** (`app/Models/VideoCall.php`)
  ```php
  namespace App\Models;
  
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  
  class VideoCall extends Model
  {
      protected $fillable = [
          'customer_id', 'agent_id', 'queue_position', 'status',
          'channel_name', 'token', 'started_at', 'ended_at',
          'duration', 'customer_rating', 'customer_feedback', 'notes'
      ];
      
      protected $casts = [
          'started_at' => 'datetime',
          'ended_at' => 'datetime',
          'duration' => 'integer',
          'customer_rating' => 'integer'
      ];
      
      // Relationships
      public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
      public function agent(): BelongsTo { return $this->belongsTo(User::class, 'agent_id'); }
      public function logs(): HasMany { return $this->hasMany(VideoCallLog::class); }
      
      // Scopes
      public function scopePending($query) { return $query->where('status', 'pending'); }
      public function scopeInProgress($query) { return $query->where('status', 'in_progress'); }
      public function scopeCompleted($query) { return $query->where('status', 'completed'); }
  }
  ```

- [ ] **Create VideoQueue Model** (`app/Models/VideoQueue.php`)
- [ ] **Create VideoCallLog Model** (`app/Models/VideoCallLog.php`)
- [ ] **Run migrations**

### Task 1.3: Agora Token Service
- [ ] **Create AgoraService** (`app/Services/AgoraService.php`)
  ```php
  namespace App\Services;
  
  use Monyxie\Agora\Token\RtcTokenBuilder;
  
  class AgoraService
  {
      private string $appId;
      private string $appCertificate;
      private const EXPIRATION_TIME_IN_SECONDS = 3600; // 1 hour
      
      public function __construct()
      {
          $this->appId = config('services.agora.app_id');
          $this->appCertificate = config('services.agora.app_certificate');
      }
      
      public function generateToken(string $channelName, int $uid, string $role): string
      {
          $privileges = [
              RtcTokenBuilder::PRIVILEGE_JOIN_CHANNEL => 0,
              RtcTokenBuilder::PRIVILEGE_PUBLISH_AUDIO_STREAM => 0,
              RtcTokenBuilder::PRIVILEGE_PUBLISH_VIDEO_STREAM => 0,
          ];
          
          return RtcTokenBuilder::buildTokenWithUid(
              $this->appId,
              $this->appCertificate,
              $channelName,
              $uid,
              $role === 'publisher' ? RtcTokenBuilder::ROLE_PUBLISHER : RtcTokenBuilder::ROLE_SUBSCRIBER,
              self::EXPIRATION_TIME_IN_SECONDS,
              $privileges
          );
      }
      
      public function generateChannelName(int $customerId): string
      {
          return 'video_call_' . $customerId . '_' . time();
      }
  }
  ```

- [ ] **Add Agora config to services.php**
- [ ] **Add keys to .env**
  ```
  AGORA_APP_ID=your_app_id
  AGORA_APP_CERTIFICATE=your_app_certificate
  ```

---

## 🎯 Phase 2: VideoCallController Implementation

### Task 2.1: Customer Endpoints
- [ ] **customerCall()** - Display video call interface for customer
  - Check if user has pending/in-progress call
  - Generate Agora token
  - Return view with channel name and token
  
- [ ] **customerRequestCall()** - Join video call queue
  - Validate request
  - Create video call record with 'pending' status
  - Add to queue with priority
  - Return queue position
  
- [ ] **queueStatus()** - Check queue position
  - Return estimated wait time
  - Return queue position
  
- [ ] **cancelQueue()** - Cancel video call request
  - Update status to 'cancelled'
  - Remove from queue
  
- [ ] **endCall()** - Customer ends call
  - Update call duration
  - Redirect to feedback form
  
- [ ] **feedback()** - Display feedback form
- [ ] **submitFeedback()** - Save customer feedback

### Task 2.2: Agent Endpoints
- [ ] **agentDashboard()** - Agent video call dashboard
  - Show waiting customers queue
  - Show agent's status (available/busy)
  - Display call statistics
  
- [ ] **agentStatus()** - Update agent availability
  - Toggle available/busy status
  
- [ ] **agentStartCall()** - Agent accepts customer call
  - Update call status to 'in_progress'
  - Assign agent to call
  - Generate agent token

### Task 2.3: Admin Endpoints
- [ ] **adminDashboard()** - Admin video call monitoring
  - Real-time statistics
  - Active calls overview
  - Queue status
  
- [ ] **apiStats()** - Return video call statistics
  - Total calls today
  - Average wait time
  - Average call duration
  - Customer satisfaction score

---

## 🎯 Phase 3: Frontend - Customer Interface

### Task 3.1: Video Call Page (Livewire Component)
- [ ] **Create CustomerVideoCall Component** (`app/Livewire/Video/CustomerVideoCall.php`)
  ```php
  namespace App\Livewire\Video;
  
  use Livewire\Component;
  use App\Models\VideoCall;
  use App\Services\AgoraService;
  
  class CustomerVideoCall extends Component
  {
      public $channelName;
      public $token;
      public $callStatus;
      public $queuePosition;
      public $isConnecting = false;
      public $isConnected = false;
      public $isMuted = false;
      public $isVideoOff = false;
      
      protected $listeners = ['callEnded', 'agentJoined'];
      
      public function mount()
      {
          $this->initializeCall();
      }
      
      public function initializeCall()
      {
          // Get or create call, generate token
      }
      
      public function joinChannel()
      {
          // Initialize Agora SDK client
      }
      
      public function toggleMute()
      {
          $this->isMuted = !$this->isMuted;
      }
      
      public function toggleVideo()
      {
          $this->isVideoOff = !$this->isVideoOff;
      }
      
      public function endCall()
      {
          // Call API to end call
      }
      
      public function render()
      {
          return view('livewire.video.customer-video-call');
      }
  }
  ```

### Task 3.2: Video Call View
- [ ] **Create view** (`resources/views/livewire/video/customer-video-call.blade.php`)
  - Video container for local and remote streams
  - Controls: Mute, Video, End Call, Screen Share
  - Status indicator (connecting, waiting for agent, connected)
  - Queue position display

### Task 3.3: Queue Waiting Screen
- [ ] **Create WaitingScreen Component**
  - Animated waiting indicator
  - Estimated wait time
  - Queue position
  - Cancel queue button
  - Tips/info display

---

## 🎯 Phase 4: Frontend - Agent Interface

### Task 4.1: Agent Dashboard (Livewire)
- [ ] **Create AgentVideoDashboard Component** (`app/Livewire/Video/AgentDashboard.php`)
  ```php
  namespace App\Livewire\Video;
  
  use Livewire\Component;
  use Livewire\Attributes\On;
  
  class AgentDashboard extends Component
  {
      public $isAvailable = true;
      public $currentCall = null;
      public $waitingCustomers = [];
      public $callHistory = [];
      public $todayStats = [];
      
      #[On('customer-joined-queue')]
      public function refreshQueue() { /* ... */ }
      
      public function toggleAvailability()
      {
          $this->isAvailable = !$this->isAvailable;
          auth()->user()->update(['is_available_for_video' => $this->isAvailable]);
      }
      
      public function acceptCall($callId)
      {
          // Start accepting the call
      }
      
      public function endCall()
      {
          // End current call
      }
      
      public function render()
      {
          return view('livewire.video.agent-dashboard');
      }
  }
  ```

### Task 4.2: Agent Dashboard View
- [ ] **Create view** (`resources/views/livewire/video/agent-dashboard.blade.php`)
  - Sidebar: Queue of waiting customers
  - Main area: Video call interface
  - Right panel: Customer info, quick actions, call notes
  - Status toggle button
  - Call history preview

### Task 4.3: Agent Video Controls
- [ ] **Mute/Unmute button**
- [ ] **Camera on/off toggle**
- [ ] **Screen share button**
- [ ] **Transfer call option**
- [ ] **Add notes during call**
- [ ] **End call button with reason dropdown**

---

## 🎯 Phase 5: Real-time Features

### Task 5.1: Queue Management (WebSocket/Pusher)
- [ ] **Create VideoQueueEvents** (`app/Events/VideoQueueEvents.php`)
  ```php
  namespace App\Events;
  
  use Illuminate\Broadcasting\Channel;
  use Illuminate\Queue\SerializesModels;
  use Illuminate\Broadcasting\PrivateChannel;
  use Illuminate\Broadcasting\PresenceChannel;
  use Illuminate\Foundation\Events\Dispatchable;
  use Illuminate\Broadcasting\InteractsWithSockets;
  
  class CustomerJoinedQueue
  {
      use Dispatchable, InteractsWithSockets, SerializesModels;
      
      public $call;
      
      public function __construct($call)
      {
          $this->call = $call;
      }
      
      public function broadcastOn()
      {
          return new Channel('video-queue');
      }
  }
  ```

- [ ] **Create events:**
  - `CustomerJoinedQueue` - When customer joins queue
  - `AgentAssignedToCall` - When agent accepts call
  - `CallEnded` - When call ends
  - `QueuePositionUpdated` - When queue position changes

### Task 5.2: Real-time Updates
- [ ] **Configure Pusher/Laravel Reverb**
  ```env
  BROADCAST_DRIVER=pusher
  PUSHER_APP_ID=your_app_id
  PUSHER_KEY=your_key
  PUSHER_SECRET=your_secret
  PUSHER_CLUSTER=mt1
  ```
  
- [ ] **Frontend JavaScript for real-time**
  - Listen for queue events
  - Update UI dynamically
  - Show notifications

### Task 5.3: Laravel Reverb (Optional)
- [ ] **Set up Laravel Reverb for WebSocket**
  ```env
  REVERB_APP_ID=your_app_id
  REVERB_APP_KEY=your_key
  REVERB_APP_SECRET=your_secret
  REVERB_HOST="127.0.0.1"
  REVERB_PORT=8080
  ```

---

## 🎯 Phase 6: Frontend JavaScript (Agora SDK)

### Task 6.1: Agora Client Setup
- [ ] **Create agora-client.js** (`resources/js/agora-client.js`)
  ```javascript
  import AgoraRTC from 'agora-rtc-sdk-ng';
  
  class AgoraClient {
      constructor() {
          this.client = null;
          this.localTracks = {
              audioTrack: null,
              videoTrack: null
          };
          this.remoteTracks = {};
      }
      
      async initialize(appId, channel, token, uid) {
          this.client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });
          
          await this.client.join(appId, channel, token, uid);
          
          this.localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
          this.localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();
          
          await this.client.publish([this.localTracks.audioTrack, this.localTracks.videoTrack]);
          
          this.client.on('user-published', this.handleUserPublished.bind(this));
          this.client.on('user-unpublished', this.handleUserUnpublished.bind(this));
      }
      
      async handleUserPublished(user, mediaType) {
          const track = await this.client.subscribe(user, mediaType);
          if (mediaType === 'video') {
              this.remoteTracks[user.uid] = track;
              this.renderRemoteVideo(user.uid, track);
          }
      }
      
      async toggleMute() {
          if (this.localTracks.audioTrack) {
              await this.localTracks.audioTrack.setEnabled(!enabled);
          }
      }
      
      async toggleVideo() {
          if (this.localTracks.videoTrack) {
              await this.localTracks.videoTrack.setEnabled(!enabled);
          }
      }
      
      async startScreenShare() {
          // Screen share implementation
      }
      
      async leave() {
          if (this.localTracks.audioTrack) {
              this.localTracks.audioTrack.close();
          }
          if (this.localTracks.videoTrack) {
              this.localTracks.videoTrack.close();
          }
          await this.client.leave();
      }
  }
  
  export default new AgoraClient();
  ```

### Task 6.2: Video Component Blade Directives
- [ ] **Register Agora SDK in vite.config.js**
  ```js
  import { defineConfig } from 'vite';
  import laravel from 'laravel-vite-plugin';
  
  export default defineConfig({
      plugins: [
          laravel({
              input: [
                  'resources/css/app.css',
                  'resources/js/app.js',
                  'resources/js/agora-client.js',
              ],
              refresh: true,
          }),
      ],
  });
  ```

---

## 🎯 Phase 7: Admin Monitoring Dashboard

### Task 7.1: Admin Video Call Statistics
- [ ] **Create VideoStats Component**
  - Total calls today/week/month
  - Average wait time
  - Average call duration
  - Customer satisfaction score
  - Agent performance metrics

### Task 7.2: Live Monitoring View
- [ ] **Create admin video monitoring page**
  - Active calls grid
  - Real-time queue status
  - Agent availability status
  - Quick actions (force end call, reassign agent)

---

## 🎯 Phase 8: Testing & Quality Assurance

### Task 8.1: Unit Tests
- [ ] **Test VideoCall Model**
  ```php
  test('can create video call', function () {
      $call = VideoCall::create([
          'customer_id' => 1,
          'channel_name' => 'test_channel',
          'status' => 'pending'
      ]);
      expect($call->status)->toBe('pending');
  });
  ```

- [ ] **Test AgoraService**
  - Token generation
  - Channel name generation

- [ ] **Test VideoCallController**
  - Customer request call
  - Queue status
  - End call logic

### Task 8.2: Feature Tests
- [ ] **Test customer video call flow**
  - Request call → Join queue → Connect → End call → Submit feedback
  
- [ ] **Test agent workflow**
  - Set available → Accept call → Handle call → End call
  
- [ ] **Test admin monitoring**

### Task 8.3: Integration Tests
- [ ] **Test Agora SDK integration**
- [ ] **Test real-time events**
- [ ] **Test concurrent calls**

---

## 🎯 Phase 9: Documentation

### Task 9.1: Technical Documentation
- [ ] **API Documentation**
  - Endpoints with request/response examples
  - Authentication requirements
  
- [ ] **Database Schema Documentation**
- [ ] **Architecture Overview**
- [ ] **Deployment Guide**

### Task 9.2: User Documentation
- [ ] **Customer User Guide**
- [ ] **Agent User Guide**
- [ ] **Admin Guide**

---

## ✅ Success Metrics (KPIs)

### Performance Metrics
| Metric | Target | Measurement |
|--------|--------|-------------|
| Call Connection Time | < 5 seconds | From request to agent join |
| Queue Wait Time (Avg) | < 3 minutes | Time in queue before agent connects |
| Video Quality Score | > 4.0/5.0 | Based on bitrate, latency, packet loss |
| Call Drop Rate | < 2% | Unintentional disconnects |
| CPU Usage (Client) | < 30% | During video call |

### Business Metrics
| Metric | Target | Measurement |
|--------|--------|-------------|
| Customer Satisfaction | > 4.5/5 | Post-call feedback rating |
| Agent Utilization | 70-80% | Time spent on calls vs available |
| Calls per Day | 100+ | Peak system capacity |
| Resolution Rate | > 90% | Calls resolved in first interaction |

### Technical Metrics
| Metric | Target | Measurement |
|--------|--------|-------------|
| Uptime | 99.9% | System availability |
| Latency | < 200ms | End-to-end delay |
| Token Generation Time | < 100ms | Backend token creation |
| Queue Update Latency | < 1 second | Real-time queue sync |

---

## 📦 Deliverables Checklist

### Code Deliverables
- [ ] Database migrations
- [ ] Laravel models (VideoCall, VideoQueue, VideoCallLog)
- [ ] AgoraService
- [ ] VideoCallController with all endpoints
- [ ] Livewire components (Customer, Agent, Admin)
- [ ] Blade views
- [ ] JavaScript modules (Agora client)
- [ ] WebSocket events
- [ ] Unit tests
- [ ] Feature tests
- [ ] API documentation

### Configuration Deliverables
- [ ] Environment variables (.env)
- [ ] Broadcast configuration
- [ ] Agora SDK configuration
- [ ] Role-based permissions

### Documentation Deliverables
- [ ] API documentation
- [ ] Deployment guide
- [ ] User guides (Customer, Agent, Admin)

---

## 🚀 Deployment Steps

1. **Prepare Environment**
   ```bash
   cp .env.example .env
   # Add AGORA_APP_ID and AGORA_APP_CERTIFICATE
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```

3. **Install Dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

4. **Configure Broadcasting**
   ```bash
   php artisan broadcast:install
   ```

5. **Start Queue Worker**
   ```bash
   php artisan queue:work
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

---

## 📞 Support & Troubleshooting

### Common Issues
1. **Token generation failure** - Check Agora credentials in .env
2. **Video not connecting** - Verify firewall allows UDP ports 3478, 443
3. **Queue not updating** - Check WebSocket connection
4. **Poor video quality** - Check bandwidth requirements

### Log Locations
- Laravel: `storage/logs/laravel.log`
- Nginx/Apache error logs
- Agora Dashboard for RTC metrics

---

**Document Version:** 1.0
**Last Updated:** February 17, 2026
**Next Review:** February 24, 2026
