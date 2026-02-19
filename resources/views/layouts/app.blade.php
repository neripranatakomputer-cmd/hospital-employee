<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMKA') - RSUD Kamang Baru</title>
    

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #0d6efd;
            --sidebar-bg: #1a2035;
            --sidebar-hover: #252d4a;
            --header-height: 60px;
        }

        body { background: #f0f2f5; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        #sidebar .sidebar-brand {
            padding: 20px 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        #sidebar .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 16px;
        }

        #sidebar .sidebar-brand small { color: rgba(255,255,255,0.5); font-size: 11px; }

        #sidebar .nav-item { margin: 2px 10px; }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: var(--sidebar-hover);
            color: #fff;
        }

        #sidebar .nav-link i { font-size: 18px; width: 22px; }

        #sidebar .sidebar-section-title {
            color: rgba(255,255,255,0.3);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 14px 24px 6px;
        }

        /* Main Content */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin 0.3s ease;
        }

        /* Topbar */
        #topbar {
            background: #fff;
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Cards */
        .stat-card {
            border-radius: 12px;
            border: none;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }

        /* Page content */
        .page-content { padding: 24px; }

        /* Badges */
        .badge-sip-aktif { background: #d1fae5; color: #065f46; }
        .badge-sip-hampir { background: #fef3c7; color: #92400e; }
        .badge-sip-expired { background: #fee2e2; color: #991b1b; }

        /* Table */
        .table-responsive { border-radius: 10px; overflow: hidden; }

        /* Alert float */
        .alert-floating {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }

        /* Profile photo preview */
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
    <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo RSUD Kamang Baru"
            style="width:48px;height:48px;object-fit:contain;filter:brightness(0) invert(1);">
        <div>
            <h5>KAMANG BARU</h5>
            <small>Rumah Sakit Umum Daerah</small>
        </div>
    </div>
    </div>

    <ul class="nav flex-column pt-2">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <li class="sidebar-section-title">Karyawan</li>

        <li class="nav-item">
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') && !request()->routeIs('employees.sip-alerts') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Karyawan
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('employees.sip-alerts') }}" class="nav-link {{ request()->routeIs('employees.sip-alerts') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notifikasi SIP
                @php $sipCount = \App\Models\Employee::expiringSip(30)->count() + \App\Models\Employee::expiredSip()->count() @endphp
                @if($sipCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $sipCount }}</span>
                @endif
            </a>
        </li>

        <li class="sidebar-section-title">Absensi</li>

        <li class="nav-item">
            <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.create') || request()->routeIs('attendance.edit') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Data Absensi
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('attendance.report') }}" class="nav-link {{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan Absensi
            </a>
        </li>

        @if(auth()->user()->isSuperAdmin())
        <li class="sidebar-section-title">Admin</li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Manajemen User
            </a>
        </li>
        @endif
    </ul>
</nav>

<!-- Main Content -->
<div id="main-content">
    <!-- Topbar -->
    <div id="topbar">
        <button class="btn btn-sm btn-outline-secondary me-3 d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <span class="text-muted">@yield('page-title', 'Dashboard')</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ auth()->user()->name }}
                    <span class="badge bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : 'primary' }} ms-1">
                        {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        @yield('content')
    </div>
</div>

<!-- Alert floating -->
@if(session('success'))
<div class="alert alert-success alert-dismissible alert-floating fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible alert-floating fade show" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible alert-floating fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-floating').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el).close();
        });
    }, 5000);

    // Sidebar toggle (mobile)
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Select2 init
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });
    });
</script>

@stack('scripts')
</body>
</html>
