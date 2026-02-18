@extends('layouts.admin')

@section('title', 'Admin Dashboard - Prime Bank')

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
        <h3 class="fw-800 mb-1 text-white letter-spacing-1">Admin Dashboard</h3>
        <p class="sub-label mb-0">Video Call Support Analytics</p>
    </div>
    <div class="header-profile-box glass-card">
        <div class="avatar bg-primary">
            <i class="fas fa-user-tie text-white small"></i>
        </div>
        <div>
            <p class="small fw-800 mb-0 text-white">{{ Auth::user()->name }}</p>
            <span class="sub-label fs-9">Administrator</span>
        </div>
    </div>
</div>

<!-- Stats Overview Row 1 -->
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

<!-- Metrics Row 2 -->
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
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= round($stats['avg_customer_rating'] ?? 0) ? 'text-warning' : 'text-secondary' }} me-1"></i>
                @endfor
            </div>
        </div>
    </div>
</div>


<!-- Calls in Queue List -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card">
            <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="section-title mb-0"><i class="fas fa-list-ol me-2" style="color: #8b5cf6;"></i>Calls in Queue</h5>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Customer</th>
                                <th>Waiting Since</th>
                                <th>Estimated Wait</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $queueCalls = \App\Models\CallQueue::with(['user'])->where('status', 'waiting')->orderBy('position')->limit(10)->get();
                            @endphp
                            @forelse($queueCalls as $queue)
                            <tr>
                                <td>
                                    <span class="fw-700" style="font-size: 1rem; color: #8b5cf6;">#{{ $queue->position }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(245, 158, 11, 0.2);">
                                            <span class="fw-700 small" style="color: #f59e0b;">{{ substr($queue->user->name ?? $queue->customer_name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <span>{{ $queue->user->name ?? $queue->customer_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>{{ $queue->joined_at ? $queue->joined_at->diffForHumans() : '--' }}</td>
                                <td>
                                    <span class="fw-600" style="color: #f59e0b;">{{ $queue->estimated_wait_time ?? '5-10 min' }}</span>
                                </td>
                                <td>
                                    <span class="badge-glass badge-warning">WAITING</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-check-circle mb-2" style="font-size: 1.5rem; color: #22c55e;"></i>
                                    <p class="mb-0" style="color: #94a3b8;">No calls in queue - All caught up!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agent Status -->
<div class="glass-card mb-4">
    <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h5 class="section-title mb-0"><i class="fas fa-users me-2" style="color: #3b82f6;"></i>Agent Status</h5>
    </div>
    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Calls Today</th>
                        <th>Total Duration</th>
                        <th>Avg Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Agent::all() as $agent)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.2);">
                                    <span class="fw-700 small" style="color: #3b82f6;">{{ substr($agent->name, 0, 1) }}</span>
                                </div>
                                <span>{{ $agent->name }}</span>
                            </div>
                        </td>
                        <td>{{ $agent->department ?? 'General' }}</td>
                        <td>
                            <span class="badge-glass badge-{{ $agent->status === 'free' ? 'success' : ($agent->status === 'busy' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($agent->status) }}
                            </span>
                        </td>
                        <td>{{ $agent->total_calls }}</td>
                        <td>{{ gmdate('H:i:s', $agent->total_duration) }}</td>
                        <td>{{ number_format($agent->average_rating, 1) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Calls -->
<div class="glass-card">
    <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h5 class="section-title mb-0"><i class="fas fa-history me-2" style="color: #8b5cf6;"></i>Recent Calls</h5>
    </div>
    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Started</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Agent</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\CallSession::latest()->take(10)->get() as $session)
                    <tr>
                        <td>{{ $session->started_at->format('d M Y, h:i A') }}</td>
                        <td>{{ gmdate('i:s', $session->duration) }}</td>
                        <td>
                            <span class="badge-glass badge-{{ $session->status === 'ended' ? 'success' : 'warning' }}">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td>{{ $session->agent->name ?? 'N/A' }}</td>
                        <td>
                            @if($session->feedback)
                                <span class="text-warning"><i class="fas fa-star me-1"></i>{{ $session->feedback->rating }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
