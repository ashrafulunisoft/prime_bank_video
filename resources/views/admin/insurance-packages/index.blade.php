@extends('layouts.admin')

@section('title', 'Insurance Packages - Prime Bank Limited')

@section('content')
<div class="glass-card glass-card-dark col-12 mt-4">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
        <div class="d-flex align-items-center gap-3">
            <div class="logo-vms" style="width: 44px; height: 44px; font-size: 1.2rem; background: #fff; color: #1e293b; border-radius: 10px; font-weight: 900; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(255,255,255,0.2);">V</div>
            <div>
                <h6 class="fw-800 mb-0 text-white text-shadow-white">Prime Bank Limited</h6>
                <span class="permission-title" style="font-size: 0.7rem; margin: 0; text-shadow-blue">ADMIN PANEL</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                <h2 class="fw-800 mb-0 text-white letter-spacing-1 text-shadow-white" style="font-size: 2rem;">Insurance Packages</h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%); border:1px solid rgba(59, 130, 246, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Total Packages</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $packages->total() }}</h2>
                        <div class="text-white-50 small">All packages</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <i class="fas fa-box text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%); border:1px solid rgba(34, 197, 94, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Active</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $packages->where('is_active', true)->count() }}</h2>
                        <div class="text-white-50 small">Currently active</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(34, 197, 94, 0.2); border-radius: 12px;">
                        <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(185, 28, 28, 0.1) 100%); border:1px solid rgba(239, 68, 68, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Inactive</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2.5rem;">{{ $packages->where('is_active', false)->count() }}</h2>
                        <div class="text-white-50 small">Currently inactive</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.2); border-radius: 12px;">
                        <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%); border:1px solid rgba(16, 185, 129, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-800 text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Starting From</div>
                        <h2 class="fw-800 mb-0 text-white" style="font-size: 2rem;">${{ number_format($packages->min('price') ?? 0, 2) }}</h2>
                        <div class="text-white-50 small">Lowest price</div>
                    </div>
                    <div class="summary-icon" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.2); border-radius: 12px;">
                        <i class="fas fa-tag text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="permission-title">
        <i class="fas fa-list me-2"></i>
        All Insurance Packages
        <span class="badge bg-primary ms-auto" style="font-size: 0.8rem;">{{ $packages->total() }} packages</span>
    </div>

    <div class="glass-card-light p-4 mb-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info bg-opacity-10 text-info" style="border: 1px solid rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-database me-1"></i> Packages
                </span>
                <span class="text-white-50 small">
                    <i class="fas fa-clock me-1"></i>
                    Page {{ $packages->currentPage() }} of {{ $packages->lastPage() }}
                </span>
            </div>
            <a href="{{ route('insurance-packages.create') }}" class="btn btn-create btn-primary btn-gradient" style="padding: 0.6rem 1.5rem; font-size: 0.8rem;">
                <i class="fas fa-plus me-1"></i> Add Package
            </a>
        </div>

        <div class="table-responsive" style="border-radius: 12px; overflow: hidden;">
            <table class="table table-dark mb-0" style="margin: 0;">
                <thead>
                    <tr style="background: rgba(0, 0, 0, 0.3);">
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">ID</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Package Name</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Price</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Coverage</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Duration</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Status</th>
                        <th style="padding: 1rem; border: none; color: #fff; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s;">
                            <td style="padding: 1rem; color: #fff;">
                                <span class="fw-800" style="color: #3b82f6;">#{{ $package->id }}</span>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="fw-800 text-white mb-1">{{ $package->name }}</div>
                                <div class="text-white-50 small">{{ Str::limit($package->description, 35) }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="text-success fw-bold" style="font-size: 1.1rem;">${{ number_format($package->price, 2) }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="text-white-50 small">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    ${{ number_format($package->coverage_amount, 2) }}
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="text-white-50 small">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $package->duration_months }} Months
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge {{ $package->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; font-weight: 800; border: 1px solid {{ $package->is_active ? 'rgba(34, 197, 94, 0.4)' : 'rgba(239, 68, 68, 0.4)' }};">
                                    {{ $package->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('insurance-packages.show', $package->id) }}" class="btn-circle btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('insurance-packages.edit', $package->id) }}" class="btn-circle btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.insurance-packages.toggle-status', $package->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn-circle {{ $package->is_active ? 'btn-secondary' : 'btn-success' }}" title="{{ $package->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn-circle btn-danger delete-btn" data-id="{{ $package->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form action="{{ route('insurance-packages.destroy', $package->id) }}" method="POST" class="d-none delete-form-{{ $package->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 3rem; text-align: center;">
                                <div class="text-white-50">
                                    <i class="fas fa-box-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="fw-800 mb-2">No Packages Found</h5>
                                    <p class="mb-3 small">There are no insurance packages in the system.</p>
                                    <a href="{{ route('insurance-packages.create') }}" class="btn btn-create btn-primary btn-gradient btn-sm">
                                        <i class="fas fa-plus me-1"></i> Create First Package
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <div class="text-white-50 small">
                Showing {{ $packages->firstItem() ?? 0 }} to {{ $packages->lastItem() ?? 0 }} of {{ $packages->total() }} packages
            </div>
            <div>
                {{ $packages->links() }}
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center text-white-50 small" style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <div>
            <i class="fas fa-cog me-1"></i>
            Insurance Package Management
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
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success', text: '{{ session("success") }}', timer: 3000, showConfirmButton: false });
        @endif

        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: '{{ session("error") }}', timer: 3000, showConfirmButton: false });
        @endif

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Delete Package?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280'
                }).then(result => {
                    if (result.isConfirmed) {
                        document.querySelector('.delete-form-' + id).submit();
                    }
                });
            });
        });
    });
</script>

<style>
    .btn-circle { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; transition: all 0.3s; color: inherit; text-decoration: none; cursor: pointer; }
    .btn-circle:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); }
    .btn-success { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); }
    .btn-warning { background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.4); }
    .btn-info { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); }
    .btn-secondary { background: rgba(107, 114, 128, 0.2); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.4); }
    .btn-danger { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
    .btn-circle:hover { background: rgba(255, 255, 255, 0.1) !important; color: #fff !important; }
    .pagination .page-link { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; }
    .pagination .page-link:hover { background: rgba(59, 130, 246, 0.2); border-color: rgba(59, 130, 246, 0.4); color: #60a5fa; }
    .pagination .page-item.active .page-link { background: #3b82f6; border-color: #3b82f6; }
    .pagination .page-item.disabled .page-link { background: transparent; border-color: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.3); }
</style>
@endpush
