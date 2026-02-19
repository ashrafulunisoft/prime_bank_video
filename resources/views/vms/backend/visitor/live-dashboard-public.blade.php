
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Dashboard - Prime Bank Limited</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Pusher for real-time updates -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <style>
        :root {
            --bg-page: radial-gradient(circle at center, #101d42 0%, #060b1d 100%);
            --accent-indigo: #4f46e5;
            --accent-blue: #3b82f6;
            --text-primary: #fff;
            --text-secondary: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-page);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-primary);
            padding: 2rem;
        }

        .container-fluid {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(35px);
            -webkit-backdrop-filter: blur(35px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 40px;
            padding: 4rem;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(59, 130, 246, 0.05);
        }

        .glass-card-light {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
        }

        .glass-table-container {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
        }

        .logo-vms {
            background: linear-gradient(135deg, var(--accent-indigo), var(--accent-blue));
            color: #fff;
            border-radius: 12px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .text-shadow-white {
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.2);
        }

        .text-shadow-blue {
            text-shadow: 0 2px 10px rgba(59, 130, 246, 0.4);
        }

        .permission-title {
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .letter-spacing-1 {
            letter-spacing: 1px;
        }

        .summary-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--accent-indigo), var(--accent-blue));
            border: none;
            color: #fff;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }

        .table-dark {
            background: transparent;
            margin: 0;
        }

        .table-dark thead {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .table-dark th {
            border: none;
            color: #fff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            padding: 1rem;
            background: transparent;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-dark td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1rem;
            transition: all 0.3s;
            color: #fff;
            background: transparent;
        }

        .table-dark tbody tr {
            background: transparent;
            transition: all 0.3s;
        }

        .table-dark tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .table-responsive {
            background: transparent;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 800;
        }

        .rfid-badge {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.4);
            color: #e0f2fe;
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .rfid-badge:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.25) 0%, rgba(99, 102, 241, 0.25) 100%);
            border-color: rgba(59, 130, 246, 0.6);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 1200px) {
            body {
                padding: 1rem;
            }
            .glass-card {
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }
            .glass-card {
                padding: 1.5rem;
                border-radius: 20px;
            }

            .table-dark {
                font-size: 0.85rem;
                white-space: nowrap;
            }

            .table-dark th,
            .table-dark td {
                padding: 0.75rem;
            }

            .glass-table-container {
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            .table-dark thead {
                background: rgba(0, 0, 0, 0.2);
            }

            .table-dark th {
                background: transparent;
            }

            .table-dark td {
                background: transparent;
            }

            .table-dark tbody tr {
                background: transparent;
            }

            .table-dark tbody tr:hover {
                background: rgba(255, 255, 255, 0.05);
            }

            .table-responsive {
                overflow-x: auto;
                background: transparent;
            }
        }

        @media (max-width: 576px) {
            .glass-card {
                padding: 1rem;
            }

            .table-dark {
                font-size: 0.75rem;
                white-space: nowrap;
            }

            .table-dark th,
            .table-dark td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="glass-card">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem;">V</div>
                    <div>
                        <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                        <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">VISITOR SYSTEM</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Live Dashboard</h2>
                    </div>
                    <a href="/" class="btn btn-gradient d-flex align-items-center gap-2" style="padding: 0.75rem 1.5rem; font-size: 0.9rem;">
                        <i class="fas fa-home"></i>
                        <span>Back to Home</span>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="glass-card-light p-4" style="background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%); border: 1px solid rgba(251, 191, 36, 0.3);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Pending Host</div>
                                <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;" id="stat-pending">{{ $visits->where('status', 'pending_host')->count() }}</h2>
                                <div class="text-white-50 small">Awaiting approval</div>
                            </div>
                            <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(251, 191, 36, 0.2); border-radius: 12px;">
                                <i class="fas fa-clock text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card-light p-4" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%); border: 1px solid rgba(59, 130, 246, 0.3);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Approved</div>
                                <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;" id="stat-approved">{{ $visits->where('status', 'approved')->count() }}</h2>
                                <div class="text-white-50 small">RFID generated</div>
                            </div>
                            <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.2); border-radius: 12px;">
                                <i class="fas fa-check-circle text-info" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card-light p-4" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%); border: 1px solid rgba(34, 197, 94, 0.3);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Checked In</div>
                                <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;" id="stat-checked-in">{{ $visits->where('status', 'checked_in')->count() }}</h2>
                                <div class="text-white-50 small">On premises</div>
                            </div>
                            <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(34, 197, 94, 0.2); border-radius: 12px;">
                                <i class="fas fa-sign-in-alt text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card-light p-4" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(67, 56, 202, 0.1) 100%); border: 1px solid rgba(79, 70, 229, 0.3);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Total Active</div>
                                <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;" id="stat-total">{{ $visits->count() }}</h2>
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
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-list me-2"></i>
                <span class="fw-800 text-white" style="font-size: 1.2rem;">Active Visitors</span>
                <span class="badge bg-primary ms-auto" style="font-size: 0.8rem;" id="visitor-count">{{ $visits->count() }} visitors</span>
            </div>

            <div class="glass-card-light p-4 mb-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success" style="border: 1px solid rgba(34, 197, 94, 0.3);">
                            <i class="fas fa-bolt me-1"></i> Live Updates
                        </span>
                        <span class="text-white-50 small">
                            <i class="fas fa-clock me-1"></i> Last updated: <span id="lastUpdated">Just now</span>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                                <tr id="visit-{{ $visit->id }}" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s;">
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
                                            <span class="rfid-badge">
                                                {{ $visit->rfid }}
                                            </span>
                                        @else
                                            <span class="text-white-50 small">N/A</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div class="text-white-50 small">
                                            <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($visit->schedule_time)->format('M j, g:i A') }}
                                        </div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        @if($visit->checkin_time)
                                            <div class="text-success small">
                                                <i class="fas fa-check-circle me-1"></i> {{ \Carbon\Carbon::parse($visit->checkin_time)->format('g:i A') }}
                                            </div>
                                        @else
                                            <span class="text-white-50 small">Not checked in</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="padding: 3rem; text-align: center;">
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
                    <i class="fas fa-bolt me-1"></i> Real-time updates via WebSocket
                </div>
                <div>
                    <i class="fas fa-shield-alt me-1"></i> Secure & Monitored
                </div>
            </div>
        </div>
    </div>

    <script>
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('visits');

        channel.bind('VisitStatusChanged', function(data) {
            updateDashboard();
        });

        setInterval(updateDashboard, 30000);

        function updateDashboard() {
            fetch('/api/visitors/live-public')
                .then(response => response.json())
                .then(data => {
                    renderVisits(data);
                    updateStats(data);
                })
                .catch(error => console.error('Error updating dashboard:', error));
        }

        function renderVisits(visits) {
            const tableBody = document.querySelector('#liveVisitsTable tbody');

            if (visits.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" style="padding: 3rem; text-align: center;">
                            <div class="text-white-50">
                                <i class="fas fa-users-slash mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                <h5 class="fw-800 mb-2">No Active Visitors</h5>
                                <p class="mb-0 small">There are currently no active visitors in the system.</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = visits.map(visit => {
                const rfidBadge = visit.rfid ? `<span class="rfid-badge">${visit.rfid}</span>` : '<span class="text-white-50 small">N/A</span>';
                const checkinTime = visit.checkin_time ? `<div class="text-success small"><i class="fas fa-check-circle me-1"></i>${formatTime(visit.checkin_time)}</div>` : '<span class="text-white-50 small">Not checked in</span>';

                return `
                    <tr id="visit-${visit.id}" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s;">
                        <td style="padding: 1rem; color: #fff;">
                            <span class="fw-800" style="color: #3b82f6;">#${visit.id}</span>
                        </td>
                        <td style="padding: 1rem;">
                            <div class="fw-800 text-white mb-1">${visit.visitor ? visit.visitor.name : 'Unknown'}</div>
                            <div class="text-white-50 small">${visit.visitor ? visit.visitor.email : 'N/A'}</div>
                        </td>
                        <td style="padding: 1rem;">
                            <div class="text-white-50 small">
                                <i class="fas fa-phone me-1"></i>${visit.visitor ? (visit.visitor.phone || 'N/A') : 'N/A'}
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    ${visit.meeting_user ? visit.meeting_user.name.charAt(0) : 'N'}
                                </div>
                                <span class="text-white fw-600">${visit.meeting_user ? visit.meeting_user.name : 'Unknown'}</span>
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
                            ${rfidBadge}
                        </td>
                        <td style="padding: 1rem;">
                            <div class="text-white-50 small">
                                <i class="fas fa-calendar me-1"></i> ${formatDate(visit.schedule_time)}
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            ${checkinTime}
                        </td>
                    </tr>
                `;
            }).join('');
        }

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

        function formatStatus(status) {
            return status.replace(/_/g, ' ').toUpperCase();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
        }

        function updateStats(visits) {
            const pending = visits.filter(v => v.status === 'pending_host').length;
            const approved = visits.filter(v => v.status === 'approved').length;
            const checkedIn = visits.filter(v => v.status === 'checked_in').length;

            document.getElementById('stat-pending').textContent = pending;
            document.getElementById('stat-approved').textContent = approved;
            document.getElementById('stat-checked-in').textContent = checkedIn;
            document.getElementById('stat-total').textContent = visits.length;
            document.getElementById('visitor-count').textContent = `${visits.length} visitors`;
        }

        function updateLastUpdated() {
            document.getElementById('lastUpdated').textContent = 'Just now';
        }
    </script>
</body>
</html>

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
