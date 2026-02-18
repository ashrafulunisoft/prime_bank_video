<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\VisitType;
use App\Models\pragati\Order;
use App\Models\pragati\Claim;
use App\Models\UserInfo;
use App\Services\EmailNotificationService;
use App\Services\SmsNotificationService;
use App\Events\VisitWaitingForApproval;
use App\Events\VisitApproved;
use App\Events\VisitRejected;
use App\Events\VisitCheckedIn;
use App\Events\VisitCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VisitorController extends Controller
{
    /**
     * Display the dashboard for receptionist, staff, and visitor roles
     */
    public function dashboard()
    {
        // dd("Hello");
        // Get Video Call Statistics
        $today = now()->startOfDay();
        $totalCallsToday = \App\Models\CallSession::whereDate('started_at', $today)->count();
        $activeCalls = \App\Models\CallSession::where('status', 'active')->count();
        $callsInQueue = \App\Models\CallQueue::where('status', 'waiting')->count();
        $totalAgents = \App\Models\Agent::count();
        $freeAgents = \App\Models\Agent::where('status', 'free')->count();

        // Calculate total call duration
        $totalDuration = \App\Models\CallSession::where('status', 'completed')
            ->sum('duration');
        $totalCallDurationFormatted = gmdate('H:i:s', $totalDuration);

        // Calculate average call duration
        $completedCalls = \App\Models\CallSession::where('status', 'completed')->count();
        $avgCallDuration = $completedCalls > 0 ? round($totalDuration / $completedCalls / 60) : 0;
        $avgCallDurationText = $avgCallDuration . 'm';

        // Calculate waiting time
        $waitingQueueEntries = \App\Models\CallQueue::where('status', 'waiting')->get();
        $totalWaitingTime = 0;
        foreach ($waitingQueueEntries as $entry) {
            $totalWaitingTime += $entry->wait_time;
        }
        $avgWaitingTime = $callsInQueue > 0 ? round($totalWaitingTime / $callsInQueue) : 0;
        $avgWaitingTimeText = $avgWaitingTime . 's';
        $totalWaitingTimeFormatted = gmdate('H:i:s', $totalWaitingTime);

        // Calculate average customer rating
        $avgCustomerRating = \App\Models\CallFeedback::avg('rating') ?? 0;

        // Get visitor statistics based on user permissions
        $stats = [
            // Video Call Statistics
            'total_calls_today' => $totalCallsToday,
            'active_calls' => $activeCalls,
            'calls_in_queue' => $callsInQueue,
            'total_agents' => $totalAgents,
            'free_agents' => $freeAgents,
            'total_call_duration_formatted' => $totalCallDurationFormatted,
            'avg_call_duration' => $avgCallDurationText,
            'avg_waiting_time' => $avgWaitingTimeText,
            'total_waiting_time' => $totalWaitingTimeFormatted,
            'avg_customer_rating' => $avgCustomerRating,

            // Visitor Statistics
            'total_visitors' => Visitor::count(),
            'total_visits' => Visit::count(),
            'pending_visits' => Visit::where('status', 'pending')->count(),
            'approved_visits' => Visit::where('status', 'approved')->count(),
            'completed_visits' => Visit::where('status', 'completed')->count(),
            'cancelled_visits' => Visit::where('status', 'cancelled')->count(),
            'visits_today' => Visit::whereDate('schedule_time', today())->count(),
            'active_visits' => Visit::where('status', 'approved')
                ->whereDate('schedule_time', today())
                ->count(),
        ];

        // Get recent visits based on permissions
        $recentVisitsQuery = Visit::with(['visitor', 'type', 'meetingUser']);

        // If user doesn't have view visitors permission, show only their own visits
        if (!auth()->user()->can('view visitors')) {
            $recentVisitsQuery->where('meeting_user_id', auth()->id());
        }

        $recentVisits = $recentVisitsQuery->orderBy('created_at', 'desc')->limit(10)->get();

        // Get today's visits based on permissions
        $todayVisitsQuery = Visit::with(['visitor', 'type', 'meetingUser'])
            ->whereDate('schedule_time', today());

        if (!auth()->user()->can('view visitors')) {
            $todayVisitsQuery->where('meeting_user_id', auth()->id());
        }

        $todayVisits = $todayVisitsQuery->orderBy('schedule_time', 'asc')->get();

        // Get pending visits based on permissions
        $pendingVisitsQuery = Visit::with(['visitor', 'type', 'meetingUser'])
            ->where('status', 'pending');

        if (!auth()->user()->can('edit visitors')) {
            $pendingVisitsQuery->where('meeting_user_id', auth()->id());
        }

        $pendingVisits = $pendingVisitsQuery->orderBy('created_at', 'desc')->limit(5)->get();

        // Get user profile info (by email since table doesn't have user_id)
        $userInfo = UserInfo::where('email', auth()->user()->email)->first();

        // Get insurance orders/policies for current user
        $userOrders = Order::with(['package'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get insurance claims for current user
        $userClaims = Claim::with(['package', 'order'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate insurance stats
        $insuranceStats = [
            'total_policies' => Order::where('user_id', auth()->id())->count(),
            'active_policies' => Order::where('user_id', auth()->id())->where('status', 'active')->count(),
            'total_claims' => Claim::where('user_id', auth()->id())->count(),
            'pending_claims' => Claim::where('user_id', auth()->id())->whereIn('status', ['submitted', 'under_review'])->count(),
        ];

        //  dd("Hello"); 
        return view('vms.backend.visitor.dashboard', compact(
            'stats',
            'recentVisits',
            'todayVisits',
            'pendingVisits',
            'userInfo',
            'userOrders',
            'userClaims',
            'insuranceStats'
        ));
    }

    /**
     * Display a listing of visitors
     */
    public function index()
    {
        $visitsQuery = Visit::with(['visitor', 'type', 'meetingUser'])
            ->orderBy('created_at', 'desc');

        // If user doesn't have view visitors permission, show only their own visits
        if (!auth()->user()->can('view visitors')) {
            $visitsQuery->where('meeting_user_id', auth()->id());
        }

        $visits = $visitsQuery->paginate(10);

        return view('vms.backend.visitor.index', compact('visits'));
    }

    /**
     * Show the form for creating a new visitor
     */
    public function create()
    {
        $users = User::all();
        $visitTypes = VisitType::all();

        return view('vms.backend.visitor.create', compact('users', 'visitTypes'));
    }

    /**
     * Store a newly created visitor
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'host_name' => 'required|string|max:255',
            'purpose' => 'required|string|max:500',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_type_id' => 'required|exists:visit_types,id',
        ]);

        DB::beginTransaction();

        try {
            // Create or find visitor
            $visitor = Visitor::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->company,
                    'is_blocked' => false,
                ]
            );

            // Find host user
            $hostUser = User::where('name', 'like', '%' . $request->host_name . '%')->first();

            if (is_null($hostUser)) {
                $hostUser = Auth::user();
                Log::warning('Host not found, using current user as default host', [
                    'requested_host' => $request->host_name,
                    'default_host' => $hostUser->name,
                ]);
            }

            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create visit record with OTP
            $visit = Visit::create([
                'visitor_id' => $visitor->id,
                'meeting_user_id' => $hostUser->id,
                'visit_type_id' => $request->visit_type_id,
                'purpose' => $request->purpose,
                'schedule_time' => $request->visit_date,
                'status' => 'pending_otp',
                'otp' => $otp,
            ]);

            // Send email notification with OTP
            $emailData = [
                'visitor_name' => $visitor->name,
                'visitor_email' => $visitor->email,
                'visitor_phone' => $visitor->phone,
                'visitor_company' => $visitor->address,
                'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                'visit_type' => $visit->type->name ?? 'N/A',
                'purpose' => $visit->purpose,
                'host_name' => $hostUser->name,
                'otp' => $otp,
                'status' => $visit->status,
            ];

            $emailService = new EmailNotificationService();
            $emailService->sendVisitorRegistrationEmail($emailData);

            // Send SMS notification if phone number exists
            if (!empty($visitor->phone)) {
                $smsMessage = "Dear {$visitor->name}, Your visit to UCB Bank has been registered for " .
                              \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A') .
                              ". Host: {$hostUser->name}. Your OTP is: {$otp}. Use this to verify your visit. Thank you!";

                $phone = preg_replace('/[^0-9]/', '', $visitor->phone);
                if (strpos($phone, '880') !== 0) {
                    $phone = '88' . $phone;
                }

                $smsService = new SmsNotificationService();
                $smsService->send($phone, $smsMessage);
            }

            DB::commit();

            return redirect()->route('visitor.show', $visit->id)
                ->with('success', 'Visitor ' . $visitor->name . ' registered successfully! OTP has been sent to their email.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during visitor registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to register visitor: ' . $e->getMessage());
        }
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:visitors,email|max:255',
    //         'phone' => 'nullable|string|max:20',
    //         'company' => 'nullable|string|max:255',
    //         'host_name' => 'required|string|max:255',
    //         'purpose' => 'required|string|max:500',
    //         'visit_date' => 'required|date|after_or_equal:today',
    //         'visit_type_id' => 'required|exists:visit_types,id',
    //         'face_image' => 'nullable|string',
    //     ]);

    //     try {
    //         // Create or find visitor
    //         $visitor = Visitor::firstOrCreate(
    //             ['email' => $request->email],
    //             [
    //                 'name' => $request->name,
    //                 'phone' => $request->phone,
    //                 'address' => $request->company,
    //                 'is_blocked' => false,
    //             ]
    //         );

    //         // Find host user
    //         $hostUser = User::where('name', 'like', '%' . $request->host_name . '%')->first();

    //         if (is_null($hostUser)) {
    //             $hostUser = Auth::user();
    //             Log::warning('Host not found, using current user as default host', [
    //                 'requested_host' => $request->host_name,
    //                 'default_host' => $hostUser->name,
    //             ]);
    //         }

    //         // Create visit record
    //         $visit = Visit::create([
    //             'visitor_id' => $visitor->id,
    //             'meeting_user_id' => $hostUser->id,
    //             'visit_type_id' => $request->visit_type_id,
    //             'purpose' => $request->purpose,
    //             'schedule_time' => $request->visit_date,
    //             'status' => 'pending', // Default to pending for non-admin
    //         ]);

    //         // Send email notification
    //         $emailData = [
    //             'visitor_name' => $visitor->name,
    //             'visitor_email' => $visitor->email,
    //             'visitor_phone' => $visitor->phone,
    //             'visitor_company' => $visitor->address,
    //             'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
    //             'visit_type' => $visit->type->name ?? 'N/A',
    //             'purpose' => $visit->purpose,
    //             'host_name' => $hostUser->name,
    //             'status' => $visit->status,
    //         ];

    //         $emailService = new EmailNotificationService();
    //         $emailService->sendVisitorRegistrationEmail($emailData);

    //         // Send SMS notification if phone number exists
    //         if (!empty($visitor->phone)) {
    //             $smsMessage = "Dear {$visitor->name}, Your visit to UCB Bank has been registered for " .
    //                           \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A') .
    //                           ". Host: {$hostUser->name}. Status: {$visit->status}. Thank you!";

    //             $phone = preg_replace('/[^0-9]/', '', $visitor->phone);
    //             if (strpos($phone, '880') !== 0) {
    //                 $phone = '88' . $phone;
    //             }

    //             $smsService = new SmsNotificationService();
    //             $smsService->send($phone, $smsMessage);
    //         }

    //         return redirect()->route('visitor.index')
    //             ->with('success', 'Visitor ' . $visitor->name . ' registered successfully!');

    //     } catch (\Exception $e) {
    //         Log::error('Error during visitor registration', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         return back()->with('error', 'Failed to register visitor: ' . $e->getMessage());
    //     }
    // }

    /**
     * Display the specified visitor
     */
    public function show($id)
    {
        $visit = Visit::with(['visitor', 'type', 'meetingUser'])->findOrFail($id);

        return view('vms.backend.visitor.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified visitor
     */
    public function edit($id)
    {
        $visit = Visit::with(['visitor', 'type', 'meetingUser'])->findOrFail($id);
        $users = User::all();
        $visitTypes = VisitType::all();

        return view('vms.backend.visitor.edit', compact('visit', 'users', 'visitTypes'));
    }

    /**
     * Update the specified visitor
     */
    public function update(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);
        $visitor = Visitor::findOrFail($visit->visitor_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:visitors,email,'.$visitor->id.'|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'host_name' => 'required|string|max:255',
            'purpose' => 'required|string|max:500',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_type_id' => 'required|exists:visit_types,id',
            'status' => 'nullable|in:approved,pending,completed,cancelled',
        ]);

        try {
            // Update visitor information
            $visitor->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->company,
            ]);

            // Find host user
            $hostUser = User::where('name', 'like', '%' . $request->host_name . '%')->first();
            if (is_null($hostUser)) {
                $hostUser = Auth::user();
            }

            // Update visit
            $oldStatus = $visit->status;
            $visit->update([
                'meeting_user_id' => $hostUser->id,
                'visit_type_id' => $request->visit_type_id,
                'purpose' => $request->purpose,
                'schedule_time' => $request->visit_date,
                'status' => $request->status ?? $oldStatus,
            ]);

            // Send status update notification if status changed
            if ($oldStatus !== $visit->status) {
                // Send email
                $emailData = [
                    'visitor_name' => $visitor->name,
                    'visitor_email' => $visitor->email,
                    'visitor_company' => $visitor->address,
                    'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                    'visit_type' => $visit->type->name ?? 'N/A',
                    'purpose' => $visit->purpose,
                    'host_name' => $hostUser->name,
                    'status' => $visit->status,
                ];

                $emailService = new EmailNotificationService();
                $emailService->sendVisitStatusEmail($emailData);

                // Send SMS if phone exists
                if (!empty($visitor->phone)) {
                    $statusMessages = [
                        'approved' => 'Your visit has been approved.',
                        'completed' => 'Your visit has been completed.',
                        'cancelled' => 'Your visit has been cancelled.',
                        'pending' => 'Your visit is pending approval.',
                    ];

                    $statusMessage = $statusMessages[$visit->status] ?? "Your visit status is: " . ucfirst($visit->status);
                    $smsMessage = "Dear {$visitor->name}, {$statusMessage} Thank you!";

                    $phone = preg_replace('/[^0-9]/', '', $visitor->phone);
                    if (strpos($phone, '880') !== 0) {
                        $phone = '88' . $phone;
                    }

                    $smsService = new SmsNotificationService();
                    $smsService->send($phone, $smsMessage);
                }
            }

            return redirect()->route('visitor.index')
                ->with('success', 'Visitor updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating visitor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to update visitor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified visitor
     */
    public function destroy($id)
    {
        try {
            $visit = Visit::findOrFail($id);
            $visit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Visit deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting visitor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete visit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-fill visitor details from previous visits (DEPRECATED - use specific methods below)
     */
    public function autofill(Request $request)
    {
        $email = $request->get('email');
        $phone = $request->get('phone');

        $visitor = null;

        if (!empty($email)) {
            $visitor = Visitor::where('email', $email)->first();
        } elseif (!empty($phone)) {
            $visitor = Visitor::where('phone', $phone)->first();
        }

        if (!is_null($visitor)) {
            // Get latest visit for this visitor with relationships
            $latestVisit = $visitor->visits()->with('meetingUser')->latest()->first();

            return response()->json([
                'success' => true,
                'visitor' => [
                    'name' => $visitor->name,
                    'email' => $visitor->email,
                    'phone' => $visitor->phone,
                    'company' => $visitor->address,
                ],
                'latest_visit' => $latestVisit ? [
                    'visit_type_id' => $latestVisit->visit_type_id,
                    'purpose' => $latestVisit->purpose,
                    'host_name' => $latestVisit->meetingUser->name ?? '',
                ] : null,
            ]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Check visitor by email (EXACT COPY from AdminController)
     */
    public function checkVisitorByEmail(Request $request)
    {
        $email = $request->get('email');

        Log::info('=== checkVisitorByEmail Called ===', ['email' => $email]);

        $visitor = Visitor::where('email', $email)->first();

        Log::info('Visitor query result', ['found' => !is_null($visitor)]);

        if ($visitor) {
            Log::info('Returning visitor data', ['visitor_name' => $visitor->name]);

            return response()->json([
                'success' => true,
                'visitor' => [
                    'name' => $visitor->name,
                    'email' => $visitor->email,
                    'phone' => $visitor->phone,
                    'company' => $visitor->address,
                ]
            ]);
        }

        Log::info('No visitor found, returning false');
        return response()->json(['success' => false]);
    }

    /**
     * Check visitor by phone (EXACT COPY from AdminController)
     */
    public function checkVisitorByPhone(Request $request)
    {
        $phone = $request->get('phone');

        Log::info('=== checkVisitorByPhone Called ===', ['phone' => $phone]);

        $visitor = Visitor::where('phone', $phone)->first();

        Log::info('Visitor query result', ['found' => !is_null($visitor)]);

        if ($visitor) {
            Log::info('Returning visitor data', ['visitor_name' => $visitor->name]);

            return response()->json([
                'success' => true,
                'visitor' => [
                    'name' => $visitor->name,
                    'email' => $visitor->email,
                    'phone' => $visitor->phone,
                    'company' => $visitor->address,
                ]
            ]);
        }

        Log::info('No visitor found, returning false');
        return response()->json(['success' => false]);
    }

    /**
     * Search for host (EXACT COPY from AdminController)
     */
    public function searchHost(Request $request)
    {
        $query = $request->get('q');

        Log::info('=== searchHost Called ===', ['query' => $query]);

        $users = User::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('email', 'like', '%' . $query . '%');
                })
                ->limit(10)
                ->get(['id', 'name', 'email']);

        Log::info('Users found', ['count' => $users->count()]);

        return response()->json([
            'success' => true,
            'hosts' => $users
        ]);
    }

    /**
     * Get visitor statistics
     */
    public function statistics()
    {
        $stats = [
            'total_visitors' => Visitor::count(),
            'total_visits' => Visit::count(),
            'pending_visits' => Visit::where('status', 'pending')->count(),
            'approved_visits' => Visit::where('status', 'approved')->count(),
            'completed_visits' => Visit::where('status', 'completed')->count(),
            'cancelled_visits' => Visit::where('status', 'cancelled')->count(),
            'visits_this_month' => Visit::whereMonth('schedule_time', now()->month)
                                         ->whereYear('schedule_time', now()->year)
                                         ->count(),
        ];

        return view('vms.backend.visitor.statistics', compact('stats'));
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp($id)
    {
        $visit = Visit::with('visitor')->findOrFail($id);

        return view('vms.backend.visitor.verify-otp', compact('visit'));
    }

    /**
     * Verify OTP for visit
     */
    public function verifyOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $visit = Visit::with(['visitor', 'meetingUser'])->findOrFail($id);

        if ($visit->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        // Update visit status after OTP verification
        $visit->update([
            'otp' => null,
            'otp_verified_at' => now(),
            'status' => 'pending_host',
        ]);

        // Dispatch event for real-time updates
        broadcast(new VisitWaitingForApproval($visit));

        // Send notification email to host with approval link
        $hostEmailData = [
            'host_name' => $visit->meetingUser->name,
            'visitor_name' => $visit->visitor->name,
            'visitor_email' => $visit->visitor->email,
            'visitor_phone' => $visit->visitor->phone,
            'purpose' => $visit->purpose,
            'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
            'visit_type' => $visit->type->name ?? 'N/A',
            'approval_link' => route('visitor.show', $visit->id),
        ];

        try {
            Mail::to($visit->meetingUser->email)->send(new \App\Mail\VisitApprovalRequestEmail($hostEmailData));
        } catch (\Exception $e) {
            Log::error('Failed to send host approval email', [
                'error' => $e->getMessage(),
                'visit_id' => $visit->id,
            ]);
        }

        return redirect()->route('visitor.show', $visit->id)
            ->with('success', 'OTP verified successfully. Your visit is now waiting for host approval. Host has been notified.');
    }

    /**
     * Approve visit and generate RFID
     */
    public function approveVisit($id)
    {
        try {
            $visit = Visit::with(['visitor', 'meetingUser'])->findOrFail($id);

            // Generate RFID
            $rfid = 'RFID-' . strtoupper(Str::random(8));

            $visit->update([
                'status' => 'approved',
                'rfid' => $rfid,
                'approved_at' => now(),
            ]);

            // Dispatch event for real-time updates
            broadcast(new VisitApproved($visit));

            // Send email notification to visitor
            $emailData = [
                'visitor_name' => $visit->visitor->name,
                'visitor_email' => $visit->visitor->email,
                'rfid' => $rfid,
                'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                'host_name' => $visit->meetingUser->name,
            ];

            try {
                Mail::to($visit->visitor->email)->send(new \App\Mail\VisitApprovedEmail($emailData));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email', [
                    'error' => $e->getMessage(),
                    'visit_id' => $visit->id,
                ]);
            }

            // Always return JSON for this endpoint with explicit content type
            return response()->json([
                'success' => true,
                'message' => 'Visit approved successfully. RFID: ' . $rfid,
                'rfid' => $rfid
            ])->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error('Error approving visit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'visit_id' => $id,
            ]);

            // Always return JSON error with explicit content type
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve visit: ' . $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Reject visit
     */
    public function rejectVisit(Request $request, $id)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $visit = Visit::with(['visitor', 'meetingUser'])->findOrFail($id);

            $visit->update([
                'status' => 'rejected',
                'rejected_reason' => $validated['reason'],
            ]);

            // Dispatch event for real-time updates
            broadcast(new VisitRejected($visit));

            // Send email notification to visitor
            $emailData = [
                'visitor_name' => $visit->visitor->name,
                'visitor_email' => $visit->visitor->email,
                'reason' => $validated['reason'],
                'host_name' => $visit->meetingUser->name,
            ];

            try {
                Mail::to($visit->visitor->email)->send(new \App\Mail\VisitRejectedEmail($emailData));
            } catch (\Exception $e) {
                Log::error('Failed to send rejection email', [
                    'error' => $e->getMessage(),
                    'visit_id' => $visit->id,
                ]);
            }

            // Always return JSON for this endpoint with explicit content type
            return response()->json([
                'success' => true,
                'message' => 'Visit rejected successfully.'
            ])->header('Content-Type', 'application/json');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON with explicit content type
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error('Error rejecting visit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'visit_id' => $id,
            ]);

            // Always return JSON error with explicit content type
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject visit: ' . $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Check-in visitor
     */
    public function checkIn($id)
    {
        try {
            $visit = Visit::findOrFail($id);

            $user = auth()->user();
            $hasPermission = $user ? $user->can('checkin visit') : false;

            Log::info('Check-in attempt', [
                'visit_id' => $id,
                'current_status' => $visit->status,
                'user_id' => auth()->id(),
                'user_has_permission' => $hasPermission,
            ]);

            if ($visit->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Visit must be approved before check-in. Current status: ' . $visit->status,
                ], 400);
            }

            $visit->update([
                'status' => 'checked_in',
                'checkin_time' => now(),
            ]);

            Log::info('Check-in successful', ['visit_id' => $id]);

            // Dispatch event for real-time updates
            broadcast(new VisitCheckedIn($visit));

            return response()->json([
                'success' => true,
                'message' => 'Visitor checked in successfully.',
                'checkin_time' => $visit->checkin_time->format('h:i A'),
            ]);

        } catch (\Exception $e) {
            Log::error('Check-in error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'visit_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check-out visitor
     */
    public function checkOut($id)
    {
        $visit = Visit::findOrFail($id);

        if ($visit->status !== 'checked_in') {
            return response()->json([
                'success' => false,
                'message' => 'Visitor must be checked in before check-out.',
            ], 400);
        }

        $visit->update([
            'status' => 'completed',
            'checkout_time' => now(),
        ]);

        // Dispatch event for real-time updates
        broadcast(new VisitCompleted($visit));

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked out successfully.',
            'checkout_time' => $visit->checkout_time->format('h:i A'),
        ]);
    }

    /**
     * Live dashboard view
     */
    public function liveDashboard()
    {
        // Get all active visits for initial load
        $visits = Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereIn('status', ['pending_host', 'approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vms.backend.visitor.live-dashboard', compact('visits'));
    }

    /**
     * API endpoint for live dashboard data
     */
    public function liveVisitorsApi()
    {
        $visits = Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereIn('status', ['pending_host', 'approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($visits);
    }

    /**
     * Get pending visits for current host (for notification panel)
     */
    public function hostPendingVisitsApi()
    {
        $visits = Visit::with(['visitor', 'meetingUser'])
            ->where('status', 'pending_host')
            ->where('meeting_user_id', Auth::id())
            ->orderBy('schedule_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'visits' => $visits
        ]);
    }

    /**
     * Display pending visits for approval
     */
    public function pendingVisits()
    {
        $visits = Visit::with(['visitor', 'type', 'meetingUser'])
            ->where('status', 'pending_host')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vms.backend.visitor.pending', compact('visits'));
    }

    /**
     * Display rejected visits
     */
    public function rejectedVisits()
    {
        $visits = Visit::with(['visitor', 'type', 'meetingUser'])
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vms.backend.visitor.rejected', compact('visits'));
    }

    /**
     * Display approved visits
     */
    public function approvedVisits()
    {
        $visits = Visit::with(['visitor', 'type', 'meetingUser'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vms.backend.visitor.approved', compact('visits'));
    }

    /**
     * Display visit history
     */
    public function visitHistory()
    {
        $visitsQuery = Visit::with(['visitor', 'type', 'meetingUser'])
            ->orderBy('created_at', 'desc');

        // If user doesn't have view visitors permission, show only their own visits
        if (!auth()->user()->can('view visitors')) {
            $visitsQuery->where('meeting_user_id', auth()->id());
        }

        $visits = $visitsQuery->paginate(10);

        return view('vms.backend.visitor.history', compact('visits'));
    }

    /**
     * Display active visits (checked_in)
     */
    public function activeVisits()
    {
        $visits = Visit::with(['visitor', 'type', 'meetingUser'])
            ->where('status', 'checked_in')
            ->orderBy('checkin_time', 'desc')
            ->paginate(10);

        return view('vms.backend.visitor.active', compact('visits'));
    }

    /**
     * Display check-in/check-out panel
     */
    public function checkinCheckout()
    {
        $visitsQuery = Visit::with(['visitor', 'type', 'meetingUser'])
            ->whereIn('status', ['approved', 'checked_in'])
            ->orderBy('created_at', 'desc');

        // If user doesn't have edit visitors permission, show only their own visits
        if (!auth()->user()->can('edit visitors')) {
            $visitsQuery->where('meeting_user_id', auth()->id());
        }

        $visits = $visitsQuery->paginate(10);

        return view('vms.backend.visitor.checkin-checkout', compact('visits'));
    }

    /**
     * Search visitors by phone number for autocomplete
     */
    public function searchVisitorByPhone(Request $request)
    {
        $phone = $request->get('q');

        $visitors = Visitor::where('phone', 'like', "%{$phone}%")
            ->where('is_blocked', false)
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json([
            'success' => true,
            'visitors' => $visitors
        ]);
    }

    /**
     * Search hosts by email for autocomplete
     */
    public function searchHostByEmail(Request $request)
    {
        $email = $request->get('q');

        $users = User::where('email', 'like', "%{$email}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'hosts' => $users
        ]);
    }

    /**
     * Generate visitor report with date range and selected visitors
     */
    public function report(Request $request)
    {
        $selectedVisitorIds = $request->input('visitor_ids', []);
        $hostEmail = $request->input('host_email');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build query
        $query = Visit::with(['visitor', 'type', 'meetingUser']);

        // Apply filters using OR logic - visits matching ANY criteria should be included
        if (!empty($selectedVisitorIds) || !empty($hostEmail) || ($startDate && $endDate)) {
            $query->where(function($q) use ($selectedVisitorIds, $hostEmail, $startDate, $endDate) {
                // Filter by selected visitors if any
                if (!empty($selectedVisitorIds)) {
                    $q->whereIn('visitor_id', $selectedVisitorIds);
                }

                // Filter by host email if provided (JavaScript sends host ID as host_email)
                if (!empty($hostEmail)) {
                    $q->orWhere('meeting_user_id', $hostEmail);
                }

                // Apply date range filter if provided
                if ($startDate && $endDate) {
                    $q->orWhereBetween('schedule_time', [$startDate, $endDate]);
                }
            });
        }

        // If user doesn't have view visitors permission, show only their own visits
        if (!auth()->user()->can('view visitors')) {
            $query->where('meeting_user_id', auth()->id());
        }

        $visits = $query->orderBy('schedule_time', 'desc')->paginate(20);

        // Get selected visitors details for display
        $selectedVisitors = [];
        if (!empty($selectedVisitorIds)) {
            $selectedVisitors = Visitor::whereIn('id', $selectedVisitorIds)
                ->get(['id', 'name', 'phone', 'email']);
        }

        // Get selected host for display
        $selectedHost = null;
        if (!empty($hostEmail)) {
            $selectedHost = User::where('email', $hostEmail)->first(['id', 'name', 'email']);
        }

        return view('vms.backend.visitor.report', compact(
            'visits',
            'selectedVisitors',
            'selectedHost',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export visitor report to CSV
     */
    public function exportReportCsv(Request $request)
    {
        $selectedVisitorIds = $request->input('visitor_ids', []);
        $hostEmail = $request->input('host_email');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Log received parameters for debugging
        Log::info('CSV Export - Received parameters', [
            'visitor_ids' => $selectedVisitorIds,
            'host_email' => $hostEmail,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'all_request_data' => $request->all(),
        ]);

        // Get selected visitors
        $visitors = [];
        if (!empty($selectedVisitorIds)) {
            $visitors = Visitor::whereIn('id', $selectedVisitorIds)->get();
        }

        // Get selected host (host_email parameter contains host ID)
        $host = null;
        if (!empty($hostEmail)) {
            $host = User::find($hostEmail);
        }

        // Generate CSV content
        $headers = [
            'Type',
            'Name',
            'Phone',
            'Email',
            'Host Name',
            'Visit Type',
            'Purpose',
            'Scheduled Date',
            'Check-in',
            'Check-out',
            'Status'
        ];

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $headers);

        // Add selected visitors to CSV
        foreach ($visitors as $visitor) {
            fputcsv($csv, [
                'Visitor',
                $visitor->name ?? 'N/A',
                $visitor->phone ?? 'N/A',
                $visitor->email ?? 'N/A',
                $host ? $host->name : 'N/A',
                'N/A',
                '-',
                '-',
                '-',
                '-',
                'Selected'
            ]);
        }

        // Add selected host to CSV
        if ($host) {
            fputcsv($csv, [
                'Host',
                '-',
                '-',
                $host->email ?? 'N/A',
                $host->name,
                'N/A',
                '-',
                '-',
                '-',
                '-',
                'Selected'
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        // Generate filename with date range
        $dateRange = ($startDate ?? 'all') . '_to_' . ($endDate ?? 'all');
        $fileName = 'visitor_report_' . $dateRange . '_' . time() . '.csv';

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Public live dashboard view (no authentication required)
     */
    public function liveDashboardPublic()
    {
        // Get all active visits for initial load
        $visits = Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereIn('status', ['pending_host', 'approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vms.backend.visitor.live-dashboard-public', compact('visits'));
    }

    /**
     * Public API endpoint for live dashboard data (no authentication required)
     */
    public function liveVisitorsApiPublic()
    {
        $visits = Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereIn('status', ['pending_host', 'approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($visits);
    }
}
