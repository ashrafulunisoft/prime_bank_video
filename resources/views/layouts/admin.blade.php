<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Prime Bank - Video Call Support Dashboard')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('styles')

    <style>
        :root {
            --bg-page: radial-gradient(circle at center, #101d42 0%, #060b1d 100%);
            --sidebar-bg: #111a30;
            --accent-indigo: #4f46e5;
            --accent-blue: #3b82f6;
            --accent-pink: #db2777;
            --glass-card: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
            --neon-blue: 0 0 20px rgba(59, 130, 246, 0.4), 0 0 40px rgba(59, 130, 246, 0.2);
            --neon-indigo: 0 8px 25px rgba(79, 70, 229, 0.4);
            --sidebar-width: 280px;
            --text-muted: #94a3b8;
        }

        /* --- CUSTOM NEON SCROLLER FOR MOBILE --- */
        .bar-chart-wrapper::-webkit-scrollbar,
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .bar-chart-wrapper::-webkit-scrollbar-track,
        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .bar-chart-wrapper::-webkit-scrollbar-thumb,
        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--accent-blue);
            border-radius: 10px;
            box-shadow: 0 0 10px var(--accent-blue);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-page);
            background-attachment: fixed;
            min-height: 100vh;
            color: #fff;
            margin: 0;
            overflow-x: hidden;
        }

        /* Layout Container */
        .vms-page { display: flex; min-height: 100vh; width: 100%; }

        /* General UI */
        .glass-card {
            background: var(--glass-card);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        /* Sidebar Desktop */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--glass-border);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.2rem;
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.85rem 1.2rem;
            border-radius: 14px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 0.4rem;
            transition: 0.3s;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .sidebar-item:hover, .sidebar-item.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-item.active {
            background: var(--accent-indigo);
            box-shadow: var(--neon-indigo);
        }

        .sidebar-submenu {
            display: none;
            flex-direction: column;
            padding-left: 2.8rem;
            margin-bottom: 0.5rem;
        }
        .sidebar-submenu.active { display: flex; }

        .submenu-item {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Help Center */
        .help-center-box {
            background: linear-gradient(180deg, var(--accent-indigo) 0%, var(--accent-blue) 100%);
            border-radius: 24px;
            padding: 1.5rem;
            margin-top: auto;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .help-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center; justify-content: center;
            margin: -40px auto 1rem;
            border: 4px solid #111a30;
            font-size: 1.2rem; font-weight: 800;
        }

        /* Main Content Desktop */
        .main-container {
            flex: 1;
            padding: 2rem;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }

        /* Header Components */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* Stats Row */
        .summary-card {
            padding: 1.5rem;
            transition: transform 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-card h2 { font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -1px; }
        .summary-icon {
            width: 42px; height: 42px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: var(--accent-blue);
        }

        /* Charts */
        .bar-chart-wrapper {
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .bar-chart {
            display: flex; align-items: end; gap: 15px; height: 200px; min-width: 500px;
        }
        .bar-col {
            flex: 1;
            background: linear-gradient(180deg, var(--accent-indigo), var(--accent-blue));
            border-radius: 8px 8px 0 0;
            box-shadow: var(--neon-blue);
        }

        /* Recent Visits Log Container & Font Fix */
        .log-container table,
        .log-container table * {
            color: #ffffff !important;
        }

        /* Tables */
        .table-responsive {
            border-radius: 15px;
            overflow-x: auto;
        }
        .table { color: #fff !important; --bs-table-bg: transparent; border-color: rgba(255, 255, 255, 0.05); min-width: 800px; }
        .table th {
            text-transform: uppercase; font-size: 10px; letter-spacing: 1.5px;
            color: #fff !important; font-weight: 800; white-space: nowrap;
        }
        .table td { white-space: nowrap; vertical-align: middle; }

        /* Badges & Buttons */
        .status-badge {
            font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 50px;
            background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff !important;
        }
        .btn-circle {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; border: none; transition: 0.3s;
        }
        .btn-accept { background: rgba(34, 197, 94, 0.1); color: #4ade80; }
        .btn-reject { background: rgba(239, 68, 68, 0.1); color: #f87171; }

        /* Mobile Header */
        .mobile-top-nav {
            display: none;
            background: var(--sidebar-bg);
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--glass-border);
            width: 100%;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 992px) {
            .sidebar { left: -280px; }
            .sidebar.show { left: 0; }
            .sidebar-overlay.show { display: block; }
            .main-container { margin-left: 0; padding: 1.2rem; width: 100%; }
            .mobile-top-nav { display: flex; align-items: center; justify-content: space-between; }
            .header-section { flex-direction: column; align-items: flex-start; }
            .header-profile-box { width: 100%; }
        }

        @media (max-width: 576px) {
            .summary-card h2 { font-size: 1.5rem; }
            .main-container { padding: 1rem; }
            .glass-card { border-radius: 16px; padding: 1.2rem !important; }
            .donut-container { width: 140px !important; height: 140px !important; }
            .donut-inner { width: 100px !important; height: 100px !important; }
        }

        .logo-vms {
            background: #fff; color: #1e293b;
            width: 100%;
            max-width: 200px;
            height: auto;
            border-radius: 10px;
            font-weight: 900;
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
        }
        
        @media (max-width: 768px) {
            .logo-vms {
                width: 80%;
                max-width: 150px;
            }
        }
        
        @media (max-width: 576px) {
            .logo-vms {
                width: 70%;
                max-width: 120px;
            }
        }

        .sub-label { color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; }

        /* --- Utility Classes --- */
        .role-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .donut-container {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: conic-gradient(var(--accent-blue) 0% 40%, #10b981 40% 60%, #f59e0b 60% 80%, #ef4444 80% 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donut-inner {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #060b1d;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .header-profile-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-opacity-10 {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* --- Form Styles --- */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-custom:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), 0 0 40px rgba(59, 130, 246, 0.3) !important;
            background: rgba(0, 0, 0, 0.45) !important;
            transform: translateY(-2px);
        }

        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.6), 0 0 40px rgba(239, 68, 68, 0.3) !important;
        }

        .btn-create:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.5);
            filter: brightness(1.1);
        }

        .btn-reset:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 15px 40px rgba(239, 68, 68, 0.5);
            transform: translateY(-3px);
        }

        /* --- Dashboard Specific Styles --- */
        .glass-card-dark {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(35px);
            -webkit-backdrop-filter: blur(35px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 40px;
            padding: 3.5rem;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(59, 130, 246, 0.05);
            position: relative;
        }

        .input-dark {
            width: 100%;
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 14px 20px;
            color: #fff !important;
            font-size: 0.95rem;
            transition: all 0.3s;
            outline: none;
        }

        .checkbox-label {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: 0.3s;
            user-select: none;
        }

        .checkbox-label:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #5046e5, #3b82f6);
            border: none;
            border-radius: 100px;
            padding: 16px;
            color: white;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), 0 0 40px rgba(59, 130, 246, 0.3);
        }

        .btn-outline {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 100px;
            color: #94a3b8;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px;
            transition: 0.3s;
            text-decoration: none;
        }

        .permission-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #3b82f6;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 15px;
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-blue);
            opacity: 0.6;
            font-size: 0.9rem;
            pointer-events: none;
        }

        /* --- Dashboard Utility Classes --- */
        .fs-9 { font-size: 9px; }
        .letter-spacing-1 { letter-spacing: -1px; }
        .border-dashed { border-style: dashed; }
        .border-orange { border-color: orange; }
        .text-shadow-white { text-shadow: 0 0 15px rgba(255, 255, 255, 0.2); }
        .text-shadow-blue { text-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
        .cursor-pointer { cursor: pointer; }
    </style>
</head>
<body>

    <!-- Mobile Navigation Bar -->
    <div class="mobile-top-nav">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('vms/logo/prime-bank-logo-squre.jpg') }}" style="height: 40px; width: 40px; border-radius: 8px;" alt="Prime Bank Logo">
            <h6 class="mb-0 fw-800 text-white">PRIME BANK</h6>
        </div>
        <button class="btn text-white p-0" onclick="toggleSidebar()">
            <i class="fas fa-bars fs-4"></i>
        </button>
    </div>

    <!-- Sidebar Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div id="page-admin-dashboard" class="vms-page">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                <img src="{{ asset('vms/logo/primebank_logo.png') }}" style="height: 60px; width: auto; max-width: 100%; border-radius: 8px;" alt="Prime Bank Logo">
            </div>

            <nav>
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard</a>

                {{-- Insurance Modules (Commented Out) --}}
                {{--
                <a href="{{ route('insurance-packages.index') }}" class="sidebar-item {{ request()->routeIs('insurance-packages.*') ? 'active' : '' }}"><i class="fas fa-box"></i> Insurance Packages</a>
                <a href="{{ route('admin.policies.index') }}" class="sidebar-item {{ request()->routeIs('admin.policies.*') ? 'active' : '' }}"><i class="fas fa-file-contract"></i> Policies</a>
                <a href="{{ route('admin.claims.index') }}" class="sidebar-item {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}"><i class="fas fa-clipboard-list"></i> Claims</a>
                --}}

                {{-- Visitor Management (Commented Out) --}}
                {{--
                <a href="{{ route('admin.live.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.live.dashboard') ? 'active' : '' }}"><i class="fas fa-broadcast-tower"></i> Live Dashboard</a>
                <a href="{{ route('admin.visitor.registration.create') }}" class="sidebar-item {{ request()->routeIs('admin.visitor.registration.create') ? 'active' : '' }}"><i class="fas fa-plus"></i> Visitor Registration</a>
                <a href="{{ route('admin.visitor.list') }}" class="sidebar-item {{ request()->routeIs('admin.visitor.list') ? 'active' : '' }}"><i class="fas fa-users"></i> Visitor List</a>
                --}}

                {{-- User Profile --}}
                <a href="{{ route('admin.profile') }}" class="sidebar-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}"><i class="fas fa-user"></i> My Profile</a>

                {{-- RBAC Roles (Commented Out - Uncomment if needed) --}}
                {{--
                <div class="sidebar-dropdown">
                    <a href="#" class="sidebar-item d-flex align-items-center" onclick="toggleSubmenu(event)">
                        <i class="fas fa-user-shield"></i> RBAC Roles
                        <i class="fas fa-chevron-down ms-auto small opacity-50"></i>
                    </a>
                    <div class="sidebar-submenu" id="rbac-submenu">
                        <a href="{{ route('admin.role.create') }}" class="submenu-item"><i class="fas fa-plus-circle"></i> Add Role</a>
                        <a href="{{ route('admin.role.assign.create') }}" class="submenu-item"><i class="fas fa-user-tag"></i> Assign Role</a>
                    </div>
                </div>
                --}}

                {{-- Logout --}}
                <a href="{{ route('logout') }}" class="sidebar-item text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>

            {{-- Help Center (Commented Out) --}}
            {{--
            <div class="help-center-box">
                <div class="help-icon text-white">?</div>
                <h6 class="fw-bold mb-1 text-white">Help Center</h6>
                <p style="font-size: 10px;" class="text-white-50 mb-3">24/7 Security Support</p>
                <button class="btn btn-light btn-sm w-100 rounded-pill fw-800 text-primary" style="font-size: 10px;">SUPPORT</button>
            </div>
            --}}
        </div>

        <!-- Main Content -->
        <div class="main-container">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleSubmenu(e) {
            e.preventDefault();
            const submenu = document.getElementById('rbac-submenu');
            submenu.classList.toggle('active');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>

    @stack('scripts')
</body>
</html>
