<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\CallQueue;
use App\Models\CallSession;
use App\Models\CallFeedback;
use App\Models\CallMetric;
use App\Services\AgoraService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
     * Customer video call page.
     */
    public function customerCall()
    {
        $user = Auth::user();
        
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
}
