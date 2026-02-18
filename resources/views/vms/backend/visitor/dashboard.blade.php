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

    @if(auth()->user()->hasRole('visitor'))
    <!-- Video Call Section - Only for Visitors/Customers -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-800 sub-label mb-1">🎥 Video Call Support</h6>
                        <p class="text-white small mb-0">Connect with a customer care representative via video call</p>
                    </div>
                    <a href="{{ route('video.call') }}" class="btn btn-primary">
                        <i class="fas fa-video me-2"></i>Start Video Call
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Insurance Stats Row -->
    @if(isset($insuranceStats) && ($insuranceStats['total_policies'] > 0 || $insuranceStats['total_claims'] > 0))
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

    <!-- Stats Row - Based on permissions -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl">
            <div class="glass-card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['total_visitors'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Total Visitors</p>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="glass-card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['visits_today'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Today's Visits</p>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="glass-card stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['pending_visits'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Pending</p>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="glass-card stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="fw-800 mb-1" style="font-size: 2rem; color: #fff;">{{ $stats['active_visits'] }}</h3>
                <p style="color: #94a3b8; margin-bottom: 0;">Active Visits</p>
            </div>
        </div>
        <div class="col-12 col-xl">
            @can('create visitors')
            <a href="{{ route('visitor.create') }}" class="glass-card stat-card justify-content-center cursor-pointer border-dashed text-decoration-none" style="border-width: 2px;">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-plus"></i>
                    <span class="fw-bold text-uppercase fs-9">Register New Visitor</span>
                </div>
            </a>
            @else
            <div class="glass-card stat-card justify-content-center border-dashed" style="border-width: 2px;">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-lock"></i>
                    <span class="fw-bold text-uppercase fs-9">No Permission to Register Visitors</span>
                </div>
            </div>
            @endcan
        </div>
    </div>

    <!-- My Policies Section -->
    @if(isset($userOrders) && $userOrders->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card">
                <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="section-title mb-0"><i class="fas fa-file-contract me-2" style="color: #3b82f6;"></i>My Insurance Policies</h5>
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('orders.index') }}" class="btn-glass">
                        <i class="fas fa-external-link-alt me-1"></i>View All
                    </a>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Policy #</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userOrders as $order)
                                <tr>
                                    <td>
                                        <span class="fw-bold" style="font-family: 'Courier New', monospace; color: #fff;">
                                            {{ $order->policy_number }}
                                        </span>
                                    </td>
                                    <td class="text-white">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        {{ $order->package->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge-glass badge-{{ $order->status == 'active' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">
                                            @php
                                                $orderStatus = ucfirst($order->status);
                                            @endphp
                                            {{ $orderStatus }}
                                        </span>
                                    </td>
                                    <td class="text-white">{{ $order->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-circle text-info" title="View Details">
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

    <!-- My Claims Section -->
    @if(isset($userClaims) && $userClaims->count() > 0)
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
