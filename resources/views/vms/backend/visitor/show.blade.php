@extends('layouts.receptionist')

@section('title', 'Visitor Details - Prime Bank Limited')

@section('content')
<div class="role-container">
    <div class="glass-card glass-card-dark">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
                <div>
                    <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                    <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">VISITOR SYSTEM</span>
                </div>
            </div>
            <div>
                <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Visitor Details</h2>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="d-flex justify-content-center mb-4">
            <span class="badge {{ getStatusBadge($visit->status) }}" style="font-size: 1rem; padding: 0.6rem 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fas fa-circle-notch fa-spin me-2" style="font-size: 0.8rem;"></i>
                {{ formatStatus($visit->status) }}
            </span>
        </div>

        <!-- OTP Verification Section (Only show if status is pending_otp) -->
        @if($visit->status === 'pending_otp')
            <div class="glass-card-light p-4 mb-5" style="background: rgba(251, 191, 36, 0.1); border: 2px dashed rgba(251, 191, 36, 0.5); border-radius: 16px;">
                <div class="text-center mb-3">
                    <i class="fas fa-shield-alt mb-2" style="font-size: 2.5rem; color: #f59e0b;"></i>
                    <h5 class="fw-800 text-white mb-2">OTP Verification Required</h5>
                    <p class="text-white-50 small mb-3">A 6-digit OTP has been sent to the visitor's email address.</p>
                </div>

                <form action="{{ route('visitor.verify.otp', $visit->id) }}" method="POST">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <label class="form-label text-center" style="display: block;">Enter 6-Digit OTP</label>
                            <div class="position-relative">
                                <input type="text"
                                       name="otp"
                                       class="input-dark input-custom"
                                       placeholder="123456"
                                       style="font-size: 1.5rem; letter-spacing: 8px; text-align: center;"
                                       maxlength="6"
                                       pattern="[0-9]{6}"
                                       required>
                            </div>
                            @error('otp')
                                <div class="text-danger small mt-2" style="font-size: 0.8rem;">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn-gradient btn-create" style="padding: 1rem 2.5rem;">
                            <i class="fas fa-check-circle me-2"></i> Verify OTP
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Visitor Information -->
        <div class="permission-title">Visitor Information</div>
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->visitor->name }}" readonly>
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <div class="position-relative">
                    <input type="email" class="input-dark" value="{{ $visit->visitor->email }}" readonly>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <div class="position-relative">
                    <input type="tel" class="input-dark" value="{{ $visit->visitor->phone ?? 'N/A' }}" readonly>
                    <i class="fas fa-phone input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Company/Organization</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->visitor->address ?? 'N/A' }}" readonly>
                    <i class="fas fa-building input-icon"></i>
                </div>
            </div>
        </div>

        <!-- Visit Information -->
        <div class="permission-title">Visit Details</div>
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <label class="form-label">Host Name</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->meetingUser->name }}" readonly>
                    <i class="fas fa-user-tie input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Visit Type</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->type->name ?? 'N/A' }}" readonly>
                    <i class="fas fa-tag input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Purpose of Visit</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->purpose }}" readonly>
                    <i class="fas fa-briefcase input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Scheduled Time</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A') }}" readonly>
                    <i class="fas fa-calendar-alt input-icon"></i>
                </div>
            </div>
        </div>

        <!-- RFID Information (Only show if approved) -->
        @if($visit->rfid)
            <div class="permission-title">RFID Badge Information</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">RFID Badge Number</label>
                    <div class="position-relative">
                        <input type="text" class="input-dark" value="{{ $visit->rfid }}" readonly style="color: #22c55e; font-weight: 800; font-size: 1.2rem;">
                        <i class="fas fa-id-badge input-icon" style="color: #22c55e;"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Approved At</label>
                    <div class="position-relative">
                        <input type="text" class="input-dark" value="{{ $visit->approved_at ? \Carbon\Carbon::parse($visit->approved_at)->format('F j, Y - g:i A') : 'N/A' }}" readonly>
                        <i class="fas fa-check-circle input-icon" style="color: #22c55e;"></i>
                    </div>
                </div>
            </div>
        @endif

        <!-- Check-in/out Information -->
        <div class="permission-title">Visit Timeline</div>
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <label class="form-label">Check-in Time</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->checkin_time ? \Carbon\Carbon::parse($visit->checkin_time)->format('g:i A') : 'Not checked in' }}" readonly>
                    <i class="fas fa-sign-in-alt input-icon"></i>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Check-out Time</label>
                <div class="position-relative">
                    <input type="text" class="input-dark" value="{{ $visit->checkout_time ? \Carbon\Carbon::parse($visit->checkout_time)->format('g:i A') : 'Not checked out' }}" readonly>
                    <i class="fas fa-sign-out-alt input-icon"></i>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($visit->status === 'pending_host')
            <div class="glass-card-light p-4 mb-4" style="background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.3); border-radius: 16px;">
                <h6 class="fw-800 text-white mb-3">
                    <i class="fas fa-user-shield me-2"></i>Host Approval
                </h6>
                <form action="{{ route('visit.approve', $visit->id) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <button type="submit" class="btn-gradient btn-create">
                        <i class="fas fa-check-circle me-2"></i>Approve Visit
                    </button>
                </form>
            </div>
        @endif

        <!-- Actions -->
        <div class="d-flex justify-content-between gap-3 mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <a href="{{ route('visitor.index') }}" class="btn-outline btn-reset" style="text-decoration: none;">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <div class="d-flex gap-2">
                @if($visit->status === 'pending_host' && auth()->user()->can('approve visit'))
                    <form action="{{ route('visit.approve', $visit->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-gradient btn-create">
                            <i class="fas fa-check-circle me-2"></i>Approve
                        </button>
                    </form>
                @endif

                @if($visit->status === 'approved' && auth()->user()->can('checkin visit'))
                    <button onclick="checkIn({{ $visit->id }})" class="btn btn-gradient" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                        <i class="fas fa-sign-in-alt me-2"></i>Check In
                    </button>
                @endif

                @if($visit->status === 'checked_in' && auth()->user()->can('checkout visit'))
                    <button onclick="checkOut({{ $visit->id }})" class="btn btn-gradient" style="background: linear-gradient(135deg, #eab308, #ca8a04);">
                        <i class="fas fa-sign-out-alt me-2"></i>Check Out
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Check-in function
    function checkIn(visitId) {
        Swal.fire({
            title: 'Check In Visitor?',
            text: 'Are you sure you want to check in this visitor?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Check In',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/visits/${visitId}/check-in`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', data.message, 'success')
                            .then(() => {
                                location.reload();
                            });
                    } else {
                        Swal.fire('Error!', data.message || 'Error checking in visitor', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Error checking in visitor', 'error');
                    console.error('Error:', error);
                });
            }
        });
    }

    // Check-out function
    function checkOut(visitId) {
        Swal.fire({
            title: 'Check Out Visitor?',
            text: 'Are you sure you want to check out this visitor?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Check Out',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/visits/${visitId}/check-out`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', data.message, 'success')
                            .then(() => {
                                location.reload();
                            });
                    } else {
                        Swal.fire('Error!', data.message || 'Error checking out visitor', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Error checking out visitor', 'error');
                    console.error('Error:', error);
                });
            }
        });
    }
</script>

@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3b82f6',
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            closeButtonAriaLabel: 'Close this alert'
        });
    </script>
@endif

@if($errors->any())
    <script>
        Swal.fire({
            title: 'Error!',
            text: "{{ $errors->first() }}",
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444',
            showCloseButton: true,
            closeButtonAriaLabel: 'Close this alert'
        });
    </script>
@endif
@endpush

@php
    function getStatusBadge($status) {
        $badges = [
            'pending_host' => 'bg-warning bg-opacity-10 text-warning',
            'approved' => 'bg-info bg-opacity-10 text-info',
            'checked_in' => 'bg-success bg-opacity-10 text-success',
            'pending_otp' => 'bg-warning bg-opacity-10 text-warning',
            'rejected' => 'bg-danger bg-opacity-10 text-danger',
            'completed' => 'bg-dark bg-opacity-10 text-white-50'
        ];
        return $badges[$status] ?? 'bg-secondary bg-opacity-10 text-white-50';
    }

    function formatStatus($status) {
        return str_replace('_', ' ', strtoupper($status));
    }
@endphp
