@extends('layouts.admin')

@section('title', 'Claim Details - Prime Bank Limited')

@section('content')
    <!-- Header -->
    <div class="header-section">
        <div>
            <h3 class="fw-800 mb-1 text-white letter-spacing-1">Claim Details</h3>
            <p class="sub-label mb-0">View claim information and details</p>
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
        <a href="{{ route('admin.claims.index') }}" class="btn btn-outline-light btn-sm" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-2"></i> Back to Claims
        </a>
    </div>

    <!-- Claim Details Card -->
    <div class="glass-card glass-card-dark col-12">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem; background: #fff; color: #1e293b; border-radius: 10px; font-weight: 900; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(255,255,255,0.2);">C</div>
                <div>
                    <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                    <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                @php
                    $statusClass = match($claim->status) {
                        'approved' => 'bg-success bg-opacity-10 text-success',
                        'pending' => 'bg-warning bg-opacity-10 text-warning',
                        'rejected' => 'bg-danger bg-opacity-10 text-danger',
                        default => 'bg-secondary bg-opacity-10 text-secondary'
                    };
                    $statusBorder = match($claim->status) {
                        'approved' => 'rgba(34, 197, 94, 0.4)',
                        'pending' => 'rgba(234, 179, 8, 0.4)',
                        'rejected' => 'rgba(239, 68, 68, 0.4)',
                        default => 'rgba(107, 114, 128, 0.4)'
                    };
                @endphp
                <span class="badge {{ $statusClass }}" style="font-size: 0.85rem; padding: 0.5rem 1rem; font-weight: 800; border: 1px solid {{ $statusBorder }};">
                    {{ strtoupper($claim->status) }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Claim Information -->
            <div class="col-lg-6">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        Claim Information
                    </h5>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Claim Number</label>
                        <p class="fw-800 text-white mb-0" style="font-size: 1.1rem; color: #3b82f6;">{{ $claim->claim_number }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Claim Amount</label>
                        <p class="fw-800 text-success mb-0" style="font-size: 1.5rem;">${{ number_format($claim->claim_amount, 2) }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Submitted On</label>
                        <p class="text-white mb-0">{{ $claim->created_at->format('F d, Y - h:i A') }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Last Updated</label>
                        <p class="text-white mb-0">{{ $claim->updated_at->format('F d, Y - h:i A') }}</p>
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
                        <p class="fw-800 text-white mb-0">{{ $claim->user->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Email Address</label>
                        <p class="text-white mb-0">{{ $claim->user->email }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Customer Since</label>
                        <p class="text-white mb-0">{{ $claim->user->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Claim Reason -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-info-circle me-2 text-warning"></i>
                        Claim Reason
                    </h5>

                    <div class="p-3 rounded" style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255,255,255,0.1);">
                        <p class="text-white mb-0" style="line-height: 1.8;">{{ $claim->reason }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Policy Information -->
        @if($claim->order)
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-file-contract me-2 text-info"></i>
                        Related Policy Information
                    </h5>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Policy Number</label>
                            <p class="fw-800 text-white mb-0">{{ $claim->order->policy_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Coverage Period</label>
                            <p class="text-white mb-0">
                                <i class="fas fa-calendar me-1 text-info"></i>
                                {{ $claim->order->start_date->format('M d, Y') }} - {{ $claim->order->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Policy Status</label>
                            <p class="mb-0">
                                @php
                                    $policyStatusClass = match($claim->order->status) {
                                        'active' => 'bg-success bg-opacity-10 text-success',
                                        'pending' => 'bg-warning bg-opacity-10 text-warning',
                                        'expired' => 'bg-danger bg-opacity-10 text-danger',
                                        default => 'bg-secondary bg-opacity-10 text-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $policyStatusClass }}" style="font-size: 0.75rem; padding: 0.3rem 0.6rem; font-weight: 800;">
                                    {{ strtoupper($claim->order->status) }}
                                </span>
                            </p>
                        </div>
                        @if($claim->order->package)
                        <div class="col-12">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Insurance Package</label>
                            <p class="text-white mb-0">
                                <i class="fas fa-box me-1 text-warning"></i>
                                {{ $claim->order->package->name }} - Coverage: ${{ number_format($claim->order->package->coverage_amount, 2) }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Insurance Package Information -->
        @if($claim->package)
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                    <h5 class="fw-800 text-white mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-box me-2 text-warning"></i>
                        Insurance Package Details
                    </h5>

                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Package Name</label>
                            <p class="fw-800 text-white mb-0">{{ $claim->package->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Price</label>
                            <p class="fw-800 text-success mb-0" style="font-size: 1.2rem;">${{ number_format($claim->package->price, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Coverage Amount</label>
                            <p class="fw-800 text-info mb-0">${{ number_format($claim->package->coverage_amount, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-white-50 small fw-800 text-uppercase" style="font-size: 0.7rem;">Duration</label>
                            <p class="text-white mb-0">{{ $claim->package->duration_months }} Months</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="d-flex justify-content-between align-items-center text-white-50 small mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <div>
                <i class="fas fa-shield-alt me-1"></i>
                Claim Details - {{ $claim->claim_number }}
            </div>
            <div>
                <i class="fas fa-user-shield me-1"></i>
                Admin - Full Access
            </div>
        </div>
    </div>
@endsection
