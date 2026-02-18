@extends('layouts.receptionist')


@section('title', 'Dashboard - Prime Bank')

@section('content')
<style>
    .glass-card {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }
    .summary-card {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), 0 0 30px rgba(59, 130, 246, 0.2);
    }
    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    .stat-card {
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), 0 0 30px rgba(59, 130, 246, 0.2);
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .stat-icon.primary { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .stat-icon.success { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .stat-icon.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .stat-icon.info { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .stat-icon.pink { background: rgba(219, 39, 119, 0.15); color: #db2777; }

    .btn-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        transition: all 0.3s;
        text-decoration: none;
    }
    .btn-glass:hover, .btn-glass.active {
        background: rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.5);
        color: #fff;
    }

    .table-dark-custom {
        color: #fff !important;
    }
    .table-dark-custom th {
        color: #94a3b8 !important;
        font-weight: 600;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem;
    }
    .table-dark-custom td {
        color: #fff !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem;
        vertical-align: middle;
    }
    .table-dark-custom tbody tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .badge-glass {
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-success { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .badge-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .badge-warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .badge-secondary { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }

    .log-container {
        max-height: 400px;
        overflow-y: auto;
    }
    .log-container::-webkit-scrollbar {
        width: 6px;
    }
    .log-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }
    .log-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }
    .log-container::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.05);
    }
    .border-dashed {
        border-style: dashed !important;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff !important;
        margin-bottom: 1rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }
    .page-header h2 {
        font-weight: 800;
        font-size: 1.75rem;
        color: #fff !important;
    }
