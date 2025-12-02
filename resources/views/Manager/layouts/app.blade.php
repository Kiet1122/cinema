<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cinema Booking Admin Dashboard">
    <title>@yield('title', 'Cinema Booking - Manager Dashboard')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4a90e2;
            --primary-light: #6ba8e9;
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: #ffffff;
            color: #333333;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            border-right: 1px solid #e9ecef;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-header {
            padding: 1.5rem 0.5rem;
        }

        .sidebar-brand {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
        }

        .brand-text {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .brand-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-user {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-user {
            padding: 1.2rem 0.5rem;
        }

        .user-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            object-fit: cover;
            margin: 0 auto 0.75rem auto;
            display: block;
            transition: all 0.3s ease;
            background: white;
        }

        .sidebar.collapsed .user-avatar {
            width: 44px;
            height: 44px;
            margin: 0 auto 0.5rem auto;
        }

        .user-info {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .sidebar.collapsed .user-info {
            opacity: 0;
            height: 0;
            margin: 0;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
            white-space: nowrap;
            color: #333;
        }

        .user-role {
            font-size: 0.75rem;
            color: #6c757d;
            white-space: nowrap;
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - 240px);
            overflow-y: auto;
            transition: all 0.3s ease;
            background: white;
        }

        .sidebar.collapsed .sidebar-nav {
            padding: 1rem 0.25rem;
        }

        .nav-category {
            padding: 0.75rem 1rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            white-space: nowrap;
            background: #f8f9fa;
            margin: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .sidebar.collapsed .nav-category {
            padding: 0.75rem 0.5rem 0.5rem;
            text-align: center;
            font-size: 0.65rem;
            margin: 0.25rem 0.25rem;
        }

        .category-text {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .category-text {
            opacity: 0;
        }

        .nav-item {
            margin: 0.15rem 0.5rem;
        }

        .sidebar.collapsed .nav-item {
            margin: 0.15rem 0.25rem;
        }

        .nav-link {
            color: #555;
            padding: 0.75rem 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            font-weight: 500;
            position: relative;
            border: none;
        }

        .sidebar.collapsed .nav-link {
            padding: 0.75rem;
            justify-content: center;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background: #e9ecef;
            transform: none;
        }

        .nav-link.active {
            color: white;
            background: var(--primary-color);
            box-shadow: 0 2px 4px rgba(74, 144, 226, 0.3);
        }

        .nav-link.active:hover {
            background: var(--primary-light);
        }

        .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .nav-text {
            transition: opacity 0.3s ease;
            white-space: nowrap;
            flex: 1;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        .top-header {
            background: white;
            border-bottom: 1px solid #dee2e6;
            height: 70px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 1.5rem;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .toggle-sidebar:hover {
            background: #e9ecef;
            color: var(--primary-color);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-dropdown .dropdown-toggle {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #495057;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
            font-weight: 500;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: #e9ecef;
            color: var(--primary-color);
        }

        .user-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }

        /* Content */
        .content-wrapper {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
            padding: 0;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 400;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            background: white;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            border-radius: 8px 8px 0 0 !important;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 1.5rem;
            }
        }

        /* Scrollbar */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ url('/manager/dashboard') }}" class="sidebar-brand">
                <i class="fas fa-film" style="color: var(--primary-color);"></i>
                <span class="brand-text">Cinema</span>
            </a>
        </div>

        <div class="sidebar-user">
            <img src="{{ Auth::check() && Auth::user()->manager && Auth::user()->manager->Avatar 
                ? Auth::user()->manager->Avatar 
                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->manager->FullName ?? 'Manager') . '&background=4a90e2&color=fff' }}" 
                alt="Avatar" class="user-avatar">
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->manager->FullName ?? 'Manager' }}</div>
                <div class="user-role">Quản lý</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Tổng quan -->
            <div class="nav-category">
                <span class="category-text">TỔNG QUAN</span>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/dashboard') }}" class="nav-link {{ request()->is('manager/dashboard') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/reports') }}" class="nav-link {{ request()->is('manager/reports*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Báo cáo">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Báo cáo</span>
                </a>
            </div>

            <!-- Nội dung -->
            <div class="nav-category">
                <span class="category-text">NỘI DUNG</span>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/movies') }}" class="nav-link {{ request()->is('manager/movies*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Phim">
                    <i class="fas fa-film"></i>
                    <span class="nav-text">Phim</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/showtimes') }}" class="nav-link {{ request()->is('manager/showtimes*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Lịch chiếu">
                    <i class="far fa-calendar-alt"></i>
                    <span class="nav-text">Lịch chiếu</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/genre') }}" class="nav-link {{ request()->is('manager/genre*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Thể loại">
                    <i class="fas fa-tag"></i>
                    <span class="nav-text">Thể loại</span>
                </a>
            </div>

            <!-- Rạp chiếu -->
            <div class="nav-category">
                <span class="category-text">RẠP CHIẾU</span>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/theaters') }}" class="nav-link {{ request()->is('manager/theaters*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Rạp">
                    <i class="fas fa-building"></i>
                    <span class="nav-text">Rạp</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/rooms') }}" class="nav-link {{ request()->is('manager/rooms*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Phòng">
                    <i class="fas fa-door-open"></i>
                    <span class="nav-text">Phòng</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/seats') }}" class="nav-link {{ request()->is('manager/seats*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Ghế">
                    <i class="fas fa-chair"></i>
                    <span class="nav-text">Ghế</span>
                </a>
            </div>

            <!-- Khách hàng -->
            <div class="nav-category">
                <span class="category-text">KHÁCH HÀNG</span>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/bookings') }}" class="nav-link {{ request()->is('manager/bookings*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Đặt vé">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="nav-text">Đặt vé</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/vouchers') }}" class="nav-link {{ request()->is('manager/vouchers*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Voucher">
                    <i class="fas fa-gift"></i>
                    <span class="nav-text">Voucher</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/notification') }}" class="nav-link {{ request()->is('manager/notification*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Thông báo">
                    <i class="far fa-bell"></i>
                    <span class="nav-text">Thông báo</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/manager/member') }}" class="nav-link {{ request()->is('manager/member*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Khách hàng">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Khách hàng</span>
                </a>
            </div>

            <!-- Cá nhân -->
            <div class="nav-category">
                <span class="category-text">CÁ NHÂN</span>
            </div>
            <div class="nav-item">
                <a href="{{ route('manager.profile.index') }}" class="nav-link {{ request()->is('manager/profile*') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Hồ sơ">
                    <i class="fas fa-user"></i>
                    <span class="nav-text">Hồ sơ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Đăng xuất" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Đăng xuất</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="top-header">
            <div class="header-content">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="user-menu">
                    <div class="dropdown user-dropdown">
                        <button class="dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="{{ Auth::check() && Auth::user()->manager && Auth::user()->manager->Avatar 
                                ? Auth::user()->manager->Avatar 
                                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->manager->FullName ?? 'Manager') . '&background=4a90e2&color=fff' }}" 
                                alt="Avatar" class="user-avatar-sm">
                            <span class="d-none d-md-inline">{{ Auth::user()->manager->FullName ?? 'Manager' }}</span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('manager.profile.index') }}">
                                    <i class="fas fa-user me-2" style="color: var(--primary-color);"></i>
                                    <span>Hồ sơ</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-2"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    <span>Đăng xuất</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleSidebar = document.getElementById('toggleSidebar');

            // Toggle sidebar
            toggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            // Active link
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Responsive handling
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('collapsed');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();
    </script>

    @stack('scripts')
</body>
</html>