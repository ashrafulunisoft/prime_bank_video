@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="glass-card glass-card-dark">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
                <div>
                    <h6 class="fw-800 mb-0 text-white text-shadow-white" style="font-size: 1.1rem;">Prime Bank Limited</h6>
                    <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">VISITOR SYSTEM</span>
                </div>
            </div>
            <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Visitor List</h2>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Total Visitors</h6>
                            <h3 class="text-white fw-800 mb-0">{{ $visitors->total() }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.2);">
                            <i class="fas fa-users" style="color: var(--accent-blue);"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Approved</h6>
                            <h3 class="text-white fw-800 mb-0">{{ $visitors->where('status', 'approved')->count() }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(34, 197, 94, 0.2);">
                            <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Pending</h6>
                            <h3 class="text-white fw-800 mb-0">{{ $visitors->where('status', 'pending')->count() }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(251, 191, 36, 0.2);">
                            <i class="fas fa-clock" style="color: #fbbf24;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Completed</h6>
                            <h3 class="text-white fw-800 mb-0">{{ $visitors->where('status', 'completed')->count() }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(168, 85, 247, 0.2);">
                            <i class="fas fa-flag-checkered" style="color: #a855f7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor Table -->
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Visitor Name</th>
                        <th>Email</th>
                        <th>Host</th>
                        <th>Visit Type</th>
                        <th>Purpose</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $index => $visit)
                    <tr>
                        <td>{{ ($visitors->currentPage() - 1) * $visitors->perPage() + $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="background: linear-gradient(135deg, var(--accent-blue), #8b5cf6);">
                                    {{ substr($visit->visitor->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white fw-600" style="font-size: 0.9rem;">{{ $visit->visitor->name }}</div>
                                    <div style="font-size: 0.75rem; opacity: 0.6;">{{ $visit->visitor->phone ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size: 0.85rem;">{{ $visit->visitor->email }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-tie" style="opacity: 0.6; font-size: 0.8rem;"></i>
                                <span style="font-size: 0.85rem;">{{ $visit->meetingUser ? $visit->meetingUser->name : 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-visit-type">{{ $visit->type ? $visit->type->name : 'N/A' }}</span>
                        </td>
                        <td style="font-size: 0.85rem; max-width: 150px;">{{ Str::limit($visit->purpose, 30) }}</td>
                        <td style="font-size: 0.85rem;">
                            <i class="fas fa-calendar-alt me-2" style="opacity: 0.6; font-size: 0.75rem;"></i>
                            {{ \Carbon\Carbon::parse($visit->schedule_time)->format('M d, Y') }}
                        </td>
                        <td>
                            @if($visit->status === 'approved')
                                <span class="badge badge-approved">
                                    <i class="fas fa-check-circle me-1"></i> Approved
                                </span>
                            @elseif($visit->status === 'pending')
                                <span class="badge badge-pending">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @elseif($visit->status === 'completed')
                                <span class="badge badge-completed">
                                    <i class="fas fa-flag-checkered me-1"></i> Completed
                                </span>
                            @elseif($visit->status === 'cancelled')
                                <span class="badge badge-cancelled">
                                    <i class="fas fa-times-circle me-1"></i> Cancelled
                                </span>
                            @else
                                <span class="badge" style="background: rgba(107, 114, 128, 0.2); color: #94a3b8;">{{ ucfirst($visit->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-edit" onclick="openEditModal({{ $visit->id }}, '{{ $visit->visitor->name }}', '{{ $visit->visitor->email }}', '{{ $visit->visitor->phone ?? '' }}', '{{ $visit->visitor->address ?? '' }}', '{{ $visit->meetingUser ? $visit->meetingUser->name : '' }}', '{{ $visit->purpose }}', '{{ $visit->schedule_time }}', {{ $visit->visit_type_id }}, '{{ $visit->status }}')" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteVisitor({{ $visit->id }})" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-users-slash" style="font-size: 48px; opacity: 0.3; margin-bottom: 1rem;"></i>
                            <div class="text-white" style="opacity: 0.5;">No visitors found</div>
                            <a href="{{ route('admin.visitor.registration.create') }}" class="btn-gradient" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.5rem; border-radius: 8px; text-decoration: none;">
                                <i class="fas fa-plus me-2"></i>Register New Visitor
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($visitors->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <div class="text-white" style="font-size: 0.85rem; opacity: 0.7;">
                Showing {{ ($visitors->currentPage() - 1) * $visitors->perPage() + 1 }}
                to {{ min($visitors->currentPage() * $visitors->perPage(), $visitors->total()) }}
                of {{ $visitors->total() }} entries
            </div>
            {{ $visitors->links('vendor.pagination.bootstrap-5') }}
        </div>
        @endif
    </div>

    <!-- Edit Visitor Modal -->
    <div class="modal fade" id="editVisitorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glass-card-dark">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding: 1.5rem;">
                    <h5 class="modal-title text-white fw-800" style="font-size: 1.25rem;">Edit Visitor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="editVisitorForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="visit_id" id="edit_visit_id">

                    <div class="modal-body" style="padding: 2rem;">
                        <h6 class="text-white fw-800 mb-3" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6;">Personal Information</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-user input-icon"></i> Visitor Name</label>
                                    <input type="text" class="input-dark" name="name" id="edit_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-envelope input-icon"></i> Email</label>
                                    <input type="email" class="input-dark" name="email" id="edit_email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-phone input-icon"></i> Phone</label>
                                    <input type="text" class="input-dark" name="phone" id="edit_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-building input-icon"></i> Company</label>
                                    <input type="text" class="input-dark" name="company" id="edit_company">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-white fw-800 mb-3" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6; margin-top: 2rem;">Visit Details</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label class="form-label"><i class="fas fa-user-tie input-icon"></i> Host Name</label>
                                    <input type="text" class="input-dark" name="host_name" id="edit_host_name" required list="edit_host_list">
                                    <datalist id="edit_host_list">
                                        @foreach($visitors->pluck('meetingUser.name') as $host)
                                            @if($host)
                                                <option value="{{ $host }}">
                                            @endif
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-calendar input-icon"></i> Visit Date</label>
                                    <input type="date" class="input-dark" name="visit_date" id="edit_visit_date" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-list input-icon"></i> Visit Type</label>
                                    <select class="input-dark" name="visit_type_id" id="edit_visit_type_id" required>
                                        <option value="">Select Type</option>
                                        @foreach($visitors->pluck('type') as $type)
                                            @if($type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-info-circle input-icon"></i> Status</label>
                                    <select class="input-dark" name="status" id="edit_status" required>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-comment input-icon"></i> Purpose</label>
                            <textarea class="input-dark" name="purpose" id="edit_purpose" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1); padding: 1.5rem;">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 0.75rem 2rem; border-radius: 100px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn-gradient" style="padding: 0.75rem 2rem; border-radius: 100px;">
                            <i class="fas fa-save me-2"></i>Update Visitor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
    <style>
        .stat-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 1.5rem;
            transition: 0.3s;
        }

        .stat-card:hover {
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom tbody th {
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-align: left;
            border-bottom: 2px solid rgba(59, 130, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            background: rgba(15, 23, 42, 0.3);
            transition: 0.2s;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }

        .table-custom tbody tr:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        .table-custom td {
            padding: 1rem;
            color: rgba(255, 255, 255, 0.8);
            vertical-align: middle;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
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

        .badge-approved {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .badge-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .badge-completed {
            background: rgba(168, 85, 247, 0.2);
            color: #a855f7;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        .badge-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            font-size: 0.85rem;
        }

        .btn-edit {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .btn-edit:hover {
            background: #fbbf24;
            color: #000;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: #fff;
        }

        .pagination {
            margin: 0;
        }

        .page-link {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 8px;
            transition: 0.2s;
        }

        .page-link:hover {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
        }

        .page-item.active .page-link {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
        }

        .modal-content {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .modal-body {
            color: #fff;
        }

        .modal-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>

    <script>
        function openEditModal(id, name, email, phone, company, hostName, purpose, visitDate, visitTypeId, status) {
            document.getElementById('edit_visit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone || '';
            document.getElementById('edit_company').value = company || '';
            document.getElementById('edit_host_name').value = hostName || '';
            document.getElementById('edit_purpose').value = purpose;
            document.getElementById('edit_visit_date').value = visitDate.split(' ')[0];
            document.getElementById('edit_visit_type_id').value = visitTypeId;
            document.getElementById('edit_status').value = status;

            document.getElementById('editVisitorForm').action = '/admin/visitor/' + id + '/update';

            var modal = new bootstrap.Modal(document.getElementById('editVisitorModal'));
            modal.show();
        }

        function deleteVisitor(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                background: '#0f172a',
                color: '#fff',
                customClass: {
                    confirmButton: 'btn-gradient',
                    cancelButton: 'btn-danger'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/admin/visitor/' + id + '/delete', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
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
                                text: 'Failed to delete visitor',
                                icon: 'error',
                                background: '#0f172a',
                                color: '#fff',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while deleting',
                            icon: 'error',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }

        document.getElementById('editVisitorForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const visitId = formData.get('visit_id');

            fetch('/admin/visitor/' + visitId + '/update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message || 'Visitor updated successfully!',
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
                        text: data.message || 'Failed to update visitor',
                        icon: 'error',
                        background: '#0f172a',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                let errorMessage = 'An error occurred while updating';

                if (error.errors) {
                    // Handle Laravel validation errors
                    const firstError = Object.values(error.errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                } else if (error.message) {
                    errorMessage = error.message;
                }

                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
                });
            });
        });
    </script>
@endpush
@endsection
