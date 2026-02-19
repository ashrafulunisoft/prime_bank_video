@extends('layouts.admin')

@section('title', 'My Profile - Admin')

@section('content')
<div class="glass-card glass-card-dark">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-center gap-3 gap-md-0" style="margin-bottom: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
        <div class="d-flex align-items-center gap-3 text-center text-md-start">
            {{-- <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div> --}}
            <div class="mb-3 mb-md-0">
                <img class="bg-white" src="{{ asset('vms/logo/primebank_logo.png') }}" style="height: 70px; width: 200px; border-radius:10px;" alt="Prime Bank Logo">
            </div>

            <div>
                <h6 class="fw-800 mb-0 text-white text-shadow-white" style="font-size: 1rem;">Prime Bank Limited</h6>
                <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
            </div>
        </div>
        <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white text-center" style="font-size: 1.5rem;">My Profile</h2>
    </div>

    <!-- Profile Information -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="info-card text-center">
                <div class="avatar-large mb-4">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <h4 class="text-white fw-800 mb-2">{{ auth()->user()->name }}</h4>
                <div class="badge badge-role">{{ ucfirst(auth()->user()->getRoleNames()->first()) }}</div>
                <p class="text-white mt-3" style="opacity: 0.7;">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="col-md-8">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-user-circle me-2"></i>Account Information
                </h6>
                <div class="info-row">
                    <label>Name</label>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <div class="info-row">
                    <label>Email</label>
                    <span>{{ auth()->user()->email }}</span>
                </div>
                <div class="info-row">
                    <label>Role</label>
                    <span>{{ ucfirst(auth()->user()->getRoleNames()->first()) }}</span>
                </div>
                <div class="info-row">
                    <label>Email Verified</label>
                    <span>
                        @if(auth()->user()->email_verified_at)
                            <i class="fas fa-check-circle text-success"></i> Yes
                        @else
                            <i class="fas fa-times-circle text-danger"></i> No
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <label>Joined Date</label>
                    <span>{{ auth()->user()->created_at->format('F j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions -->
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <div class="info-card">
                <h6 class="text-white fw-800 mb-4" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">
                    <i class="fas fa-shield-alt me-2"></i>Permissions
                </h6>
                <div class="permissions-grid">
                    @if(auth()->user()->getAllPermissions()->count() > 0)
                        @foreach(auth()->user()->getAllPermissions() as $permission)
                            <div class="permission-badge">
                                <i class="fas fa-check"></i>
                                {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                            </div>
                        @endforeach
                    @else
                        <p class="text-white" style="opacity: 0.7;">No specific permissions assigned</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
        <button class="btn-edit-profile" onclick="openEditModal()" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
            <i class="fas fa-edit me-2"></i>Edit Profile
        </button>
        <button class="btn-change-password" onclick="openPasswordModal()" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
            <i class="fas fa-key me-2"></i>Change Password
        </button>
        <button class="btn-logout" onclick="logout()" style="padding: 0.75rem 2rem; border-radius: 100px; border: none; cursor: pointer;">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </button>
    </div>
</div>

@push('scripts')
<style>
    .avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-blue), #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 900;
        color: #fff;
        margin: 0 auto;
        box-shadow: 0 10px 40px rgba(59, 130, 246, 0.3);
    }
    .badge-role {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        padding: 0.4rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
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
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .permission-badge {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: rgba(255,255,255,0.9);
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.3s;
    }
    .permission-badge:hover {
        background: rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.4);
    }
    .btn-edit-profile {
        background: rgba(59, 130, 246, 0.2);
        color: var(--accent-blue);
        border: 1px solid rgba(59, 130, 246, 0.3);
        transition: 0.3s;
    }
    .btn-edit-profile:hover {
        background: var(--accent-blue);
        color: #fff;
    }
    .btn-change-password {
        background: rgba(251, 191, 36, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.3);
        transition: 0.3s;
    }
    .btn-change-password:hover {
        background: #fbbf24;
        color: #fff;
    }
    .btn-logout {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        transition: 0.3s;
    }
    .btn-logout:hover {
        background: #ef4444;
        color: #fff;
    }
</style>

<script>
    function openEditModal() {
        Swal.fire({
            title: 'Edit Profile',
            input: 'text',
            inputLabel: 'Your Name',
            inputValue: '{{ auth()->user()->name }}',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // TODO: Implement profile update
                Swal.fire({
                    title: 'Success!',
                    text: 'Profile updated successfully',
                    icon: 'success',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#22c55e'
                });
            }
        });
    }

    function openPasswordModal() {
        Swal.fire({
            title: 'Change Password',
            html:
                '<input id="swal-input1" class="swal2-input" placeholder="Current Password" type="password">' +
                '<input id="swal-input2" class="swal2-input" placeholder="New Password" type="password">' +
                '<input id="swal-input3" class="swal2-input" placeholder="Confirm New Password" type="password">',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonColor: '#fbbf24',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Change Password',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff',
            preConfirm: () => {
                const current = document.getElementById('swal-input1').value;
                const newPass = document.getElementById('swal-input2').value;
                const confirm = document.getElementById('swal-input3').value;

                if (!current || !newPass || !confirm) {
                    Swal.showValidationMessage('Please fill all fields');
                }
                if (newPass !== confirm) {
                    Swal.showValidationMessage('Passwords do not match');
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // TODO: Implement password change
                Swal.fire({
                    title: 'Success!',
                    text: 'Password changed successfully',
                    icon: 'success',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#22c55e'
                });
            }
        });
    }

    function logout() {
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('logout') }}';
            }
        });
    }
</script>
@endpush
@endsection