</style>

    <!-- Header -->
    <div class="header-section">
        <div>
            <h3 class="fw-800 mb-1 text-white letter-spacing-1">
                @php
                    $dashboardTitle = 'Visitor Dashboard';
                    if(auth()->user()->hasRole('receptionist')) {
                        $dashboardTitle = 'Receptionist Dashboard';
                    } elseif(auth()->user()->hasRole('staff')) {
                        $dashboardTitle = 'Staff Dashboard';
                    }
                @endphp
                {{ $dashboardTitle }}
            </h3>
            <p class="sub-label mb-0">Welcome back, {{ Auth::user()->name }}</p>
        </div>
        <div class="header-profile-box glass-card">
            <div class="avatar bg-primary">
                <i class="fas fa-user-tie text-white small"></i>
            </div>
            <div>
                <p class="small fw-800 mb-0 text-white">{{ Auth::user()->name }}</p>
                <span class="sub-label fs-9">
                    @php
                        $roleName = ucfirst(auth()->user()->getRoleNames()->first()) ?? 'User';
                    @endphp
                    {{ $roleName }}
                </span>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasRole('visitor') || auth()->user()->hasRole('receptionist'))
    <!-- Stats Overview Row 1 - Video Call Analytics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['total_calls_today'] ?? 0 }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Total Calls Today</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['active_calls'] ?? 0 }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Active Calls</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-list-ol"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['calls_in_queue'] ?? 0 }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Calls in Queue</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-user-headset"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['free_agents'] ?? 0 }}/{{ $stats['total_agents'] ?? 0 }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Free Agents</p>
            </div>
        </div>
    </div>

    <!-- Metrics Row 2 - Video Call Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="fw-800 mb-1" style="color: #fff;">{{ $stats['total_call_duration_formatted'] ?? '00:00:00' }}</h4>
                <p style="color: #94a3b8; margin-bottom: 0.5rem;">Total Duration</p>
                <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <span class="small" style="color: #94a3b8;">Avg Duration</span>
                    <span class="fw-600" style="color: #fff;">{{ $stats['avg_call_duration'] ?? '0m' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h4 class="fw-800 mb-1" style="color: #fff;">{{ $stats['avg_waiting_time'] ?? '0s' }}</h4>
                <p style="color: #94a3b8; margin-bottom: 0.5rem;">Average Wait Time</p>
                <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <span class="small" style="color: #94a3b8;">Total Wait</span>
                    <span class="fw-600" style="color: #fff;">{{ $stats['total_waiting_time'] ?? '0h 0m' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card stat-card">
                <div class="stat-icon pink">
                    <i class="fas fa-star"></i>
                </div>
                <h4 class="fw-800 mb-1" style="color: #fff;">{{ $stats['avg_customer_rating'] ?? '0.0' }} <small style="color: #94a3b8;">/ 5</small></h4>
                <p style="color: #94a3b8; margin-bottom: 0.5rem;">Average Rating</p>
                <div class="d-flex justify-content-center pt-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    @php
                        $rating = round($stats['avg_customer_rating'] ?? 0);
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-secondary' }} me-1"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    @endif


    <!-- Insurance Stats Row (only for visitors) -->
    @if(auth()->user()->hasRole('visitor') && isset($insuranceStats) && ($insuranceStats['total_policies'] > 0 || $insuranceStats['total_claims'] > 0))
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $insuranceStats['total_policies'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Total Policies</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $insuranceStats['active_policies'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Active Policies</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $insuranceStats['total_claims'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Total Claims</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $insuranceStats['pending_claims'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Pending Claims</p>
            </div>
        </div>
    </div>
    @endif


    <!-- Today's Visits (from Receptionist Dashboard) -->
    @if($todayVisits->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h6 class="fw-800 sub-label mb-4">Today's Visits</h6>
                <div class="table-responsive log-container">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Host</th>
                                <th>Visit Type</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayVisits as $visit)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <span class="fw-800 small">{{ substr($visit->visitor->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="small fw-800 d-block">{{ $visit->visitor->name }}</span>
                                            <span class="fs-9 text-white-50">{{ $visit->visitor->phone ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $visit->meetingUser->name }}</td>
                                <td class="small">{{ $visit->type->name ?? 'N/A' }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($visit->schedule_time)->format('g:i A') }}</td>
                                <td>
                                    @if($visit->status == 'approved')
                                        <span class="status-badge" style="color: var(--accent-emerald);">Active</span>
                                    @elseif($visit->status == 'pending')
                                        <span class="status-badge" style="color: var(--accent-amber);">Pending</span>
                                    @elseif($visit->status == 'completed')
                                        <span class="status-badge">Completed</span>
                                    @else
                                        <span class="status-badge text-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('edit visitors')
                                        <a href="{{ route('visitor.edit', $visit->id) }}" class="btn btn-circle" style="color: var(--accent-emerald);" title="Edit">
                                            <i class="fas fa-edit small"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

 
    <!-- My Calls in Queue -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card">
                <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="section-title mb-0"><i class="fas fa-list-ol me-2" style="color: #8b5cf6;"></i>My Queue Position</h5>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Waiting Since</th>
                                    <th>Estimated Wait</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $myQueuePosition = \App\Models\CallQueue::where('user_id', auth()->id())->where('status', 'waiting')->first();
                                @endphp
                                @if($myQueuePosition)
                                    <tr>
                                        <td>
                                            <span class="fw-700" style="font-size: 1rem; color: #8b5cf6;">#{{ $myQueuePosition->position ?? 0 }}</span>
                                        </td>
                                        <td>{{ $myQueuePosition->joined_at ? $myQueuePosition->joined_at->diffForHumans() : '--' }}</td>
                                        <td>
                                            <span class="fw-600" style="color: #f59e0b;">{{ $myQueuePosition->estimated_wait_time ?? '5-10 min' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-glass badge-warning">WAITING</span>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="fas fa-check-circle mb-2" style="font-size: 1.5rem; color: #22c55e;"></i>
                                            <p class="mb-0" style="color: #94a3b8;">You are not in the queue</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Claims Section (only for visitors) -->
    @if(auth()->user()->hasRole('visitor') && isset($userClaims) && $userClaims->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card">
                <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="section-title mb-0"><i class="fas fa-clipboard-list me-2" style="color: #f59e0b;"></i>My Insurance Claims</h5>
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('claims.index') }}" class="btn-glass">
                        <i class="fas fa-external-link-alt me-1"></i>View All
                    </a>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Claim #</th>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userClaims as $claim)
                                <tr>
                                    <td>
                                        <span class="fw-bold" style="font-family: 'Courier New', monospace; color: #fff;">
                                            {{ $claim->claim_number }}
                                        </span>
                                    </td>
                                    <td class="text-white">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        {{ $claim->package->name ?? 'N/A' }}
                                    </td>
                                    <td class="fw-bold">${{ number_format($claim->claim_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $claimStatusClass = 'secondary';
                                            if($claim->status == 'approved') {
                                                $claimStatusClass = 'success';
                                            } elseif($claim->status == 'under_review') {
                                                $claimStatusClass = 'warning';
                                            } elseif($claim->status == 'submitted') {
                                                $claimStatusClass = 'info';
                                            }
                                        @endphp
                                        <span class="badge-glass badge-{{ $claimStatusClass }}">
                                            @php
                                                $claimStatusLabel = ucfirst(str_replace('_', ' ', $claim->status));
                                            @endphp
                                            {{ $claimStatusLabel }}
                                        </span>
                                    </td>
                                    <td class="text-white">{{ $claim->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <a href="{{ route('claims.show', $claim->id) }}" class="btn btn-circle text-info" title="View Details">
                                            <i class="fas fa-eye small"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


@endsection
