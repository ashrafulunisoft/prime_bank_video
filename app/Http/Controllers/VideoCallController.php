<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\CallQueue;
use App\Models\CallSession;
use App\Models\CallFeedback;
use App\Models\CallMetric;
use App\Models\VideoCallOtp;
use App\Models\VideoCallChat;
use App\Services\AgoraService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VideoCallController extends Controller
{
    protected $agoraService;
    protected $smsService;

    public function __construct(AgoraService $agoraService, SmsNotificationService $smsService)
    {
        $this->agoraService = $agoraService;
        $this->smsService = $smsService;
    }

    /**
     * Show OTP verification page.
     */
    public function showOtpVerification()
    {
        $user = Auth::user();
        
        // Check if user has a phone number
        if (!$user->phone) {
            return redirect()->route('home')->with('error', 'Please update your phone number to use video call support.');
        }
        
        // Mask the phone number for display
        $maskedPhone = $this->maskPhoneNumber($user->phone);
        
        // Send OTP automatically when page loads
        $this->sendOtpInternal($user);
        
        return view('video.verify-otp', compact('maskedPhone'));
    }

    /**
     * Send OTP to customer's phone.
     */
    public function sendOtp(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found. Please update your profile.'
            ], 400);
        }
        
        $result = $this->sendOtpInternal($user);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your mobile number.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 500);
    }

    /**
     * Verify OTP submitted by customer.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $otpInput = $request->input('otp');
        
        // Find the most recent active OTP for this user
        $videoCallOtp = VideoCallOtp::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$videoCallOtp) {
            return back()->with('error', 'No active OTP found. Please request a new OTP.')->withInput();
        }

        // Check if OTP is expired
        if ($videoCallOtp->isExpired()) {
            return back()->with('error', 'OTP has expired. Please request a new OTP.')->withInput();
        }

        // Check if OTP is already verified
        if ($videoCallOtp->isVerified()) {
            return back()->with('error', 'This OTP has already been used. Please request a new OTP.')->withInput();
        }

        // Verify the OTP hash
        if (!hash_equals($videoCallOtp->otp_hash, hash('sha256', $otpInput))) {
            // Increment attempts
            $videoCallOtp->increment('attempts');
            
            return back()->with('error', 'Invalid OTP. Please try again.')->withInput();
        }

        // Mark OTP as verified
        $videoCallOtp->update([
            'verified_at' => now(),
            'is_active' => false
        ]);

        // Store OTP verification status in session
        Session::put('video_call_otp_verified', true);
        Session::put('video_call_otp_verified_at', now());

        Log::info('Video call OTP verified', [
            'user_id' => $user->id,
            'verified_at' => now()
        ]);

        return redirect()->route('video.call')->with('success', 'OTP verified successfully. You can now start your video call.');
    }

    /**
     * Internal method to send OTP.
     */
    protected function sendOtpInternal($user)
    {
        try {
            Log::info('OTP Sending - Starting Process', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->name,
                'user_phone' => $user->phone,
                'user_phone_type' => gettype($user->phone),
                'user_phone_is_null' => is_null($user->phone),
                'user_phone_is_empty' => empty($user->phone),
            ]);

            // Check if user has a phone number
            if (!$user->phone) {
                Log::warning('OTP Sending - No phone number', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ]);
                return ['success' => false, 'message' => 'Phone number not found. Please update your profile.'];
            }

            // Normalize phone number to match the working /test-sms format
            // The /test-sms route uses: '8801859385787' (with country code, no + prefix)
            $normalizedPhone = $this->normalizePhoneNumber($user->phone);
            
            Log::info('OTP Sending - Phone Normalization', [
                'user_id' => $user->id,
                'original_phone' => $user->phone,
                'original_phone_length' => strlen($user->phone),
                'normalized_phone' => $normalizedPhone,
                'normalized_phone_length' => strlen($normalizedPhone),
                'matches_test_format' => $normalizedPhone === '8801859385787',
            ]);

            // Generate 6-digit OTP
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            Log::info('OTP Sending - OTP Generated', [
                'user_id' => $user->id,
                'otp' => $otp,
                'otp_length' => strlen($otp),
            ]);
            
            // Deactivate any existing active OTPs for this user
            VideoCallOtp::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
            
            // Create new OTP record
            VideoCallOtp::create([
                'user_id' => $user->id,
                'otp_hash' => hash('sha256', $otp),
                'channel' => 'sms',
                'expires_at' => now()->addMinutes(10),
                'verified_at' => null,
                'attempts' => 0,
                'is_active' => true
            ]);
            
            // Send OTP via SMS
            $message = "Your Prime Bank Video Call OTP is: {$otp}. Valid for 10 minutes. Do not share this with anyone.";
            
            // DIAGNOSTIC: Log before sending SMS
            Log::info('OTP Sending - Before SMS Service', [
                'user_id' => $user->id,
                'original_phone' => $user->phone,
                'normalized_phone' => $normalizedPhone,
                'message' => $message,
                'message_length' => strlen($message),
                'sms_service_class' => get_class($this->smsService),
            ]);
            
            Log::info('OTP Sending - Calling SMS Service', [
                'user_id' => $user->id,
                'phone_to_send' => $normalizedPhone,
                'message_preview' => substr($message, 0, 50) . '...',
            ]);
            
            $smsResult = $this->smsService->send($normalizedPhone, $message);
            
            Log::info('OTP Sending - SMS Service Returned', [
                'user_id' => $user->id,
                'sms_result_success' => $smsResult['success'] ?? false,
                'sms_result_message' => $smsResult['message'] ?? 'No message',
                'sms_result_keys' => array_keys($smsResult),
            ]);
            
            // DIAGNOSTIC: Log SMS result
            Log::info('OTP Sending - SMS Service Result', [
                'user_id' => $user->id,
                'phone' => $normalizedPhone,
                'sms_success' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['message'] ?? 'No message',
            ]);
            
            if ($smsResult['success']) {
                Log::info('Video call OTP sent', [
                    'user_id' => $user->id,
                    'phone' => $normalizedPhone,
                    'expires_at' => now()->addMinutes(10)
                ]);
                
                return ['success' => true];
            } else {
                Log::error('Failed to send video call OTP', [
                    'user_id' => $user->id,
                    'error' => $smsResult['message']
                ]);
                
                return ['success' => false, 'message' => 'Failed to send OTP. Please try again.'];
            }
        } catch (\Exception $e) {
            Log::error('Error sending video call OTP', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }

    /**
     * Normalize phone number to match SMS provider format
     * Converts phone numbers to format: 880XXXXXXXXXX (with country code, no + prefix)
     */
    protected function normalizePhoneNumber($phone)
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If phone starts with 00, replace with 880
        if (strpos($phone, '00') === 0) {
            $phone = '880' . substr($phone, 2);
        }
        // If phone starts with +, remove the +
        elseif (strpos($phone, '+') === 0) {
            $phone = substr($phone, 1);
        }
        // If phone starts with 0 (local format), add 880
        elseif (strpos($phone, '0') === 0 && strlen($phone) === 11) {
            $phone = '880' . substr($phone, 1);
        }
        // If phone doesn't start with 880 and is 10 digits (local format), add 880
        elseif (strlen($phone) === 10 && strpos($phone, '880') !== 0) {
            $phone = '880' . $phone;
        }

        return $phone;
    }

    /**
     * Mask phone number for display.
     */
    protected function maskPhoneNumber($phone)
    {
        if (strlen($phone) <= 4) {
            return $phone;
        }
        
        $visible = substr($phone, -4);
        $masked = str_repeat('*', strlen($phone) - 4);
        
        return $masked . $visible;
    }

    /**
     * Customer video call page.
     */
    public function customerCall()
    {
        $user = Auth::user();
        
        // Check if OTP is verified
        if (!Session::get('video_call_otp_verified')) {
            return redirect()->route('video.verify.otp');
        }
        
        // Check if OTP verification is still valid (within 30 minutes)
        $verifiedAt = Session::get('video_call_otp_verified_at');
        if ($verifiedAt && now()->diffInMinutes($verifiedAt) > 30) {
            Session::forget(['video_call_otp_verified', 'video_call_otp_verified_at']);
            return redirect()->route('video.verify.otp');
        }
        
        // Check if user has active queue or session
        $activeQueue = CallQueue::where('user_id', $user->id)
            ->whereIn('status', ['waiting', 'connected'])
            ->latest()
            ->first();

        $activeSession = CallSession::where('user_id', $user->id)
            ->whereIn('status', ['ringing', 'connected'])
            ->latest()
            ->first();

        return view('video.customer', compact('activeQueue', 'activeSession'));
    }

    /**
     * Customer page - request a video call.
     */
    public function customerRequestCall(Request $request)
    {
        Log::info('Video call request received', ['user_id' => Auth::id()]);
        
        try {
            $user = Auth::user();
            
            // Check if OTP is verified
            if (!Session::get('video_call_otp_verified')) {
                return response()->json([
                    'type' => 'redirect',
                    'redirect_url' => route('video.verify.otp'),
                    'message' => 'Please verify your mobile number with OTP before starting a video call.'
                ]);
            }
            
            // Check if OTP verification is still valid (within 30 minutes)
            $verifiedAt = Session::get('video_call_otp_verified_at');
            if ($verifiedAt && now()->diffInMinutes($verifiedAt) > 30) {
                Session::forget(['video_call_otp_verified', 'video_call_otp_verified_at']);
                
                return response()->json([
                    'type' => 'redirect',
                    'redirect_url' => route('video.verify.otp'),
                    'message' => 'OTP verification has expired. Please verify again.'
                ]);
            }
            
            // Check if already in queue
            $existingQueue = CallQueue::where('user_id', $user->id)
                ->where('status', 'waiting')
                ->first();

            if ($existingQueue) {
                return response()->json([
                    'type' => 'queue',
                    'position' => $existingQueue->position,
                    'queue_id' => $existingQueue->id,
                    'message' => 'You are already in the queue. Position: ' . $existingQueue->position,
                ]);
            }

            // Check if already in an active call
            $activeSession = CallSession::where('user_id', $user->id)
                ->whereIn('status', ['ringing', 'connected'])
                ->first();

            if ($activeSession) {
                $tokenData = $this->agoraService->generateSimpleToken(
                    $activeSession->channel_name,
                    (int) $user->id
                );
                
                return response()->json([
                    'type' => 'connect',
                    'channel' => $activeSession->channel_name,
                    'token' => $tokenData['token'],
                    'uid' => $tokenData['uid'],
                    'app_id' => $tokenData['appId'],
                    'session_id' => $activeSession->id,
                    'message' => 'You have an active call session.',
                ]);
            }

            // Always add customer to queue first
            return $this->addToQueue($user);
        } catch (\Exception $e) {
            Log::error('Video call request error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connect customer to an available agent.
     */
    protected function connectToAgent($user, Agent $agent)
    {
        $channelName = CallSession::generateChannelName();
        
        // Create queue entry
        $queue = CallQueue::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? '',
            'customer_email' => $user->email,
            'status' => 'connected',
            'position' => 0,
            'connected_at' => now(),
        ]);

        // Create call session
        $session = CallSession::create([
            'channel_name' => $channelName,
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'call_queue_id' => $queue->id,
            'status' => 'ringing',
        ]);

        // Mark agent as busy
        $agent->setBusy();

        // Generate Agora token
        $tokenData = $this->agoraService->generateSimpleToken($channelName, (int) $user->id);

        Log::info('Customer connected to agent', [
            'customer_id' => $user->id,
            'agent_id' => $agent->id,
            'channel' => $channelName,
        ]);

        return response()->json([
            'type' => 'connect',
            'channel' => $channelName,
            'token' => $tokenData['token'],
            'uid' => $tokenData['uid'],
            'app_id' => $tokenData['appId'],
            'session_id' => $session->id,
            'agent_name' => $agent->name,
            'message' => 'Connected to ' . $agent->name,
        ]);
    }

    /**
     * Add customer to waiting queue.
     */
    protected function addToQueue($user)
    {
        $position = CallQueue::waitingCount() + 1;
        
        $queue = CallQueue::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? '',
            'customer_email' => $user->email,
            'status' => 'waiting',
            'position' => $position,
        ]);

        Log::info('Customer added to queue', [
            'customer_id' => $user->id,
            'position' => $position,
            'queue_id' => $queue->id,
        ]);

        return response()->json([
            'type' => 'queue',
            'queue_id' => $queue->id,
            'position' => $position,
            'message' => 'All agents are busy. You are in queue. Position: ' . $position,
        ]);
    }

    /**
     * Check queue status - for polling.
     */
    public function queueStatus(Request $request)
    {
        $user = Auth::user();
        $queueId = $request->input('queue_id');

        $queue = CallQueue::where('user_id', $user->id)
            ->where('id', $queueId)
            ->first();

        if (!$queue) {
            return response()->json([
                'type' => 'error',
                'message' => 'Queue entry not found.',
            ], 404);
        }

        if ($queue->status === 'connected') {
            // Find the call session
            $session = CallSession::where('call_queue_id', $queue->id)->first();
            $agent = $session->agent ?? Agent::find($session->agent_id);

            $tokenData = $this->agoraService->generateSimpleToken(
                $session->channel_name, 
                (int) $user->id
            );

            return response()->json([
                'type' => 'connect',
                'channel' => $session->channel_name,
                'token' => $tokenData['token'],
                'uid' => $tokenData['uid'],
                'app_id' => $tokenData['appId'],
                'session_id' => $session->id,
                'agent_name' => $agent->name ?? 'Agent',
                'message' => 'An agent is ready for you!',
            ]);
        }

        return response()->json([
            'type' => 'queue',
            'queue_id' => $queue->id,
            'position' => $queue->position,
            'status' => $queue->status,
            'wait_time' => $queue->wait_time,
            'message' => 'Still waiting. Position: ' . $queue->position,
        ]);
    }

    /**
     * Agent page - dashboard.
     */
    public function agentDashboard()
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        // Auto-create agent record for staff/receptionist users
        if (!$agent && ($user->hasRole('staff') || $user->hasRole('receptionist'))) {
            $agent = Agent::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'department' => 'Customer Support',
                'status' => 'offline',
            ]);
        }
        
        if (!$agent) {
            return redirect()->route('home')->with('error', 'You are not registered as an agent.');
        }

        $todayCalls = $agent->callSessions()
            ->whereDate('started_at', today())
            ->count();

        $todayDuration = $agent->callSessions()
            ->whereDate('started_at', today())
            ->sum('duration');

        $pendingQueue = CallQueue::where('status', 'waiting')->count();

        return view('video.agent-dashboard', compact('agent', 'todayCalls', 'todayDuration', 'pendingQueue'));
    }

    /**
     * Agent status update.
     */
    public function agentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:free,busy,offline',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $agent = Agent::where('user_id', Auth::id())->first();
        
        if (!$agent) {
            return response()->json(['error' => 'Agent not found.'], 404);
        }

        $status = $request->input('status');
        
        if ($status === 'free') {
            $agent->setFree();
        } elseif ($status === 'busy') {
            $agent->setBusy();
        } else {
            $agent->setOffline();
        }

        // If agent is free, check for waiting queue
        if ($status === 'free') {
            $nextInQueue = CallQueue::getNext();
            if ($nextInQueue) {
                // Connect to next customer
                $session = $this->connectQueueToAgent($nextInQueue, $agent);
                
                // Generate token for agent
                $tokenData = $this->agoraService->generateSimpleToken(
                    $session->channel_name,
                    (int) $agent->user_id
                );
                
                return response()->json([
                    'status' => $status,
                    'call_started' => true,
                    'customer_name' => $nextInQueue->customer_name,
                    'channel' => $session->channel_name,
                    'token' => $tokenData['token'],
                    'uid' => $tokenData['uid'],
                    'app_id' => $tokenData['appId'],
                    'session_id' => $session->id,
                ]);
            }
        }

        return response()->json([
            'status' => $status,
            'message' => 'Status updated successfully.',
        ]);
    }

    /**
     * Get real-time queue status for agent dashboard.
     */
    public function agentQueueStatus()
    {
        $pendingQueue = CallQueue::where('status', 'waiting')->count();
        
        return response()->json([
            'pending_queue' => $pendingQueue,
        ]);
    }

    /**
     * Connect queue entry to agent.
     */
    protected function connectQueueToAgent(CallQueue $queue, Agent $agent)
    {
        $channelName = CallSession::generateChannelName();

        $queue->markConnected();

        $session = CallSession::create([
            'channel_name' => $channelName,
            'user_id' => $queue->user_id,
            'agent_id' => $agent->id,
            'call_queue_id' => $queue->id,
            'status' => 'ringing',
        ]);

        $agent->setBusy();

        Log::info('Queue customer connected to agent', [
            'customer_id' => $queue->user_id,
            'agent_id' => $agent->id,
            'channel' => $channelName,
        ]);

        return $session;
    }

    /**
     * Agent start next call from queue.
     */
    public function agentStartCall(Request $request)
    {
        $agent = Agent::where('user_id', Auth::id())->first();
        
        if (!$agent) {
            return response()->json(['error' => 'Agent not found.'], 404);
        }

        if (!$agent->isAvailable()) {
            return response()->json(['error' => 'You are not available to take calls.'], 400);
        }

        $nextInQueue = CallQueue::getNext();
        
        if (!$nextInQueue) {
            return response()->json(['error' => 'No customers in queue.'], 404);
        }

        $session = $this->connectQueueToAgent($nextInQueue, $agent);

        $tokenData = $this->agoraService->generateSimpleToken(
            $session->channel_name, 
            (int) $agent->user_id
        );

        return response()->json([
            'channel' => $session->channel_name,
            'token' => $tokenData['token'],
            'uid' => $tokenData['uid'],
            'app_id' => $tokenData['appId'],
            'session_id' => $session->id,
            'customer_name' => $nextInQueue->customer_name,
        ]);
    }

    /**
     * End a call.
     */
    public function endCall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:call_sessions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $session = CallSession::find($request->input('session_id'));
        
        if (!$session) {
            return response()->json(['error' => 'Session not found.'], 404);
        }

        $session->endCall();

        Log::info('Call ended', [
            'session_id' => $session->id,
            'duration' => $session->duration,
        ]);

        return response()->json([
            'success' => true,
            'duration' => $session->duration,
            'message' => 'Call ended successfully.',
        ]);
    }

    /**
     * Customer feedback page.
     */
    public function feedback(Request $request)
    {
        $sessionId = $request->input('session_id');
        $session = CallSession::find($sessionId);
        
        if (!$session) {
            return redirect()->route('dashboard')->with('error', 'Session not found.');
        }

        return view('video.feedback', compact('session'));
    }

    /**
     * Submit feedback.
     */
    public function submitFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:call_sessions,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $session = CallSession::find($request->input('session_id'));

        CallFeedback::create([
            'call_session_id' => $session->id,
            'user_id' => $user->id,
            'agent_id' => $session->agent_id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'customer_name' => $user->name,
        ]);

        return redirect()->route('dashboard')->with('success', 'Thank you for your feedback!');
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard(Request $request)
    {
        $period = $request->input('period', 'today');
        
        if ($period === 'today') {
            $metrics = CallMetric::today() ?? new CallMetric();
        } elseif ($period === 'week') {
            $metrics = CallMetric::thisWeek();
        } else {
            $metrics = CallMetric::thisMonth();
        }

        $todayCalls = CallSession::whereDate('started_at', today())->count();
        $activeCalls = CallSession::whereIn('status', ['ringing', 'connected'])->count();
        $waitingQueue = CallQueue::where('status', 'waiting')->count();
        $totalAgents = Agent::count();
        $freeAgents = Agent::where('status', 'free')->count();

        return view('video.admin-dashboard', compact(
            'metrics',
            'todayCalls',
            'activeCalls',
            'waitingQueue',
            'totalAgents',
            'freeAgents',
            'period'
        ));
    }

    /**
     * Get call statistics API.
     */
    public function apiStats(Request $request)
    {
        $period = $request->input('period', 'today');
        
        if ($period === 'today') {
            $metrics = CallMetric::today() ?? new CallMetric();
            $sessions = CallSession::whereDate('started_at', today())->get();
        } elseif ($period === 'week') {
            $metrics = CallMetric::thisWeek();
            $sessions = CallSession::whereBetween('started_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        } else {
            $metrics = CallMetric::thisMonth();
            $sessions = CallSession::whereYear('started_at', now()->year)
                ->whereMonth('started_at', now()->month)
                ->get();
        }

        return response()->json([
            'metrics' => $metrics,
            'total_calls' => $sessions->count(),
            'connected_calls' => $sessions->where('status', 'ended')->count(),
            'avg_duration' => $sessions->avg('duration') ?? 0,
        ]);
    }

    /**
     * Cancel queue entry.
     */
    public function cancelQueue(Request $request)
    {
        $user = Auth::user();
        
        $queue = CallQueue::where('user_id', $user->id)
            ->where('status', 'waiting')
            ->first();

        if (!$queue) {
            return response()->json(['error' => 'Queue entry not found.'], 404);
        }

        $queue->update(['status' => 'cancelled']);
        CallQueue::reassignPositions();

        return response()->json([
            'success' => true,
            'message' => 'Queue request cancelled.',
        ]);
    }

    // ============ CHAT METHODS ============

    /**
     * Send a chat message in a video call session.
     */
    public function sendChatMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:call_sessions,id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $sessionId = $request->input('session_id');
        $message = $request->input('message');

        // Verify session exists and user is part of it
        $session = CallSession::find($sessionId);
        if (!$session) {
            return response()->json(['error' => 'Session not found.'], 404);
        }

        // Check if user is customer or agent in this session
        $isCustomer = $session->user_id === $user->id;
        $isAgent = $session->agent_id && $session->agent->user_id === $user->id;

        if (!$isCustomer && !$isAgent) {
            return response()->json(['error' => 'You are not part of this call session.'], 403);
        }

        // Determine sender type
        $senderType = $isCustomer ? 'customer' : 'agent';

        // Create chat message
        $chat = VideoCallChat::create([
            'call_session_id' => $sessionId,
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'message' => $message,
            'is_read' => false,
        ]);

        Log::info('Chat message sent', [
            'session_id' => $sessionId,
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'message_length' => strlen($message),
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chat->id,
                'session_id' => $sessionId,
                'sender_id' => $user->id,
                'sender_name' => $user->name,
                'sender_type' => $senderType,
                'message' => $message,
                'created_at' => $chat->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Get chat messages for a video call session.
     */
    public function getChatMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:call_sessions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $sessionId = $request->input('session_id');

        // Verify session exists
        $session = CallSession::find($sessionId);
        if (!$session) {
            return response()->json(['error' => 'Session not found.'], 404);
        }

        // Check if user is part of this session
        $isCustomer = $session->user_id === $user->id;
        $isAgent = $session->agent_id && $session->agent->user_id === $user->id;

        if (!$isCustomer && !$isAgent) {
            return response()->json(['error' => 'You are not part of this call session.'], 403);
        }

        // Get all messages
        $messages = VideoCallChat::forSession($sessionId);

        // Format messages for frontend
        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? 'Unknown',
                'sender_type' => $msg->sender_type,
                'message' => $msg->message,
                'is_read' => $msg->is_read,
                'created_at' => $msg->created_at->toISOString(),
            ];
        });

        // Mark messages from other user as read
        VideoCallChat::markAllAsRead($sessionId, $user->id);

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
        ]);
    }

    /**
     * Get unread message count for a session.
     */
    public function getUnreadCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:call_sessions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $sessionId = $request->input('session_id');

        $count = VideoCallChat::unreadCount($sessionId, $user->id);

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }
}
