@extends('layouts.admin')

@section('title', 'Create Insurance Package - Prime Bank Limited')

@section('content')
<div class="glass-card glass-card-dark">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
        <div class="d-flex align-items-center gap-3">
            <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
            <div>
                    <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Create Package</h2>
            </div>
        </div>
    </div>

    <div class="glass-card-light p-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
            </div>
        @endif

        <form action="{{ route('insurance-packages.store') }}" method="POST" class="row g-4">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Package Name *</label>
                <input type="text" name="name" id="name" class="form-control input-dark" value="{{ old('name') }}" required style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px 20px; color: #fff;">
            </div>

            <div class="col-md-6">
                <label for="price" class="form-label text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Price ($) *</label>
                <input type="number" name="price" id="price" class="form-control input-dark" value="{{ old('price') }}" step="0.01" min="0" required style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px 20px; color: #fff;">
            </div>

            <div class="col-12">
                <label for="description" class="form-label text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Description</label>
                <textarea name="description" id="description" class="form-control input-dark" rows="3" style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px 20px; color: #fff;">{{ old('description') }}</textarea>
            </div>

            <div class="col-md-6">
                <label for="coverage_amount" class="form-label text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Coverage Amount ($) *</label>
                <input type="number" name="coverage_amount" id="coverage_amount" class="form-control input-dark" value="{{ old('coverage_amount') }}" step="0.01" min="0" required style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px 20px; color: #fff;">
            </div>

            <div class="col-md-6">
                <label for="duration_months" class="form-label text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Duration (Months) *</label>
                <input type="number" name="duration_months" id="duration_months" class="form-control input-dark" value="{{ old('duration_months', 12) }}" min="1" required style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px 20px; color: #fff;">
                <small class="text-white-50 small mt-1 d-block">Enter 6, 12, 24, etc.</small>
            </div>

            <div class="col-12">
                <div class="checkbox-label" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 15px 20px; display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="is_active" id="is_active" class="checkbox-custom" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 20px; height: 20px; margin-right: 15px; cursor: pointer; accent-color: #3b82f6;">
                    <label for="is_active" class="mb-0 text-white cursor-pointer" style="cursor: pointer;">Active</label>
                </div>
            </div>

            <div class="col-12 mt-4 d-flex gap-3">
                <button type="submit" class="btn btn-create btn-primary btn-gradient" style="padding: 14px 32px;">
                    <i class="fas fa-check me-2"></i>Create Package
                </button>
                <a href="{{ route('insurance-packages.index') }}" class="btn btn-reset btn-outline" style="padding: 14px 32px;">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Footer Info -->
    <div class="d-flex justify-content-between align-items-center text-white-50 small mt-4" style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <div>
            <i class="fas fa-cog me-1"></i>
            Create New Insurance Package
        </div>
        <div>
            <i class="fas fa-shield-alt me-1"></i>
            Admin - Full Access
        </div>
    </div>
</div>
@endsection
