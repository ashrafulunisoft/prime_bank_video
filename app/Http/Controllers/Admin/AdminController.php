<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\VisitType;
use App\Models\pragati\Claim;
use App\Notifications\VisitorRegistered;
use App\Services\EmailNotificationService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get Video Call Statistics
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();
        
        // Call counts
        $totalCallsToday = \App\Models\CallSession::whereDate('started_at', $today)->count();
        $totalCallsWeekly = \App\Models\CallSession::where('started_at', '>=', $weekStart)->count();
        $totalCallsMonthly = \App\Models\CallSession::where('started_at', '>=', $monthStart)->count();
        $activeCalls = \App\Models\CallSession::where('status', 'active')->count();
        $callsInQueue = \App\Models\CallQueue::where('status', 'waiting')->count();
        
        // Agent stats
        $totalAgents = \App\Models\Agent::count();
        $freeAgents = \App\Models\Agent::where('status', 'free')->count();
        
        // Duration stats
        $todaySessions = \App\Models\CallSession::whereDate('started_at', $today)->get();
        $totalDurationToday = $todaySessions->sum('duration');
        $totalCallDurationFormatted = gmdate('H:i:s', $totalDurationToday);
        $avgCallDuration = $todaySessions->count() > 0 ? round($todaySessions->avg('duration') / 60) . 'm' : '0m';
        
        // Waiting time stats
        $queueEntries = \App\Models\CallQueue::whereDate('created_at', $today)->get();
        $totalWaitTime = $queueEntries->sum('wait_time');
        $totalWaitingTimeFormatted = floor($totalWaitTime / 3600) . 'h ' . floor(($totalWaitTime % 3600) / 60) . 'm';
        $avgWaitingTime = $queueEntries->count() > 0 ? round($queueEntries->avg('wait_time') / 60) . 'm' : '0m';
        
        // Rating stats
        $todayFeedback = \App\Models\CallFeedback::whereDate('created_at', $today);
        $avgRating = $todayFeedback->count() > 0 ? round($todayFeedback->avg('rating'), 1) : '0.0';
        $totalFeedback = $todayFeedback->count();
        
        // Satisfaction rate (ratings >= 4)
        $satisfiedCount = \App\Models\CallFeedback::whereDate('created_at', $today)->where('rating', '>=', 4)->count();
        $satisfactionRate = $totalFeedback > 0 ? round(($satisfiedCount / $totalFeedback) * 100) . '%' : '0%';

        // Get statistics for dashboard
        $stats = [
            // Video Call Stats
            'total_calls_today' => $totalCallsToday,
            'total_calls_weekly' => $totalCallsWeekly,
            'total_calls_monthly' => $totalCallsMonthly,
            'active_calls' => $activeCalls,
            'calls_in_queue' => $callsInQueue,
            'total_agents' => $totalAgents,
            'free_agents' => $freeAgents,
            'total_call_duration_formatted' => $totalCallDurationFormatted,
            'avg_call_duration' => $avgCallDuration,
            'total_waiting_time' => $totalWaitingTimeFormatted,
            'avg_waiting_time' => $avgWaitingTime,
            'avg_customer_rating' => $avgRating,
            'total_feedback' => $totalFeedback,
            'satisfaction_rate' => $satisfactionRate,
            
            // Visitor Management Stats (kept for compatibility)
            'total_visitors' => \App\Models\Visitor::count(),
            'total_visits' => \App\Models\Visit::count(),
            'pending_visits' => \App\Models\Visit::where('status', 'pending_host')->count(),
            'approved_visits' => \App\Models\Visit::where('status', 'approved')->count(),
            'completed_visits' => \App\Models\Visit::where('status', 'completed')->count(),
            'rejected_visits' => \App\Models\Visit::where('status', 'rejected')->count(),
            'checked_in_visits' => \App\Models\Visit::where('status', 'checked_in')->count(),
            'visits_today' => \App\Models\Visit::whereDate('schedule_time', today())->count(),
            'visits_this_month' => \App\Models\Visit::whereMonth('schedule_time', now()->month)
                ->whereYear('schedule_time', now()->year)
                ->count(),
            
            // User Statistics
            'total_users' => \App\Models\User::count(),
            
            // Policy/Order Statistics
            'total_policies' => \App\Models\pragati\Order::count(),
            'active_policies' => \App\Models\pragati\Order::where('status', 'active')->count(),
            'pending_policies' => \App\Models\pragati\Order::where('status', 'pending')->count(),
            'expired_policies' => \App\Models\pragati\Order::where('status', 'expired')->count(),
            
            // Claim Statistics
            'total_claims' => \App\Models\pragati\Claim::count(),
            'pending_claims' => \App\Models\pragati\Claim::where('status', 'pending')->count(),
            'approved_claims' => \App\Models\pragati\Claim::where('status', 'approved')->count(),
            'rejected_claims' => \App\Models\pragati\Claim::where('status', 'rejected')->count(),
        ];

        // Get today's visits
        $todayVisits = \App\Models\Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereDate('schedule_time', today())
            ->orderBy('schedule_time', 'desc')
            ->limit(10)
            ->get();

        // Get pending visits
        $pendingVisits = \App\Models\Visit::with(['visitor', 'meetingUser', 'type'])
            ->where('status', 'pending_host')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent visits
        $recentVisits = \App\Models\Visit::with(['visitor', 'meetingUser', 'type'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('vms.backend.admin.admin_dashboard', compact('stats', 'todayVisits', 'pendingVisits', 'recentVisits'));
    }

    /**
     * Display admin profile page
     */
    public function profile()
    {
        $user = auth()->user();
        return view('vms.backend.admin.profile', compact('user'));
    }

    /**
     * Admin live dashboard view
     */
    public function liveDashboard()
    {
        // Get all active visits for initial load
        $visits = Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereIn('status', ['pending_host', 'approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vms.backend.admin.live-dashboard', compact('visits'));
    }

    /**
     * API endpoint for admin live dashboard data
     */
    public function liveVisitsApi()
    {
        $visits = Visit::with(['visitor', 'meetingUser', 'type'])
            ->whereIn('status', ['pending_host', 'approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($visits);
    }

    public function createRole(){
        return view('vms.backend.admin.Addrole');
    }

    public function storeRole(Request $request){
        $request->validate([
            'role_name' => 'required|string|unique:roles,name|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,restricted',
        ]);

        $role = Role::create([
            'name' => $request->role_name,
        ]);

        // If permissions are selected, sync them to the role
        if ($request->has('permissions')) {
            $permissions = [];

            if (in_array('dashboard', $request->permissions)) {
                $permissions[] = Permission::firstOrCreate(['name' => 'view dashboard']);
            }
            if (in_array('users', $request->permissions)) {
                $permissions[] = Permission::firstOrCreate(['name' => 'manage users']);
            }
            if (in_array('roles', $request->permissions)) {
                $permissions[] = Permission::firstOrCreate(['name' => 'manage roles']);
            }
            if (in_array('reports', $request->permissions)) {
                $permissions[] = Permission::firstOrCreate(['name' => 'view reports']);
            }
            if (in_array('audit', $request->permissions)) {
                $permissions[] = Permission::firstOrCreate(['name' => 'view audit logs']);
            }
            if (in_array('settings', $request->permissions)) {
                $permissions[] = Permission::firstOrCreate(['name' => 'manage settings']);
            }

            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.role.create')
            ->with('success', 'Role "' . $request->role_name . '" created successfully!');
    }

    public function createAssignRole(){
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::all();
        return view('vms.backend.admin.Assignrole', compact('users', 'roles', 'permissions'));
    }

    public function storeAssignRole(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'effective_date' => 'nullable|date',
            'status' => 'required|in:active,pending,restricted',
            'remarks' => 'nullable|string|max:500',
        ]);

        $user = User::find($request->user_id);
        $role = Role::find($request->role_id);

        // Remove existing roles and assign new one
        $user->syncRoles([$role->id]);

        // Assign permissions if selected
        if ($request->has('permissions') && is_array($request->permissions)) {
            // Convert permission IDs to Permission models
            $permissionModels = Permission::whereIn('id', $request->permissions)->get();
            $user->syncPermissions($permissionModels);
        } else {
            // Remove all permissions if none selected
            $user->syncPermissions([]);
        }

        return redirect()->route('admin.role.assign.create')
            ->with('success', 'Role "' . $role->name . '" with permissions assigned to ' . $user->name . ' successfully!');
    }

    public function removeUserRole(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $roles = $user->getRoleNames();
        $user->removeRole($roles->first());

        return redirect()->route('admin.role.assign.create')
            ->with('success', 'Role removed from ' . $user->name . ' successfully!');
    }

    public function createVisitorRegistration(){
        $users = User::all();
        $visitTypes = VisitType::all();
        return view('vms.backend.admin.VisitorRegistration', compact('users', 'visitTypes'));
    }

    public function storeVisitorRegistration(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:visitors,email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'host_name' => 'required|string|max:255',
            'purpose' => 'required|string|max:500',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_type_id' => 'required|exists:visit_types,id',
            'face_image' => 'nullable|string',
        ]);

        // Log the start of visitor registration process
        Log::info('Starting visitor registration process', [
            'admin_name' => Auth::user()->name ?? 'System',
            'admin_email' => Auth::user()->email ?? 'N/A',
            'visitor_name' => $request->name,
            'visitor_email' => $request->email,
            'visit_date' => $request->visit_date,
            'ip_address' => $request->ip(),
            'timestamp' => now()->toDateTimeString()
        ]);

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

            Log::info('Visitor record created/retrieved', [
                'visitor_id' => $visitor->id,
                'visitor_name' => $visitor->name,
                'visitor_email' => $visitor->email,
                'is_new_visitor' => $visitor->wasRecentlyCreated ?? false
            ]);

            // Find or create host user by name
            $hostUser = User::where('name', 'like', '%' . $request->host_name . '%')->first();

            if (!$hostUser) {
                // If host doesn't exist, use current admin as default host
                $hostUser = Auth::user();
                Log::warning('Host not found, using current admin as default host', [
                    'requested_host' => $request->host_name,
                    'default_host' => $hostUser->name,
                    'default_host_id' => $hostUser->id
                ]);
            }

            // Create visit record
            $visit = Visit::create([
                'visitor_id' => $visitor->id,
                'meeting_user_id' => $hostUser->id,
                'visit_type_id' => $request->visit_type_id,
                'purpose' => $request->purpose,
                'schedule_time' => $request->visit_date,
                'status' => 'approved', // Auto-approve when created by admin
                'approved_at' => now(),
            ]);

            Log::info('Visit record created successfully', [
                'visit_id' => $visit->id,
                'visitor_id' => $visit->visitor_id,
                'host_id' => $visit->meeting_user_id,
                'visit_type_id' => $visit->visit_type_id,
                'schedule_time' => $visit->schedule_time,
                'status' => $visit->status,
                'approved_at' => $visit->approved_at
            ]);

            // Prepare email data
            $emailData = [
                'visitor_name' => $visitor->name,
                'visitor_email' => $visitor->email,
                'visitor_phone' => $visitor->phone,
                'visitor_company' => $visitor->address,
                'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                'visit_type' => $visit->type->name ?? 'N/A',
                'purpose' => $visit->purpose,
                'host_name' => $hostUser->name,
                'status' => $visit->status,
            ];

            // Use EmailNotificationService to send email
            $emailService = new EmailNotificationService();
            $emailSent = $emailService->sendVisitorRegistrationEmail($emailData);

            if ($emailSent) {
                Log::info('Visitor registration email sent successfully', [
                    'visit_id' => $visit->id,
                    'visitor_email' => $visitor->email,
                    'sent_at' => now()->toDateTimeString()
                ]);
            } else {
                Log::error('Failed to send visitor registration email', [
                    'visit_id' => $visit->id,
                    'visitor_email' => $visitor->email
                ]);
            }

            // Send SMS notification if phone number exists
            if ($visitor->phone) {
                // Prepare SMS data
                $smsData = [
                    'visitor_name' => $visitor->name,
                    'visitor_phone' => $visitor->phone,
                    'visitor_email' => $visitor->email,
                    'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                    'visit_type' => $visit->type->name ?? 'N/A',
                    'host_name' => $hostUser->name,
                    'status' => $visit->status,
                ];

                // Prepare SMS message
                $smsMessage = "Dear {$visitor->name}, Your visit to UCB Bank is confirmed for " .
                              \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A') .
                              ". Host: {$hostUser->name}. Status: {$visit->status}. Thank you!";

                // Use SmsNotificationService to send SMS immediately (synchronous)
                $smsService = new SmsNotificationService();

                // Format phone number to 880XXXXXXXXXX format
                $phone = $visitor->phone;
                // Remove +, spaces, and ensure starts with 880
                $phone = preg_replace('/[^0-9]/', '', $phone);
                if (strpos($phone, '880') !== 0) {
                    $phone = '88' . $phone;
                }

                $smsResult = $smsService->send($phone, $smsMessage);
                $smsSent = $smsResult['success'] ?? false;

                if ($smsSent) {
                    Log::info('SMS notification sent successfully', [
                        'visit_id' => $visit->id,
                        'visitor_phone' => $phone,
                        'message_id' => $smsResult['message_id'] ?? 'N/A',
                        'sent_at' => now()->toDateTimeString()
                    ]);
                } else {
                    Log::error('Failed to send SMS notification', [
                        'visit_id' => $visit->id,
                        'visitor_phone' => $phone,
                        'error' => $smsResult['message'] ?? 'Unknown error'
                    ]);
                }
            }

            // Log successful completion of visitor registration
            Log::info('Visitor registration completed successfully', [
                'visitor_id' => $visitor->id,
                'visit_id' => $visit->id,
                'visitor_name' => $visitor->name,
                'visitor_email' => $visitor->email,
                'visit_date' => $visit->schedule_time,
                'host_name' => $hostUser->name,
                'status' => $visit->status,
                'email_sent' => $emailSent,
                'sms_sent' => ($visitor->phone && config('sms.enabled')) ? 'attempted' : 'skipped',
                'registered_by' => Auth::user()->name ?? 'System',
                'completed_at' => now()->toDateTimeString()
            ]);

            return redirect()->route('admin.visitor.registration.create')
                ->with('success', 'Visitor ' . $visitor->name . ' registered successfully!');

        } catch (\Exception $e) {
            // Log error during visitor registration
            Log::error('Error during visitor registration', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'visitor_name' => $request->name ?? 'N/A',
                'visitor_email' => $request->email ?? 'N/A',
                'trace' => $e->getTraceAsString(),
                'occurred_at' => now()->toDateTimeString()
            ]);

            return back()->with('error', 'Failed to register visitor: ' . $e->getMessage())->withInput();
        }
    }

    public function searchHost(Request $request)
    {
        $query = $request->get('q');
        $users = User::where('name', 'like', '%' . $query . '%')
                    ->limit(10)
                    ->get(['id', 'name']);

        return response()->json($users);
    }

    public function checkVisitor(Request $request)
    {
        $email = $request->get('email');
        $visitor = Visitor::where('email', $email)->first();

        if ($visitor) {
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

        return response()->json(['success' => false]);
    }

    public function checkVisitorByPhone(Request $request)
    {
        $phone = $request->get('phone');
        $visitor = Visitor::where('phone', $phone)->first();

        if ($visitor) {
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

        return response()->json(['success' => false]);
    }

    public function visitorList()
    {
        $visitors = Visit::with(['visitor', 'type', 'meetingUser'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        return view('vms.backend.admin.visitor-list', compact('visitors'));
    }

    public function editVisitor($id)
    {
        $visit = Visit::with(['visitor', 'type', 'meetingUser'])->findOrFail($id);
        $users = User::all();
        $visitTypes = VisitType::all();

        return view('vms.backend.admin.edit-visitor', compact('visit', 'users', 'visitTypes'));
    }

    public function updateVisitor(Request $request, $id)
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
            'status' => 'required|in:approved,pending,completed,cancelled',
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
            if (!$hostUser) {
                $hostUser = Auth::user();
            }

            // Update visit
            $oldStatus = $visit->status;
            $visit->update([
                'meeting_user_id' => $hostUser->id,
                'visit_type_id' => $request->visit_type_id,
                'purpose' => $request->purpose,
                'schedule_time' => $request->visit_date,
                'status' => $request->status,
                'approved_at' => $request->status === 'approved' ? now() : $visit->approved_at,
            ]);

            // Log visit update
            Log::info('Visit details updated', [
                'visit_id' => $visit->id,
                'visitor_name' => $visitor->name,
                'old_status' => $oldStatus,
                'new_status' => $visit->status,
                'updated_by' => Auth::user()->name ?? 'System',
                'updated_at' => now()->toDateTimeString()
            ]);

            // Send status update email if status changed
            if ($oldStatus !== $visit->status) {
                // Send email notification
                $emailData = [
                    'visitor_name' => $visitor->name,
                    'visitor_email' => $visitor->email,
                    'visitor_company' => $visitor->address,
                    'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                    'visit_type' => $visit->type->name ?? 'N/A',
                    'purpose' => $visit->purpose,
                    'host_name' => $hostUser->name,
                    'status' => $visit->status,
                    'remarks' => $request->remarks ?? null,
                ];

                $emailService = new EmailNotificationService();
                $emailSent = $emailService->sendVisitStatusEmail($emailData);

                if ($emailSent) {
                    Log::info('Visit status email sent successfully', [
                        'visit_id' => $visit->id,
                        'visitor_email' => $visitor->email,
                        'status' => $visit->status,
                        'sent_at' => now()->toDateTimeString()
                    ]);
                } else {
                    Log::error('Failed to send visit status email', [
                        'visit_id' => $visit->id,
                        'visitor_email' => $visitor->email,
                        'status' => $visit->status
                    ]);
                }

                // Send SMS notification if phone number exists
                if ($visitor->phone) {
                    // Prepare SMS message
                    $statusMessages = [
                        'approved' => 'Your visit has been approved.',
                        'completed' => 'Your visit has been completed.',
                        'cancelled' => 'Your visit has been cancelled.',
                        'pending' => 'Your visit is pending approval.',
                        'rejected' => 'Your visit has been rejected.',
                    ];

                    $statusMessage = $statusMessages[$visit->status] ?? "Your visit status is: " . ucfirst($visit->status);
                    $smsMessage = "Dear {$visitor->name}, {$statusMessage} Thank you!";

                    // Format phone number to 880XXXXXXXXXX format
                    $phone = $visitor->phone;
                    $phone = preg_replace('/[^0-9]/', '', $phone);
                    if (strpos($phone, '880') !== 0) {
                        $phone = '88' . $phone;
                    }

                    $smsService = new SmsNotificationService();
                    $smsResult = $smsService->send($phone, $smsMessage);
                    $smsSent = $smsResult['success'] ?? false;

                    if ($smsSent) {
                        Log::info('Visit status SMS sent successfully', [
                            'visit_id' => $visit->id,
                            'visitor_phone' => $phone,
                            'status' => $visit->status,
                            'message_id' => $smsResult['message_id'] ?? 'N/A',
                            'sent_at' => now()->toDateTimeString()
                        ]);
                    } else {
                        Log::error('Failed to send visit status SMS', [
                            'visit_id' => $visit->id,
                            'visitor_phone' => $phone,
                            'status' => $visit->status,
                            'error' => $smsResult['message'] ?? 'Unknown error'
                        ]);
                    }
                }
            }

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Visit updated successfully!'
                ]);
            }

            return redirect()->route('admin.visitor.list')
                ->with('success', 'Visit updated successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteVisitor($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visit deleted successfully!'
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

        return view('vms.backend.admin.pending', compact('visits'));
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

        return view('vms.backend.admin.rejected', compact('visits'));
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

        return view('vms.backend.admin.approved', compact('visits'));
    }

    /**
     * Display visit history
     */
    public function visitHistory()
    {
        $visits = Visit::with(['visitor', 'type', 'meetingUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vms.backend.admin.history', compact('visits'));
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

        return view('vms.backend.admin.active', compact('visits'));
    }

    /**
     * Display check-in/check-out panel
     */
    public function checkinCheckout()
    {
        $visits = Visit::with(['visitor', 'type', 'meetingUser'])
            ->whereIn('status', ['approved', 'checked_in'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vms.backend.admin.checkin-checkout', compact('visits'));
    }

    /**
     * Auto-fill visitor details from previous visits
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
     * Check visitor by email
     */
    public function checkVisitorByEmail(Request $request)
    {
        $email = $request->get('email');
        $visitor = Visitor::where('email', $email)->first();

        if ($visitor) {
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

        return response()->json(['success' => false]);
    }

    /**
     * Get visitor statistics
     */
    public function statistics()
    {
        $stats = [
            'total_visitors' => Visitor::count(),
            'total_visits' => Visit::count(),
            'pending_visits' => Visit::where('status', 'pending_host')->count(),
            'approved_visits' => Visit::where('status', 'approved')->count(),
            'completed_visits' => Visit::where('status', 'completed')->count(),
            'rejected_visits' => Visit::where('status', 'rejected')->count(),
            'checked_in_visits' => Visit::where('status', 'checked_in')->count(),
            'visits_today' => Visit::whereDate('schedule_time', today())->count(),
            'visits_this_month' => Visit::whereMonth('schedule_time', now()->month)
                                         ->whereYear('schedule_time', now()->year)
                                         ->count(),
        ];

        return view('vms.backend.admin.statistics', compact('stats'));
    }

    /**
     * Display specified visitor
     */
    public function showVisitor($id)
    {
        $visit = Visit::with(['visitor', 'type', 'meetingUser'])->findOrFail($id);
        return view('vms.backend.admin.show', compact('visit'));
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp($id)
    {
        $visit = Visit::with('visitor')->findOrFail($id);
        return view('vms.backend.admin.verify-otp', compact('visit'));
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

        $visit->update([
            'otp' => null,
            'otp_verified_at' => now(),
            'status' => 'pending_host',
        ]);

        broadcast(new \App\Events\VisitWaitingForApproval($visit));

        try {
            Mail::to($visit->meetingUser->email)->send(new \App\Mail\VisitApprovalRequestEmail([
                'host_name' => $visit->meetingUser->name,
                'visitor_name' => $visit->visitor->name,
                'visitor_email' => $visit->visitor->email,
                'visitor_phone' => $visit->visitor->phone,
                'purpose' => $visit->purpose,
                'visit_date' => \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A'),
                'visit_type' => $visit->type->name ?? 'N/A',
                'approval_link' => route('admin.visitor.show', $visit->id),
            ]));
        } catch (\Exception $e) {
            Log::error('Failed to send host approval email', [
                'error' => $e->getMessage(),
                'visit_id' => $visit->id,
            ]);
        }

        return redirect()->route('admin.visitor.show', $visit->id)
            ->with('success', 'OTP verified successfully. Your visit is now waiting for host approval.');
    }

    /**
     * Approve visit and generate RFID
     */
    public function approveVisit($id)
    {
        try {
            $visit = Visit::with(['visitor', 'meetingUser'])->findOrFail($id);
            $rfid = 'RFID-' . strtoupper(\Illuminate\Support\Str::random(8));

            $visit->update([
                'status' => 'approved',
                'rfid' => $rfid,
                'approved_at' => now(),
            ]);

            broadcast(new \App\Events\VisitApproved($visit));

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
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $visit = Visit::with(['visitor', 'meetingUser'])->findOrFail($id);

            $visit->update([
                'status' => 'rejected',
                'rejected_reason' => $validated['reason'],
            ]);

            broadcast(new \App\Events\VisitRejected($visit));

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

            return response()->json([
                'success' => true,
                'message' => 'Visit rejected successfully.'
            ])->header('Content-Type', 'application/json');
        } catch (\Illuminate\Validation\ValidationException $e) {
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

            broadcast(new \App\Events\VisitCheckedIn($visit));

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
        try {
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

            broadcast(new \App\Events\VisitCompleted($visit));

            return response()->json([
                'success' => true,
                'message' => 'Visitor checked out successfully.',
                'checkout_time' => $visit->checkout_time->format('h:i A'),
            ]);
        } catch (\Exception $e) {
            Log::error('Check-out error', [
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
     * Display policy list (Order = Policy)
     */
    public function policyList()
    {
        $orders = \App\Models\pragati\Order::with(['user', 'package'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.policies.index', compact('orders'));
    }

    /**
     * Display policy details
     */
    public function policyShow($id)
    {
        $order = \App\Models\pragati\Order::with(['user', 'package', 'claims'])
            ->findOrFail($id);

        return view('admin.policies.show', compact('order'));
    }

    /**
     * Display claim list
     */
    public function claimList()
    {
        $claims = Claim::with(['user', 'package', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.claims.index', compact('claims'));
    }

    /**
     * Display claim details
     */
    public function claimShow($id)
    {
        $claim = Claim::with(['user', 'package', 'order'])
            ->findOrFail($id);

        return view('admin.claims.show', compact('claim'));
    }
}
