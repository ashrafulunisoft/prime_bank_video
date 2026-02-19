@extends('layouts.admin')

@section('title', 'Approved Visits - Admin')

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
        <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Approved Visits</h2>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">Total Approved</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $visits->total() }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(34, 197, 94, 0.2);">
                        <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">This Week</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $visits->filter(function($v) { return $v->created_at >= now()->subWeek(); })->count() }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.2);">
                        <i class="fas fa-calendar-week" style="color: var(--accent-blue);"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-2" style="font-size: 0.85rem; opacity: 0.7;">This Month</h6>
                        <h3 class="text-white fw-800 mb-0">{{ $visits->filter(function($v) { return $v->created_at >= now()->subMonth(); })->count() }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(34, 197, 94, 0.2);">
                        <i class="fas fa-calendar-alt" style="color: #22c55e;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
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
                    <th>RFID</th>
                    <th>Approved At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $index => $visit)
                <tr>
                    <td>{{ ($visits->currentPage() - 1) * $visits->perPage() + $index + 1 }}</td>
                    <td>
                        <div class="text-white fw-600" style="font-size: 0.9rem;">{{ $visit->visitor->name }}</div>
                    </td>
                    <td style="font-size: 0.85rem;">{{ $visit->visitor->email }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="background: linear-gradient(135deg, var(--accent-blue), #8b5cf6); width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($visit->meetingUser->name ?? 'N/A', 0, 1) }}
                            </div>
                            <span style="font-size: 0.85rem;">{{ $visit->meetingUser->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-visit-type">{{ $visit->type ? $visit->type->name : 'N/A' }}</span>
                    </td>
                    <td style="font-size: 0.85rem; max-width: 150px;">{{ Str::limit($visit->purpose, 30) }}</td>
                    <td>
                        <span class="badge badge-rfid">{{ $visit->rfid ?? 'N/A' }}</span>
                    </td>
                    <td style="font-size: 0.85rem;">{{ $visit->approved_at ? $visit->approved_at->diffForHumans() : 'N/A' }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.visitor.show', $visit->id) }}" class="action-btn btn-edit" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="action-btn btn-checkin" onclick="checkInVisitor({{ $visit->id }})" title="Check In">
                                <i class="fas fa-sign-in-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <i class="fas fa-check-circle" style="font-size: 48px; opacity: 0.3; margin-bottom: 1rem;"></i>
                        <div class="text-white" style="opacity: 0.5;">No approved visits found</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($visits->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="text-white" style="font-size: 0.85rem; opacity: 0.7;">
            Showing {{ ($visits->currentPage() - 1) * $visits->perPage() + 1 }}
            to {{ min($visits->currentPage() * $visits->perPage(), $visits->total()) }}
            of {{ $visits->total() }} entries
        </div>
        {{ $visits->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
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
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
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
    .badge-rfid {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        font-family: monospace;
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
        text-decoration: none;
    }
    .btn-edit {
        background: rgba(59, 130, 246, 0.2);
        color: var(--accent-blue);
    }
    .btn-edit:hover {
        background: var(--accent-blue);
        color: #fff;
    }
    .btn-checkin {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }
    .btn-checkin:hover {
        background: #22c55e;
        color: #fff;
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
</style>

<script>
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
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to check in visitor',
                        icon: 'error',
                        background: '#0f172a',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }
</script>
@endpush
@endsection
