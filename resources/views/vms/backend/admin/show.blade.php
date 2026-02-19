@extends('layouts.admin')

@section('title', 'Visitor Details - Admin')

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
        <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Visitor Details</h2>
    </div>

    <!-- Visitor Info Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-user me-2"></i>Visitor Information
                </h6>
                <div class="info-row">
                    <label>Full Name</label>
                    <span>{{ $visit->visitor->name }}</span>
                </div>
                <div class="info-row">
                    <label>Email</label>
                    <span>{{ $visit->visitor->email }}</span>
                </div>
                <div class="info-row">
                    <label>Phone</label>
                    <span>{{ $visit->visitor->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <label>Company</label>
                    <span>{{ $visit->visitor->address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-calendar-check me-2"></i>Visit Details
                </h6>
                <div class="info-row">
                    <label>Visit Type</label>
                    <span class="badge badge-visit-type">{{ $visit->type ? $visit->type->name : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <label>Purpose</label>
                    <span>{{ $visit->purpose }}</span>
                </div>
                <div class="info-row">
                    <label>Scheduled Date</label>
                    <span>{{ \Carbon\Carbon::parse($visit->schedule_time)->format('F j, Y - g:i A') }}</span>
                </div>
                <div class="info-row">
                    <label>Status</label>
                    <span class="badge badge-{{ $visit->status }}">
                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-user-tie me-2"></i>Host Information
                </h6>
                <div class="info-row">
                    <label>Host Name</label>
                    <span>{{ $visit->meetingUser ? $visit->meetingUser->name : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <label>Host Email</label>
                    <span>{{ $visit->meetingUser ? $visit->meetingUser->email : 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-clock me-2"></i>Timeline
                </h6>
                <div class="info-row">
                    <label>Created At</label>
                    <span>{{ $visit->created_at->diffForHumans() }}</span>
                </div>
                <div class="info-row">
                    <label>Approved At</label>
                    <span>{{ $visit->approved_at ? $visit->approved_at->diffForHumans() : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <label>Check-in Time</label>
                    <span>{{ $visit->checkin_time ? $visit->checkin_time->format('h:i A') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <label>Check-out Time</label>
                    <span>{{ $visit->checkout_time ? $visit->checkout_time->format('h:i A') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RFID & Additional Info -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-id-card me-2"></i>Access Card
                </h6>
                <div class="info-row">
                    <label>RFID</label>
                    <span class="badge badge-rfid">{{ $visit->rfid ?? 'Not Generated' }}</span>
                </div>
                <div class="info-row">
                    <label>OTP Verified</label>
                    <span>{{ $visit->otp_verified_at ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </div>
        @if($visit->status === 'rejected' && $visit->rejected_reason)
        <div class="col-md-6">
            <div class="info-card" style="border-color: rgba(239, 68, 68, 0.3);">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #ef4444;">
                    <i class="fas fa-times-circle me-2"></i>Rejection Reason
                </h6>
                <div style="color: rgba(255,255,255,0.8);">{{ $visit->rejected_reason }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="d-flex gap-3 justify-content-center">
        @if($visit->status === 'pending_host')
            <a href="{{ route('admin.visitor.verify.otp.view', $visit->id) }}" class="btn-gradient" style="padding: 0.75rem 2rem; border-radius: 100px; text-decoration: none; display: inline-block;">
                <i class="fas fa-key me-2"></i>Verify OTP
            </a>
            <button class="btn-approve" onclick="approveVisit({{ $visit->id }})" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
                <i class="fas fa-check-circle me-2"></i>Approve
            </button>
            <button class="btn-reject" onclick="rejectVisit({{ $visit->id }})" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
                <i class="fas fa-times-circle me-2"></i>Reject
            </button>
        @elseif($visit->status === 'approved')
            <button class="btn-checkin" onclick="checkInVisitor({{ $visit->id }})" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
                <i class="fas fa-sign-in-alt me-2"></i>Check In
            </button>
        @elseif($visit->status === 'checked_in')
            <button class="btn-checkout" onclick="checkOutVisitor({{ $visit->id }})" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
                <i class="fas fa-sign-out-alt me-2"></i>Check Out
            </button>
        @endif
        <a href="{{ route('admin.visitor.index') }}" class="btn-back" style="padding: 0.75rem 2rem; border-radius: 100px; text-decoration: none; display: inline-block;">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

@push('scripts')
<style>
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
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-row label {
        color: rgba(255,255,255,0.6);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-row span {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
        font-weight: 500;
    }
    .badge {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-visit-type {
        background: rgba(59, 130, 246, 0.2);
        color: var(--accent-blue);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .badge-rfid {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        font-family: monospace;
    }
    .badge-approved {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .badge-pending_host {
        background: rgba(251, 191, 36, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.3);
    }
    .badge-checked_in {
        background: rgba(14, 165, 233, 0.2);
        color: #0ea5e9;
        border: 1px solid rgba(14, 165, 233, 0.3);
    }
    .badge-completed {
        background: rgba(168, 85, 247, 0.2);
        color: #a855f7;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }
    .badge-rejected {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .btn-gradient {
        background: linear-gradient(135deg, var(--accent-blue), #8b5cf6);
        color: #fff;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(59, 130, 246, 0.3);
    }
    .btn-approve {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        transition: 0.3s;
    }
    .btn-approve:hover {
        background: #22c55e;
        color: #fff;
    }
    .btn-reject {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        transition: 0.3s;
    }
    .btn-reject:hover {
        background: #ef4444;
        color: #fff;
    }
    .btn-checkin {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        transition: 0.3s;
    }
    .btn-checkin:hover {
        background: #22c55e;
        color: #fff;
    }
    .btn-checkout {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        transition: 0.3s;
    }
    .btn-checkout:hover {
        background: #ef4444;
        color: #fff;
    }
    .btn-back {
        background: rgba(107, 114, 128, 0.2);
        color: #fff;
        border: 1px solid rgba(107, 114, 128, 0.3);
        transition: 0.3s;
    }
    .btn-back:hover {
        background: rgba(107, 114, 128, 0.3);
    }
</style>

<script>
    function approveVisit(id) {
        Swal.fire({
            title: 'Approve Visit?',
            text: 'Are you sure you want to approve this visit?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/visits/' + id + '/approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Approved!',
                            text: data.message,
                            icon: 'success',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#22c55e'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }

    function rejectVisit(id) {
        Swal.fire({
            title: 'Reject Visit?',
            input: 'textarea',
            inputLabel: 'Reason for rejection',
            inputPlaceholder: 'Please enter the reason...',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                fetch('/admin/visits/' + id + '/reject', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ reason: result.value })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Rejected!',
                            text: data.message,
                            icon: 'success',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }

    function checkInVisitor(id) {
        Swal.fire({
            title: 'Check In Visitor?',
            text: 'Are you sure you want to check in this visitor?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Check In',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/visits/' + id + '/check-in', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Checked In!',
                            text: data.message,
                            icon: 'success',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#22c55e'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }

    function checkOutVisitor(id) {
        Swal.fire({
            title: 'Check Out Visitor?',
            text: 'Are you sure you want to check out this visitor?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Check Out',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/visits/' + id + '/check-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Checked Out!',
                            text: data.message,
                            icon: 'success',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
