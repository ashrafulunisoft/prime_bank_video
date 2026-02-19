@extends('layouts.admin')

@section('title', 'Policy List - Prime Bank Limited')

@section('content')
    <!-- Header -->
    <div class="header-section">
        <div>
            <h3 class="fw-800 mb-1 text-white letter-spacing-1">Policy Management</h3>
            <p class="sub-label mb-0">Manage all insurance policies (orders)</p>
        </div>
        <div class="header-profile-box glass-card">
            <div class="avatar bg-primary">
                <i class="fas fa-user-tie text-white small"></i>
            </div>
            <div>
                <p class="small fw-800 mb-0 text-white">{{ Auth::user()->name }}</p>
                <span class="sub-label fs-9">Administrator</span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%); border:1px solid rgba(59, 130, 246, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Total Policies</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $orders->total() }}</h2>
                        <div class="text-white-50 small">All policies</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <i class="fas fa-file-contract text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%); border:1px solid rgba(34, 197, 94, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Active</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $orders->where('status', 'active')->count() }}</h2>
                        <div class="text-white-50 small">Currently active</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(34, 197, 94, 0.2); border-radius: 12px;">
                        <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(234, 179, 8, 0.15) 0%, rgba(202, 138, 4, 0.1) 100%); border:1px solid rgba(234, 179, 8, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Pending</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $orders->where('status', 'pending')->count() }}</h2>
                        <div class="text-white-50 small">Awaiting activation</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(234, 179, 8, 0.2); border-radius: 12px;">
                        <i class="fas fa-clock text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(185, 28, 28, 0.1) 100%); border:1px solid rgba(239, 68, 68, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Expired</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $orders->where('status', 'expired')->count() }}</h2>
                        <div class="text-white-50 small">No longer active</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.2); border-radius: 12px;">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policy List -->
    <div class="glass-card glass-card-dark col-12">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem; background: #fff; color: #1e293b; border-radius: 10px; font-weight: 900; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(255,255,255,0.2);">P</div>
                <div>
                    <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                    <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">All Policies</h2>
                </div>
            </div>
        </div>

        <div class="permission-title">
            <i class="fas fa-list me-2"></i>
            Policy List
            <span class="badge bg-primary ms-auto" style="font-size: 0.8rem;">{{ $orders->total() }} policies</span>
        </div>

        <div class="glass-card-light p-4 mb-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info bg-opacity-10 text-info" style="border: 1px solid rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-database me-1"></i> Policies
                    </span>
                    <span class="text-white-50 small">
                        <i class="fas fa-clock me-1"></i>
                        Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                    </span>
                </div>
            </div>

            <div class="table-responsive" style="border-radius: 12px; overflow: hidden;">
                <table class="table table-dark mb-0" style="margin: 0;">
                    <thead>
                        <tr style="background: rgba(0, 0, 0, 0.3);">
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">ID</th>
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Policy Number</th>
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Customer</th>
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Package</th>
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Duration</th>
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Status</th>
                            <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s;">
                                <td style="padding: 1rem; color: #fff;">
                                    <span class="fw-800" style="color: #3b82f6;">#{{ $order->id }}</span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="fw-800 text-white mb-1">{{ $order->policy_number }}</div>
                                    <div class="text-white-50 small">{{ $order->created_at->format('M d, Y') }}</div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm" style="width: 32px; height: 32px; background: rgba(11, 214, 150, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user text-success" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-white" style="font-size: 0.9rem;">{{ $order->user->name }}</div>
                                            <div class="text-white-50 small" style="font-size: 0.75rem;">{{ $order->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="text-white-50 small">
                                        <i class="fas fa-box me-1"></i>
                                        {{ $order->package->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="text-white-50 small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $order->start_date->format('M d, Y') }} - {{ $order->end_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td style="padding: 1rem;">
                                    @php
                                        $statusClass = match($order->status) {
                                            'active' => 'bg-success bg-opacity-10 text-success',
                                            'pending' => 'bg-warning bg-opacity-10 text-warning',
                                            'expired' => 'bg-danger bg-opacity-10 text-danger',
                                            'cancelled' => 'bg-secondary bg-opacity-10 text-secondary',
                                            default => 'bg-secondary bg-opacity-10 text-secondary'
                                        };
                                        $statusBorder = match($order->status) {
                                            'active' => 'rgba(34, 197, 94, 0.4)',
                                            'pending' => 'rgba(234, 179, 8, 0.4)',
                                            'expired' => 'rgba(239, 68, 68, 0.4)',
                                            'cancelled' => 'rgba(107, 114, 128, 0.4)',
                                            default => 'rgba(107, 114, 128, 0.4)'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; font-weight: 800; border: 1px solid {{ $statusBorder }};">
                                        {{ strtoupper($order->status) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.policies.show', $order->id) }}" class="btn-circle btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 3rem; text-align: center;">
                                    <div class="text-white-50">
                                        <i class="fas fa-folder-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <h5 class="fw-800 mb-2">No Policies Found</h5>
                                        <p class="mb-3 small">There are no policies in the system yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <div class="text-white-50 small">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} policies
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center text-white-50 small" style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
            <div>
                <i class="fas fa-shield-alt me-1"></i>
                Policy Management
            </div>
            <div>
                <i class="fas fa-user-shield me-1"></i>
                Admin - Full Access
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success', text: '{{ session("success") }}', timer: 3000, showConfirmButton: false });
        @endif

        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: '{{ session("error") }}', timer: 3000, showConfirmButton: false });
        @endif
    });
</script>

<style>
    .btn-circle { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; transition: all 0.3s; color: inherit; text-decoration: none; cursor: pointer; }
    .btn-circle:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); }
    .btn-info { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); }
    .btn-circle:hover { background: rgba(255, 255, 255, 0.1) !important; color: #fff !important; }
    .pagination .page-link { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; }
    .pagination .page-link:hover { background: rgba(59, 130, 246, 0.2); border-color: rgba(59, 130, 246, 0.4); color: #60a5fa; }
    .pagination .page-item.active .page-link { background: #3b82f6; border-color: #3b82f6; }
    .pagination .page-item.disabled .page-link { background: transparent; border-color: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.3); }
</style>
