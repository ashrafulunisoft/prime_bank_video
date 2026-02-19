@extends('layouts.admin')

@section('title', 'Package Details - Prime Bank Limited')

@section('content')
<div class="glass-card glass-card-dark offset-2 col-10 mt-4">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
        <div class="d-flex align-items-center gap-3">
            <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem; background: #fff; color: #1e293b; border-radius: 10px; font-weight: 900; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(255,255,255,0.2);">V</div>
            <div>
                <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Package Details</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-800 text-white mb-0">{{ $insurancePackage->name }}</h5>
                    <span class="badge {{ $insurancePackage->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="font-size: 0.75rem; padding: 0.4rem 1rem; font-weight: 800; border: 1px solid {{ $insurancePackage->is_active ? 'rgba(34, 197, 94, 0.4)' : 'rgba(239, 68, 68, 0.4)' }};">
                        {{ $insurancePackage->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>

                <p class="text-white-50 mb-4" style="line-height: 1.6;">{{ $insurancePackage->description ?? 'No description provided for this insurance package.' }}</p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div style="background: rgba(0, 0, 0, 0.2); border-radius: 12px; padding: 20px;">
                            <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1 mb-2">Price</div>
                            <div class="text-success fw-bold fs-3">${{ number_format($insurancePackage->price, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background: rgba(0, 0, 0, 0.2); border-radius: 12px; padding: 20px;">
                            <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1 mb-2">Coverage Amount</div>
                            <div class="text-white fw-bold fs-4">${{ number_format($insurancePackage->coverage_amount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background: rgba(0, 0, 0, 0.2); border-radius: 12px; padding: 20px;">
                            <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1 mb-2">Duration</div>
                            <div class="text-white fw-bold fs-4">{{ $insurancePackage->duration_months }} Months</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background: rgba(0, 0, 0, 0.2); border-radius: 12px; padding: 20px;">
                            <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1 mb-2">Package ID</div>
                            <div class="text-white fw-bold fs-4">#{{ $insurancePackage->id }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                <h6 class="text-white-50 small fw-800 text-uppercase letter-spacing-1 mb-4">Actions</h6>

                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('insurance-packages.edit', $insurancePackage->id) }}" class="btn btn-warning w-100" style="padding: 12px 24px;">
                        <i class="fas fa-edit me-2"></i>Edit Package
                    </a>
                    <button type="button" class="btn btn-danger w-100 delete-btn" style="padding: 12px 24px;">
                        <i class="fas fa-trash me-2"></i>Delete Package
                    </button>
                    <form action="{{ route('insurance-packages.destroy', $insurancePackage->id) }}" method="POST" class="d-none delete-form">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 24px 0;">

                <h6 class="text-white-50 small fw-800 text-uppercase letter-spacing-1 mb-3">Timestamps</h6>
                <div class="text-white-50 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created</span>
                        <span class="text-white">{{ $insurancePackage->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Updated</span>
                        <span class="text-white">{{ $insurancePackage->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="d-flex justify-content-between align-items-center text-white-50 small mt-4" style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <div>
            <i class="fas fa-cog me-1"></i>
            Package Details
        </div>
        <div>
            <i class="fas fa-shield-alt me-1"></i>
            Admin - Full Access
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.delete-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Delete Package?',
                text: 'This action cannot be undone! All associated orders will be affected.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280'
            }).then(result => {
                if (result.isConfirmed) {
                    document.querySelector('.delete-form').submit();
                }
            });
        });
    });
</script>
@endpush
