@extends('layouts.admin')

@section('title', 'Verify OTP - Admin')

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
        <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Verify OTP</h2>
    </div>

    <!-- OTP Verification Form -->
    <div style="max-width: 600px; margin: 0 auto; padding: 3rem 2rem;">
        <div class="otp-card">
            <div class="text-center mb-5">
                <div class="otp-icon" style="margin: 0 auto 1.5rem;">
                    <i class="fas fa-key"></i>
                </div>
                <h4 class="text-white fw-800 mb-2">Verify Visitor OTP</h4>
                <p class="text-white" style="opacity: 0.7; font-size: 0.9rem;">
                    Enter the 6-digit OTP sent to {{ $visit->visitor->name }}
                </p>
            </div>

            <form method="POST" action="{{ route('admin.visitor.verify.otp', $visit->id) }}" id="otpForm">
                @csrf

                <div class="mb-4">
                    <div class="input-group-otp">
                        <input type="text"
                               class="input-otp"
                               name="otp"
                               id="otp"
                               maxlength="6"
                               placeholder="000000"
                               required
                               pattern="[0-9]{6}"
                               inputmode="numeric">
                        <div class="otp-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    @error('otp')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="visitor-info mb-5">
                    <div class="info-item">
                        <label>Visitor Name</label>
                        <span>{{ $visit->visitor->name }}</span>
                    </div>
                    <div class="info-item">
                        <label>Visitor Email</label>
                        <span>{{ $visit->visitor->email }}</span>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <span>{{ $visit->visitor->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Visit Purpose</label>
                        <span>{{ $visit->purpose }}</span>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle me-2"></i>Verify OTP
                    </button>
                    <a href="{{ route('admin.visitor.show', $visit->id) }}" class="btn-cancel">
                        <i class="fas fa-times-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .otp-card {
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    .otp-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-blue), #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #fff;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }
    .input-group-otp {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .input-otp {
        width: 100%;
        padding: 1rem 3rem;
        background: rgba(15, 23, 42, 0.6);
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        color: #fff;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 0.5rem;
        text-align: center;
        transition: 0.3s;
        font-family: monospace;
    }
    .input-otp:focus {
        outline: none;
        border-color: var(--accent-blue);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
    }
    .input-otp::placeholder {
        color: rgba(255,255,255,0.3);
        letter-spacing: 0;
    }
    .input-group-otp .otp-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.5);
        font-size: 1rem;
    }
    .visitor-info {
        background: rgba(255,255,255,0.03);
        border-radius: 12px;
        padding: 1.5rem;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-item label {
        color: rgba(255,255,255,0.6);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-item span {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
        font-weight: 500;
    }
    .error-message {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
        margin-top: 1rem;
    }
    .btn-submit {
        flex: 1;
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, var(--accent-blue), #8b5cf6);
        color: #fff;
        border: none;
        border-radius: 100px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        text-align: center;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
    }
    .btn-cancel {
        flex: 1;
        padding: 0.875rem 2rem;
        background: rgba(107, 114, 128, 0.2);
        color: #fff;
        border: 1px solid rgba(107, 114, 128, 0.3);
        border-radius: 100px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        text-align: center;
        text-decoration: none;
    }
    .btn-cancel:hover {
        background: rgba(107, 114, 128, 0.3);
    }
</style>

<script>
    // Auto-format OTP input
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
    });

    // Auto-submit when OTP is complete
    document.getElementById('otp').addEventListener('input', function(e) {
        if (this.value.length === 6) {
            document.getElementById('otpForm').submit();
        }
    });
</script>
@endpush
@endsection
