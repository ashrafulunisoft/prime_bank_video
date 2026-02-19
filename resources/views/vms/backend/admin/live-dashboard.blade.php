@extends('layouts.admin')

@section('title', 'Admin Live Dashboard - Prime Bank Limited')

@section('content')
<div class="glass-card glass-card-dark">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
        <div class="d-flex align-items-center gap-3">
            <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
            <div>
                <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Live Dashboard</h2>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%); border:1px solid rgba(251, 191, 36, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Pending Host</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $visits->where('status', 'pending_host')->count() }}</h2>
                        <div class="text-white-50 small">Awaiting approval</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(251, 191, 36, 0.2); border-radius: 12px;">
                        <i class="fas fa-clock text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%); border:1px solid rgba(59, 130, 246, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Approved</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $visits->where('status', 'approved')->count() }}</h2>
                        <div class="text-white-50 small">RFID generated</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <i class="fas fa-check-circle text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%); border:1px solid rgba(34, 197, 94, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Checked In</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $visits->where('status', 'checked_in')->count() }}</h2>
                        <div class="text-white-50 small">On premises</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(34, 197, 94, 0.2); border-radius: 12px;">
                        <i class="fas fa-sign-in-alt text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(67, 56, 202, 0.1) 100%); border:1px solid rgba(79, 70, 229, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Total Active</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $visits->count() }}</h2>
                        <div class="text-white-50 small">Today's visits</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(79, 70, 229, 0.2); border-radius: 12px;">
                        <i class="fas fa-users text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Visitors Table Section -->
    <div class="permission-title">
        <i class="fas fa-list me-2"></i>
        Active Visitors
        <span class="badge bg-primary ms-auto" style="font-size: 0.8rem;">{{ $visits->count() }} visitors</span>
    </div>

    <div class="glass-card-light p-4 mb-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success" style="border: 1px solid rgba(34, 197, 94, 0.3);">
                    <i class="fas fa-bolt me-1"></i> Live Updates
                </span>
                <span class="text-white-50 small">
                    <i class="fas fa-clock me-1"></i>
                    Last updated: <span id="lastUpdated">Just now</span>
                </span>
            </div>
            <button onclick="loadVisitors()" class="btn btn-sm btn-gradient" style="padding: 0.5rem 1.5rem; font-size: 0.75rem;">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>

        <div class="table-responsive" style="border-radius: 12px; overflow: hidden;">
            <table class="table table-dark mb-0" style="margin: 0;" id="liveVisitsTable">
                <thead>
                    <tr style="background: rgba(0, 0, 0, 0.3);">
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">ID</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Visitor</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Contact</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Host</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Purpose</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Status</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">RFID</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Scheduled</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Check-in</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                        <tr id="visit-{{ $visit->id }}" data-status="{{ $visit->status }}" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s;">
                            <td style="padding: 1rem; color: #fff;">
                                <span class="fw-800" style="color: #3b82f6;">#{{ $visit->id }}</span>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="fw-800 text-white mb-1">{{ $visit->visitor->name }}</div>
                                <div class="text-white-50 small">{{ $visit->visitor->email }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="text-white-50 small">
                                    <i class="fas fa-phone me-1"></i>{{ $visit->visitor->phone ?? 'N/A' }}
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ substr($visit->meetingUser->name, 0, 1) }}
                                    </div>
                                    <span class="text-white fw-600">{{ $visit->meetingUser->name }}</span>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="text-white-50 small">{{ $visit->purpose }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge {{ getStatusBadge($visit->status) }}" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; font-weight: 800;">
                                    {{ formatStatus($visit->status) }}
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                @if($visit->rfid)
                                    <code class="badge bg-info" style="font-size: 0.7rem; padding: 0.4rem 0.8rem;">
                                        {{ $visit->rfid }}
                                    </code>
                                @else
                                    <span class="text-white-50 small">N/A</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <div class="text-white-50 small">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($visit->schedule_time)->format('M j, g:i A') }}
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                @if($visit->checkin_time)
                                    <div class="text-success small">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ \Carbon\Carbon::parse($visit->checkin_time)->format('g:i A') }}
                                    </div>
                                @else
                                    <span class="text-white-50 small">Not checked in</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <div class="d-flex gap-2">
                                    @if($visit->status === 'approved')
                                        <button onclick="checkIn({{ $visit->id }})" class="btn-circle btn-success" title="Check In">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </button>
                                    @endif

                                    @if($visit->status === 'checked_in')
                                        <button onclick="checkOut({{ $visit->id }})" class="btn-circle btn-warning" title="Check Out">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.visitor.edit', $visit->id) }}" class="btn-circle btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($visit->status === 'pending_host')
                                        <a href="{{ route('admin.visitor.edit', $visit->id) }}" class="btn-circle btn-primary" title="Approve/Reject">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding: 3rem; text-align: center;">
                                <div class="text-white-50">
                                    <i class="fas fa-users-slash mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="fw-800 mb-2">No Active Visitors</h5>
                                    <p class="mb-0 small">There are currently no active visitors in the system.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="d-flex justify-content-between align-items-center text-white-50 small" style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <div>
            <i class="fas fa-bolt me-1"></i>
            Real-time updates via WebSocket
        </div>
        <div>
            <i class="fas fa-shield-alt me-1"></i>
            Admin - Full Access
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#liveVisitsTable tbody');

    // Function to get status badge class
    function getStatusBadge(status) {
        const badges = {
            'pending_host': 'bg-warning bg-opacity-10 text-warning',
            'approved': 'bg-info bg-opacity-10 text-info',
            'checked_in': 'bg-success bg-opacity-10 text-success',
            'pending_otp': 'bg-secondary bg-opacity-10 text-white-50',
            'rejected': 'bg-danger bg-opacity-10 text-danger',
            'completed': 'bg-dark bg-opacity-10 text-white-50'
        };
        return badges[status] || 'bg-secondary bg-opacity-10 text-white-50';
    }

    // Function to format status text
    function formatStatus(status) {
        return status.replace(/_/g, ' ').toUpperCase();
    }

    // Function to get row animation class
    function getRowAnimation(status) {
        if (status === 'checked_in') return 'fade-in-success';
        if (status === 'rejected') return 'fade-out';
        return 'fade-in';
    }

    // Function to add or update table row
    function addOrUpdateRow(visit) {
        const existingRow = document.getElementById(`visit-${visit.id}`);

        const rowHtml = `
            <tr id="visit-${visit.id}" data-status="${visit.status}" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s;">
                <td style="padding: 1rem; color: #fff;">
                    <span class="fw-800" style="color: #3b82f6;">#${visit.id}</span>
                </td>
                <td style="padding: 1rem;">
                    <div class="fw-800 text-white mb-1">${visit.visitor.name}</div>
                    <div class="text-white-50 small">${visit.visitor.email}</div>
                </td>
                <td style="padding: 1rem;">
                    <div class="text-white-50 small">
                        <i class="fas fa-phone me-1"></i>${visit.visitor.phone || 'N/A'}
                    </div>
                </td>
                <td style="padding: 1rem;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                            ${visit.meeting_user.name.charAt(0)}
                        </div>
                        <span class="text-white fw-600">${visit.meeting_user.name}</span>
                    </div>
                </td>
                <td style="padding: 1rem;">
                    <div class="text-white-50 small">${visit.purpose}</div>
                </td>
                <td style="padding: 1rem;">
                    <span class="badge ${getStatusBadge(visit.status)}" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; font-weight: 800;">
                        ${formatStatus(visit.status)}
                    </span>
                </td>
                <td style="padding: 1rem;">
                    ${visit.rfid ? `<code class="badge bg-info" style="font-size: 0.7rem; padding: 0.4rem 0.8rem;">${visit.rfid}</code>` : '<span class="text-white-50 small">N/A</span>'}
                </td>
                <td style="padding: 1rem;">
                    <div class="text-white-50 small">
                        <i class="fas fa-calendar me-1"></i>
                        ${formatDate(visit.schedule_time)}
                    </div>
                </td>
                <td style="padding: 1rem;">
                    ${visit.checkin_time ? `<div class="text-success small"><i class="fas fa-check-circle me-1"></i>${formatTime(visit.checkin_time)}</div>` : '<span class="text-white-50 small">Not checked in</span>'}
                </td>
                <td style="padding: 1rem;">
                    <div class="d-flex gap-2">
                        ${getActionButtons(visit)}
                    </div>
                </td>
            </tr>
        `;

        if (existingRow) {
            existingRow.outerHTML = rowHtml;
            updateLastUpdated();
        } else {
            tableBody.insertAdjacentHTML('afterbegin', rowHtml);
            updateLastUpdated();
        }

        updateVisitorCount();
    }

    // Function to remove table row
    function removeRow(visitId) {
        const row = document.getElementById(`visit-${visitId}`);
        if (row) {
            row.remove();
            updateVisitorCount();
            updateLastUpdated();
        }
    }

    // Function to get action buttons (admin has full access)
    function getActionButtons(visit) {
        let buttons = '';

        // Admin can check-in/check-out anyone
        if (visit.status === 'approved') {
            buttons += `<button onclick="checkIn(${visit.id})" class="btn-circle btn-success" title="Check In"><i class="fas fa-sign-in-alt"></i></button>`;
        }
        if (visit.status === 'checked_in') {
            buttons += `<button onclick="checkOut(${visit.id})" class="btn-circle btn-warning" title="Check Out"><i class="fas fa-sign-out-alt"></i></button>`;
        }

        // Admin can view and edit all visits
        buttons += `<a href="/admin/visitor/${visit.id}/edit" class="btn-circle btn-info" title="View/Edit"><i class="fas fa-eye"></i></a>`;

        if (visit.status === 'pending_host') {
            buttons += `<a href="/admin/visitor/${visit.id}/edit" class="btn-circle btn-primary" title="Approve/Reject"><i class="fas fa-edit"></i></a>`;
        }

        return buttons;
    }

    // Function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
    }

    // Function to format time
    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
    }

    // Function to update visitor count
    function updateVisitorCount() {
        const count = tableBody.querySelectorAll('tr').length;
        // Update badge count if needed
    }

    // Function to update last updated time
    function updateLastUpdated() {
        const now = new Date();
        document.getElementById('lastUpdated').textContent = 'Just now';
    }

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
                        Swal.fire('Success!', data.message, 'success');
                        loadVisitors();
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
                        Swal.fire('Success!', data.message, 'success');
                        loadVisitors();
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

    // Load visitors function - uses admin API endpoint
    function loadVisitors() {
        fetch('/api/admin/visitors/live')
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                data.forEach(visit => addOrUpdateRow(visit));
            })
            .catch(error => {
                console.error('Error loading visitors:', error);
                Swal.fire('Error', 'Failed to load visitors', 'error');
            });
    }

    // Initialize Echo for real-time updates (if configured)
    @if(config('broadcasting.default') === 'reverb')
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ config('broadcasting.connections.reverb.key') }}',
            wsHost: '{{ config('broadcasting.connections.reverb.host') }}',
            wsPort: '{{ config('broadcasting.connections.reverb.port') }}',
            wssPort: '{{ config('broadcasting.connections.reverb.port') }}',
            forceTLS: '{{ config('broadcasting.connections.reverb.scheme') }}' === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        // Listen to live-visits channel
        Echo.channel('live-visits')
            .listen('VisitApproved', (e) => {
                addOrUpdateRow(e.visit);
            })
            .listen('VisitWaitingForApproval', (e) => {
                addOrUpdateRow(e.visit);
            })
            .listen('VisitCheckedIn', (e) => {
                addOrUpdateRow(e.visit);
            })
            .listen('VisitCompleted', (e) => {
                removeRow(e.visit.id);
            })
            .listen('VisitRejected', (e) => {
                removeRow(e.visit.id);
            })
            .error((error) => {
                console.error('Echo error:', error);
            });
    @endif
});
</script>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    .fade-in-success {
        animation: fadeInSuccess 0.5s ease-in;
    }

    .fade-out {
        animation: fadeOut 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity:1; transform: translateY(0); }
    }

    @keyframes fadeInSuccess {
        from { opacity: 0; transform: scale(0.95); background-color: rgba(34, 197, 94, 0.2); }
        to { opacity: 1; transform: scale(1); background-color: transparent; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    /* Custom scrollbar for table */
    #liveVisitsTable {
        --bs-table-bg: transparent;
    }

    #liveVisitsTable tbody tr:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .btn-circle {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s;
        color: inherit;
        text-decoration: none;
    }

    .btn-circle:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-success {
        background: rgba(34, 197, 94, 0.2);
        color: #4ade80;
        border:1px solid rgba(34, 197, 94, 0.4);
    }

    .btn-warning {
        background: rgba(234, 179, 8, 0.2);
        color: #facc15;
        border:1px solid rgba(234, 179, 8, 0.4);
    }

    .btn-info {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border:1px solid rgba(59, 130, 246, 0.4);
    }

    .btn-primary {
        background: rgba(79, 70, 229, 0.2);
        color: #818cf8;
        border:1px solid rgba(79, 70, 229, 0.4);
    }

    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 800;
    }
</style>
@endpush

@php
    function getStatusBadge($status) {
        $badges = [
            'pending_host' => 'bg-warning bg-opacity-10 text-warning',
            'approved' => 'bg-info bg-opacity-10 text-info',
            'checked_in' => 'bg-success bg-opacity-10 text-success',
            'pending_otp' => 'bg-secondary bg-opacity-10 text-white-50',
            'rejected' => 'bg-danger bg-opacity-10 text-danger',
            'completed' => 'bg-dark bg-opacity-10 text-white-50'
        ];
        return $badges[$status] ?? 'bg-secondary bg-opacity-10 text-white-50';
    }

    function formatStatus($status) {
        return str_replace('_', ' ', strtoupper($status));
    }
@endphp
