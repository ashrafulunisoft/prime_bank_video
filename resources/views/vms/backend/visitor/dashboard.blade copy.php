@extends('layouts.receptionist')

@section('title', 'Dashboard - UCB Bank')

@section('content')
    <!-- Header -->
    <div class="header-section">
        <div>
            <h3 class="fw-800 mb-1 text-white letter-spacing-1">
                {{ auth()->user()->hasRole('receptionist') ? 'Receptionist Dashboard' : (auth()->user()->hasRole('staff') ? 'Staff Dashboard' : 'Visitor Dashboard') }}
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
                    {{ ucfirst(auth()->user()->getRoleNames()->first()) ?? 'User' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Row - Based on permissions -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl">
            <div class="glass-card summary-card">
                <div>
                    <span class="sub-label d-block mb-1">Total Visitors</span>
                    <h2>{{ $stats['total_visitors'] }}</h2>
                </div>
                <div class="summary-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="glass-card summary-card">
                <div>
                    <span class="sub-label d-block mb-1">Today's Visits</span>
                    <h2>{{ $stats['visits_today'] }}</h2>
                </div>
                <div class="summary-icon"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="glass-card summary-card">
                <div>
                    <span class="sub-label d-block mb-1">Pending</span>
                    <h2>{{ $stats['pending_visits'] }}</h2>
                </div>
                <div class="summary-icon text-warning" style="background:rgba(255,193,7,0.1)"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="glass-card summary-card">
                <div>
                    <span class="sub-label d-block mb-1">Active Visits</span>
                    <h2>{{ $stats['active_visits'] }}</h2>
                </div>
                <div class="summary-icon text-success" style="background:rgba(34,197,94,0.1)"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
        <div class="col-12 col-xl">
            @can('create visitors')
            <a href="{{ route('visitor.create') }}" class="glass-card summary-card justify-content-center cursor-pointer border-dashed text-decoration-none" style="border-width: 2px;">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-plus"></i>
                    <span class="fw-bold text-uppercase fs-9">Register New Visitor</span>
                </div>
            </a>
            @else
            <div class="glass-card summary-card justify-content-center border-dashed" style="border-width: 2px;">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-lock"></i>
                    <span class="fw-bold text-uppercase fs-9">No Permission to Register Visitors</span>
                </div>
            </div>
            @endcan
        </div>
    </div>

    <!-- Today's Visits -->
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
                                        <span class="status-badge text-success">Active</span>
                                    @elseif($visit->status == 'pending')
                                        <span class="status-badge text-warning">Pending</span>
                                    @elseif($visit->status == 'completed')
                                        <span class="status-badge">Completed</span>
                                    @else
                                        <span class="status-badge text-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('visitor.show', $visit->id) }}" class="btn btn-circle text-info" title="View Details">
                                            <i class="fas fa-eye small"></i>
                                        </a>
                                        @can('edit visitors')
                                        <a href="{{ route('visitor.edit', $visit->id) }}" class="btn btn-circle text-primary" title="Edit">
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

    <!-- Pending Visits -->
    @if($pendingVisits->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h6 class="fw-800 sub-label mb-4">Pending Approvals</h6>
                <div class="table-responsive log-container">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Purpose</th>
                                <th>Host</th>
                                <th>Requested</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingVisits as $visit)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <span class="fw-800 small">{{ substr($visit->visitor->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="small fw-800 d-block">{{ $visit->visitor->name }}</span>
                                            <span class="fs-9 text-white-50">{{ $visit->visitor->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ substr($visit->purpose, 0, 30) }}...</td>
                                <td class="small">{{ $visit->meetingUser->name }}</td>
                                <td class="small">{{ $visit->created_at->diffForHumans() }}</td>
                                <td>
                                    <span class="status-badge text-warning border-orange">Pending</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('visitor.show', $visit->id) }}" class="btn btn-circle text-info" title="View Details">
                                            <i class="fas fa-eye small"></i>
                                        </a>
                                        @can('edit visitors')
                                        <a href="{{ route('visitor.edit', $visit->id) }}" class="btn btn-circle text-primary" title="Edit/Approve">
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

    <!-- Recent Visits -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h6 class="fw-800 sub-label mb-4">Recent Visits Log</h6>
                <div class="table-responsive log-container">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Host</th>
                                <th>Purpose</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentVisits as $visit)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <span class="fw-800 small">{{ substr($visit->visitor->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="small fw-800 d-block">{{ $visit->visitor->name }}</span>
                                            <span class="fs-9 text-white-50">{{ $visit->visitor->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $visit->meetingUser->name }}</td>
                                <td class="small">{{ substr($visit->purpose, 0, 25) }}...</td>
                                <td class="small">{{ \Carbon\Carbon::parse($visit->schedule_time)->format('M j, Y') }}</td>
                                <td>
                                    @if($visit->status == 'approved')
                                        <span class="status-badge text-success">Active</span>
                                    @elseif($visit->status == 'pending')
                                        <span class="status-badge text-warning">Pending</span>
                                    @elseif($visit->status == 'completed')
                                        <span class="status-badge">Completed</span>
                                    @else
                                        <span class="status-badge text-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('visitor.show', $visit->id) }}" class="btn btn-circle text-info" title="View Details">
                                            <i class="fas fa-eye small"></i>
                                        </a>
                                        @can('edit visitors')
                                        <a href="{{ route('visitor.edit', $visit->id) }}" class="btn btn-circle text-primary" title="Edit">
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
@endsection
