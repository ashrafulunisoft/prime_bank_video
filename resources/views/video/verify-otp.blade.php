@extends('layouts.receptionist')

@section('title', 'Verify OTP - Video Call Support')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Verify OTP
                    </h4>
                </div>

                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-mobile-alt fa-4x text-primary"></i>
                        </div>
                        <p class="text-muted">
                            A 6-digit OTP has been sent to your mobile number:
                        </p>
                        <p class="font-weight-bold text-dark">
                            {{ $maskedPhone }}
                        </p>
                        <p class="text-muted small">
                            Please enter OTP below to verify your identity before starting the video call.
                        </p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('video.verify.otp') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="otp" class="form-label fw-bold">Enter OTP</label>
                            <input type="text"
                                   id="otp"
                                   name="otp"
                                   class="form-control form-control-lg text-center fs-4 fw-bold"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   placeholder="000000"
                                   required
                                   autofocus
                                   style="letter-spacing: 0.5rem;">
                            <div class="form-text text-muted text-center mt-2">
                                Enter 6-digit OTP sent to your mobile
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>
                                Verify OTP
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resendOtp()">
                                <i class="fas fa-redo me-2"></i>
                                Resend OTP
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-link text-muted">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Home
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            OTP is valid for 10 minutes
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #otp:focus {
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        border-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
    // Resend OTP function
    async function resendOtp() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        try {
            const response = await fetch('/video/send-otp', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('OTP has been resent to your mobile number.');
                document.getElementById('otp').value = '';
                document.getElementById('otp').focus();
            } else {
                alert('Failed to resend OTP: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error resending OTP:', error);
            alert('An error occurred while resending OTP. Please try again.');
        }
    }

    // Auto-focus OTP input on page load
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.focus();
        }
    });
</script>
@endpush
