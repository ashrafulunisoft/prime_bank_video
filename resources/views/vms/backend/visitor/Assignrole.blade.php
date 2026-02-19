@extends('layouts.receptionist')

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
                <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Assign Role</h2>
            </div>
        </div>

        <form action="{{ route('visitor.role.assign.store') }}" method="POST" id="assign-role-form">
            @csrf

            <!-- Employee Info Section -->
            <div class="permission-title">Employee Information</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Employee Name *</label>
                    <div class="position-relative">
                        <select name="user_id" class="input-dark input-custom" id="user-select" onchange="handleUserChange()" required>
                            <option value="" disabled selected>Select an employee</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" data-id="{{ $user->id }}" data-name="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Employee ID</label>
                    <div class="position-relative">
                        <input type="text" id="employee-id" class="input-dark" placeholder="Auto-filled" disabled>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <div class="position-relative">
                        <select class="input-dark" id="department" disabled>
                            <option value="">Auto-filled</option>
                            <option value="Operations">Operations</option>
                            <option value="Finance">Finance</option>
                            <option value="IT Security">IT Security</option>
                            <option value="Human Resources">Human Resources</option>
                        </select>
                        <i class="fas fa-building input-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Role Assignment Section -->
            <div class="permission-title">Role Assignment</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Select Role *</label>
                    <div class="position-relative">
                        <select name="role_id" class="input-dark input-custom" id="role-select" required>
                            <option value="" disabled selected>Choose a functional role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-user-shield input-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role Type</label>
                    <div class="position-relative">
                        <input type="text" class="input-dark" value="Pre-Registration" disabled>
                        <i class="fas fa-ban input-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role Status</label>
                    <div class="position-relative">
                        <select name="status" class="input-dark input-custom" id="role-status">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="restricted">Restricted</option>
                        </select>
                        <i class="fas fa-toggle-on input-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Effective Date</label>
                    <div class="position-relative">
                        <input type="date" name="effective_date" class="input-dark input-custom" value="{{ date('Y-m-d') }}">
                        <i class="fas fa-calendar-alt input-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Permission Assignment Section -->
            <div class="permission-title">Permission Assignment</div>
            <div class="glass-card-light p-4 mb-5" style="background: rgba(255, 255, 255, 0.03);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" id="select-all-permissions" class="permission-checkbox" style="width: 18px; height: 18px; cursor: pointer;">
                        <label for="select-all-permissions" class="mb-0 small fw-800 cursor-pointer">Select All Permissions</label>
                    </div>
                    <span class="small text-white-50" id="selected-count">0 selected</span>
                </div>

                <div class="row g-3" id="permissions-container">
                    @if($permissions->count() > 0)
                        @foreach($permissions as $permission)
                        <div class="col-md-4 col-sm-6">
                            <div class="permission-item p-3" style="background: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05);">
                                <label class="d-flex align-items-start gap-2 cursor-pointer mb-0">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           class="permission-checkbox mt-1"
                                           style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;">
                                    <div>
                                        <span class="small fw-800 d-block">{{ ucfirst(str_replace('_', ' ', $permission->name)) }}</span>
                                        <span class="fs-9 text-white-50 d-block">ID: {{ $permission->id }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-shield-alt mb-2 text-white-50" style="font-size: 2rem;"></i>
                            <p class="mb-0 text-white-50 small">No permissions available. Please create permissions first.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Section -->
            <div class="permission-title">Approval & Remarks</div>
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <label class="form-label">Remarks (Optional)</label>
                    <div class="position-relative">
                        <textarea name="remarks" class="input-dark input-custom" rows="3" placeholder="Additional notes for auditing purposes..." style="resize: none; padding-right: 45px;"></textarea>
                        <i class="fas fa-comment-dots input-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex flex-wrap justify-content-end gap-3 mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <a href="{{ route('dashboard') }}" class="btn-outline btn-reset" style="text-decoration: none;">
                    Cancel
                </a>
                <button type="button" class="btn-outline btn-danger" onclick="handleRemoveRole()">
                    <i class="fas fa-user-minus"></i> Remove Role
                </button>
                <button type="submit" class="btn-gradient btn-create">
                    <i class="fas fa-check-double"></i> Approve Role
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <style>
        /* Hide browser default calendar icon for date inputs */
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            left: -9999px;
            cursor: pointer;
            height: 100%;
            width: 100%;
        }

        /* Hide browser default dropdown arrow for select elements */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none;
        }

        /* Hide browser default search clear button */
        input[type="date"]::-webkit-search-cancel-button,
        input[type="text"]::-webkit-search-cancel-button {
            display: none;
        }
    </style>
    <script>
        // Permission checkbox functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all-permissions');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox:not(#select-all-permissions)');
            const selectedCount = document.getElementById('selected-count');

            // Update selected count
            function updateSelectedCount() {
                const checked = document.querySelectorAll('.permission-checkbox:not(#select-all-permissions):checked').length;
                selectedCount.textContent = checked + ' selected';
            }

            // Select All functionality
            selectAllCheckbox.addEventListener('change', function() {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                    // Highlight/unhighlight the parent div
                    const parentDiv = checkbox.closest('.permission-item');
                    if (checkbox.checked) {
                        parentDiv.style.background = 'rgba(16, 185, 129, 0.1)';
                        parentDiv.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                    } else {
                        parentDiv.style.background = 'rgba(255, 255, 255, 0.02)';
                        parentDiv.style.borderColor = 'rgba(255, 255, 255, 0.05)';
                    }
                });
                updateSelectedCount();
            });

            // Individual checkbox change
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Highlight/unhighlight the parent div
                    const parentDiv = checkbox.closest('.permission-item');
                    if (checkbox.checked) {
                        parentDiv.style.background = 'rgba(16, 185, 129, 0.1)';
                        parentDiv.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                    } else {
                        parentDiv.style.background = 'rgba(255, 255, 255, 0.02)';
                        parentDiv.style.borderColor = 'rgba(255, 255, 255, 0.05)';
                    }
                    updateSelectedCount();
                });
            });

            // Update count on load
            updateSelectedCount();
        });

        function handleUserChange() {
            const select = document.getElementById('user-select');
            const idInput = document.getElementById('employee-id');
            const deptSelect = document.getElementById('department');

            const selected = select.options[select.selectedIndex];

            if(selected.value) {
                idInput.value = selected.getAttribute('data-id');
                deptSelect.value = selected.getAttribute('data-name');

                // Visual feedback
                idInput.style.borderColor = '#10b981';
                setTimeout(() => { idInput.style.borderColor = ''; }, 1000);
            }
        }

        function handleRemoveRole() {
            const select = document.getElementById('user-select');
            const userName = select.options[select.selectedIndex]?.text;

            if(!select.value) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select an employee first',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'Warning: Are you sure you want to remove current role assignment for ' + userName + '? This action is logged.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#3b82f6'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('visitor.role.assign.remove') }}';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrfInput);

                    const userIdInput = document.createElement('input');
                    userIdInput.type = 'hidden';
                    userIdInput.name = 'user_id';
                    userIdInput.value = select.value;
                    form.appendChild(userIdInput);

                    document.body.appendChild(form);
                    form.submit();
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
                timer: 2000,
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
@endsection
