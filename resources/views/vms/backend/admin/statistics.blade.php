@extends('layouts.admin')

@section('title', 'Statistics - Admin')

@section('content')
<div class="glass-card glass-card-dark">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
        <div class="d-flex align-items-center gap-3">
            <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
            <div>
                <h6 class="fw-800 mb-0 text-white text-shadow-white" style="font-size: 1.1rem;">Prime Bank Limited</h6>
                <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
            </div>
        </div>
        <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Statistics</h2>
    </div>

    <!-- Overview Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Total Visitors</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $stats['total_visitors'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.2);">
                        <i class="fas fa-users" style="color: var(--accent-blue);"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Total Visits</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $stats['total_visits'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(34, 197, 94, 0.2);">
                        <i class="fas fa-calendar-check" style="color: #22c55e;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Today's Visits</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $stats['today_visits'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(251, 191, 36, 0.2);">
                        <i class="fas fa-calendar-day" style="color: #fbbf24;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Active Now</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $stats['active_visits'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(14, 165, 233, 0.2);">
                        <i class="fas fa-user-check" style="color: #0ea5e9;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-chart-pie me-2"></i>Visit Status Breakdown
                </h6>
                <div class="status-breakdown">
                    <div class="status-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Completed</span>
                            <span>{{ $stats['completed'] ?? 0 }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $stats['total_visits'] ? (($stats['completed'] ?? 0) / $stats['total_visits'] * 100) : 0 }}%; background: #a855f7;"></div>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Checked In</span>
                            <span>{{ $stats['checked_in'] ?? 0 }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $stats['total_visits'] ? (($stats['checked_in'] ?? 0) / $stats['total_visits'] * 100) : 0 }}%; background: #0ea5e9;"></div>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Approved</span>
                            <span>{{ $stats['approved'] ?? 0 }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $stats['total_visits'] ? (($stats['approved'] ?? 0) / $stats['total_visits'] * 100) : 0 }}%; background: #22c55e;"></div>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pending</span>
                            <span>{{ $stats['pending'] ?? 0 }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $stats['total_visits'] ? (($stats['pending'] ?? 0) / $stats['total_visits'] * 100) : 0 }}%; background: #fbbf24;"></div>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Rejected</span>
                            <span>{{ $stats['rejected'] ?? 0 }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $stats['total_visits'] ? (($stats['rejected'] ?? 0) / $stats['total_visits'] * 100) : 0 }}%; background: #ef4444;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-chart-line me-2"></i>Weekly Overview
                </h6>
                <div class="weekly-stats">
                    <div class="week-item">
                        <label>Monday</label>
                        <div class="week-bar">
                            <div class="week-fill" style="width: {{ $stats['week_stats']['monday'] ?? 0 }}%;"></div>
                        </div>
                        <span>{{ $stats['week_visits']['monday'] ?? 0 }}</span>
                    </div>
                    <div class="week-item">
                        <label>Tuesday</label>
                        <div class="week-bar">
                            <div class="week-fill" style="width: {{ $stats['week_stats']['tuesday'] ?? 0 }}%;"></div>
                        </div>
                        <span>{{ $stats['week_visits']['tuesday'] ?? 0 }}</span>
                    </div>
                    <div class="week-item">
                        <label>Wednesday</label>
                        <div class="week-bar">
                            <div class="week-fill" style="width: {{ $stats['week_stats']['wednesday'] ?? 0 }}%;"></div>
                        </div>
                        <span>{{ $stats['week_visits']['wednesday'] ?? 0 }}</span>
                    </div>
                    <div class="week-item">
                        <label>Thursday</label>
                        <div class="week-bar">
                            <div class="week-fill" style="width: {{ $stats['week_stats']['thursday'] ?? 0 }}%;"></div>
                        </div>
                        <span>{{ $stats['week_visits']['thursday'] ?? 0 }}</span>
                    </div>
                    <div class="week-item">
                        <label>Friday</label>
                        <div class="week-bar">
                            <div class="week-fill" style="width: {{ $stats['week_stats']['friday'] ?? 0 }}%;"></div>
                        </div>
                        <span>{{ $stats['week_visits']['friday'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-calendar-week me-2"></i>This Week
                </h6>
                <div class="big-stat">
                    {{ $stats['week_total'] ?? 0 }}
                    <small>visits</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-calendar-alt me-2"></i>This Month
                </h6>
                <div class="big-stat">
                    {{ $stats['month_total'] ?? 0 }}
                    <small>visits</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-clock me-2"></i>Average Duration
                </h6>
                <div class="big-stat">
                    {{ $stats['avg_duration'] ?? 0 }}m
                    <small>per visit</small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .stat-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 1.5rem;
        transition: 0.3s;
    }
    .stat-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        transform: translateY(-2px);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .info-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 2rem;
        transition: 0.3s;
    }
    .info-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
    }
    .status-breakdown {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .status-item {
        margin-bottom: 0.5rem;
    }
    .status-item > div {
        color: rgba(255,255,255,0.9);
        font-size: 0.85rem;
        font-weight: 600;
    }
    .progress {
        height: 8px;
        background: rgba(255,255,255,0.1);
        border-radius: 100px;
        overflow: hidden;
    }
    .progress-bar {
        height: 100%;
        border-radius: 100px;
        transition: width 0.5s ease;
    }
    .weekly-stats {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .week-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .week-item label {
        min-width: 80px;
        color: rgba(255,255,255,0.7);
        font-size: 0.85rem;
        font-weight: 600;
    }
    .week-bar {
        flex: 1;
        height: 8px;
        background: rgba(255,255,255,0.1);
        border-radius: 100px;
        overflow: hidden;
    }
    .week-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-blue), #8b5cf6);
        border-radius: 100px;
        transition: width 0.5s ease;
    }
    .week-item span {
        min-width: 40px;
        text-align: right;
        color: rgba(255,255,255,0.9);
        font-size: 0.85rem;
        font-weight: 700;
    }
    .big-stat {
        font-size: 3rem;
        font-weight: 800;
        color: var(--accent-blue);
        line-height: 1;
        text-align: center;
    }
    .big-stat small {
        display: block;
        font-size: 0.85rem;
        color: rgba(255,255,255,0.6);
        font-weight: 500;
        margin-top: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush
@endsection
