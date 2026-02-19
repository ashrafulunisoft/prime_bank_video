@extends('layouts.admin')

@section('title', 'Policy Details - Prime Bank Limited')

@section('content')
    <!-- Header -->
    <div class="header-section">
        <div>
            <h3 class="fw-800 mb-1 text-white letter-spacing-1">Policy Details</h3>
            <p class="sub-label mb-0">View policy information and claims</p>
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

    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.policies.index') }}" class="btn btn-outline-light btn-sm" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-2"></i> Back to Policies
        </a>
    </div>

    <!-- Policy Details Card -->
    <div class="glass-card glass-card-dark col-12">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem; background: #fff; color: #1e293b; border-radius: 10px; font-weight: 900; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(255,255,255,0.2);">P</div>
                <div>
                    <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                    <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                @php
                    $statusClass = match($order->status) {
                        'active' => 'bg-success bg-opacity-10 text-success',
                        'pending' => 'bg-warning bg-opacity-10 text-warning',
                        'expired' => 'bg-danger bg-opacity-10 text-danger',
                        'cancelled' => 'bg-secondary bg-opacity-10 text-secondary',
                        default => 'bg-secondary bg-opacity-10 text-secondary'
                    };
                    $statusBorder = match($order->status) {
                        'active' => 'rgba(34, 197, 94, 0.4)',
                        'pending' => 'rgba(234, 179, 8, 0.4)',
                        'expired' => 'rgba(239, 68, 68, 0.4)',
                        'cancelled' => 'rgba(107, 114, 128, 0.4)',
                        default => 'rgba(107, 114, 128, 0.4)'
                    };
                @endphp
                <span class="badge {{ $statusClass }}" style="font-size: 0.85rem; padding: 0.5rem 1rem; font-weight: 800; border: 1px solid {{ $statusBorder }};">
                    {{ strtoupper($order->status) }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Policy Information -->
            <div class="col-lg-6">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-file-contract me-2 text-primary"></i>
                        Policy Information
                    </h5>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Policy Number</label>
                        <p class="fw-800 text-white mb-0" style="font-size: 1.1rem; color: #3b82f6;">{{ $order->policy_number }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Created At</label>
                        <p class="text-white mb-0">{{ $order->created_at->format('F d, Y - h:i A') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Coverage Period</label>
                        <p class="text-white mb-0">
                            <i class="fas fa-calendar me-1 text-info"></i>
                            {{ $order->start_date->format('M d, Y') }} - {{ $order->end_date->format('M d, Y') }}
                        </p>
                    </div>

                    @php
                        $daysRemaining = now()->diffInDays($order->end_date, false);
                        $isExpired = now()->isAfter($order->end_date);
                    @endphp

                    <div class="mb-0">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Time Remaining</label>
                        <p class="mb-0 {{ $isExpired ? 'text-danger' : ($daysRemaining < 30 ? 'text-warning' : 'text-success') }}">
                            @if($isExpired)
                                <i class="fas fa-exclamation-circle me-1"></i> Expired {{ now()->diffInDays($order->end_date) }} days ago
                            @else
                                <i class="fas fa-clock me-1"></i> {{ $daysRemaining }} days remaining
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-lg-6">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-user me-2 text-success"></i>
                        Customer Information
                    </h5>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Customer Name</label>
                        <p class="fw-800 text-white mb-0">{{ $order->user->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Email Address</label>
                        <p class="text-white mb-0">{{ $order->user->email }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Customer Since</label>
                        <p class="text-white mb-0">{{ $order->user->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Information -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-box me-2 text-warning"></i>
                        Insurance Package Details
                    </h5>

                    @if($order->package)
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Package Name</label>
                                <p class="fw-800 text-white mb-0">{{ $order->package->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Price</label>
                                <p class="fw-800 text-success mb-0" style="font-size: 1.2rem;">${{ number_format($order->package->price, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Coverage Amount</label>
                                <p class="fw-800 text-info mb-0">${{ number_format($order->package->coverage_amount, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Duration</label>
                                <p class="text-white mb-0">{{ $order->package->duration_months }} Months</p>
                            </div>
                            <div class="col-12">
                                <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Description</label>
                                <p class="text-white-50 mb-0">{{ $order->package->description }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                            <p class="text-white-50 mb-0">Package information not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Claims Section -->
        <div class="mt-4">
            <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                    <i class="fas fa-clipboard-list me-2 text-danger"></i>
                    Claims History
                    <span class="badge bg-danger ms-2" style="font-size: 0.75rem;">{{ $order->claims->count() }}</span>
                </h5>

                @if($order->claims->count() > 0)
                    <div class="table-responsive" style="border-radius: 12px; overflow: hidden;">
                        <table class="table table-dark mb-0" style="margin: 0;">
                            <thead>
                                <tr style="background: rgba(0, 0, 0, 0.3);">
                                    <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">ID</th>
                                    <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Claim Amount</th>
                                    <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Reason</th>
                                    <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Status</th>
                                    <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->claims as $claim)
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                        <td style="padding: 1rem;">
                                            <span class="fw-800" style="color: #3b82f6;">#{{ $claim->id }}</span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span class="fw-800 text-white">${{ number_format($claim->claim_amount, 2) }}</span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span class="text-white-50">{{ $claim->reason }}</span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            @php
                                                $claimStatusClass = match($claim->status) {
                                                    'approved' => 'bg-success bg-opacity-10 text-success',
                                                    'pending' => 'bg-warning bg-opacity-10 text-warning',
                                                    'rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                    default => 'bg-secondary bg-opacity-10 text-secondary'
                                                };
                                                $claimStatusBorder = match($claim->status) {
                                                    'approved' => 'rgba(34, 197, 94, 0.4)',
                                                    'pending' => 'rgba(234, 179, 8, 0.4)',
                                                    'rejected' => 'rgba(239, 68, 68, 0.4)',
                                                    default => 'rgba(107, 114, 128, 0.4)'
                                                };
                                            @endphp
                                            <span class="badge {{ $claimStatusClass }}" style="font-size: 0.7rem; padding: 0.3rem 0.6rem; font-weight: 800; border: 1px solid {{ $claimStatusBorder }};">
                                                {{ strtoupper($claim->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span class="text-white-50 small">{{ $claim->created_at->format('M d, Y') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-check mb-2" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        <h6 class="fw-800 text-white mb-2">No Claims Yet</h6>
                        <p class="text-white-50 mb-0 small">This policy has no claims submitted.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center text-white-50 small mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <div>
                <i class="fas fa-shield-alt me-1"></i>
                Policy Details - {{ $order->policy_number }}
            </div>
            <div>
                <i class="fas fa-user-shield me-1"></i>
                Admin - Full Access
            </div>
        </div>
    </div>
@endsection
