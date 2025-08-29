<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Freelancing Hub</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        color: #333;
    }

    .navbar {
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .navbar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 70px;
    }

    .logo {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4f46e5;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .desktop-menu {
        display: flex;
        gap: 1.5rem;
    }

    .nav-link {
        color: #4b5563;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
        padding: 0.5rem 0;
        position: relative;
    }

    .nav-link:hover {
        color: #4f46e5;
    }

    .nav-link.active {
        color: #4f46e5;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #4f46e5;
        border-radius: 2px;
    }

    .mobile-menu-button {
        display: none;
        background: none;
        border: none;
        color: #4b5563;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 3rem;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 0;
        min-width: 12rem;
        display: none;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #4b5563;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f3f4f6;
    }

    .user-menu {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .notification-bell {
        position: relative;
        font-size: 1.25rem;
        color: #4b5563;
        cursor: pointer;
    }

    .notification-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background-color: #ef4444;
        color: white;
        font-size: 0.75rem;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e5e7eb;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #4f46e5;
        font-weight: 600;
        cursor: pointer;
    }

    .username {
        font-weight: 500;
        margin-right: 8px;
        color: #4b5563;
    }

    .mobile-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        z-index: 200;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        overflow-y: auto;
    }

    .mobile-sidebar.open {
        transform: translateX(0);
    }

    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .close-sidebar {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #4b5563;
        cursor: pointer;
    }

    .mobile-nav-links {
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
    }

    .mobile-nav-link {
        padding: 1rem 0;
        color: #4b5563;
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px solid #e5e7eb;
    }

    .mobile-nav-link:last-child {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .desktop-menu {
            display: none;
        }

        .mobile-menu-button {
            display: block;
        }

        .username {
            display: none;
        }
    }
    </style>
</head>

<body>
    <div x-data="{ mobileMenuOpen: false, userMenuOpen: false, notificationsOpen: false }">
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/" class="logo">
                    <i class="fas fa-briefcase"></i>
                    Interlink
                </a>

                <div class="desktop-menu">
                    @php $user = Auth::user(); $role = $user->role ?? null; @endphp
                    @if($role === 'admin')
                        <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                    @endif
                    @if($role === 'student')
                        <a href="/student/dashboard" class="nav-link {{ request()->is('student/dashboard') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> Student Dashboard</a>
                        <a href="/opportunities" class="nav-link {{ request()->is('opportunities') ? 'active' : '' }}"><i class="fas fa-briefcase"></i> Opportunities</a>
                        <a href="/applications" class="nav-link {{ request()->is('applications') ? 'active' : '' }}"><i class="fas fa-file-alt"></i> My Applications</a>
                        <a href="/community-service" class="nav-link {{ request()->is('community-service') ? 'active' : '' }}"><i class="fas fa-hands-helping"></i> Community Service</a>
                    @endif
                    @if($role === 'organization')
                        <a href="/organization/dashboard" class="nav-link {{ request()->is('organization/dashboard') ? 'active' : '' }}"><i class="fas fa-building"></i> Organization Dashboard</a>
                        <a href="/organization/opportunities" class="nav-link {{ request()->is('organization/opportunities') ? 'active' : '' }}"><i class="fas fa-plus-circle"></i> Post Opportunity</a>
                        <a href="/organization/applicants" class="nav-link {{ request()->is('organization/applicants') ? 'active' : '' }}"><i class="fas fa-users"></i> Applicants</a>
                    @endif
                    {{-- Profile and Logout links are now only in the dropdown --}}
                </div>

                <div class="user-menu">
                    <div class="dropdown" @click.away="userMenuOpen = false">
                        <div class="avatar" @click="userMenuOpen = !userMenuOpen">
                            {{ substr(Auth::user()->name, 0, 1) }}{{ substr(substr(Auth::user()->name, strpos(Auth::user()->name, ' ') + 1, 1), 0, 1) }}
                        </div>
                        <div class="dropdown-menu" :class="{ 'show': userMenuOpen }">
                            <a href="{{ route('profile.update.form') }}" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                My Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-chart-line"></i>
                                Analytics
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-question-circle"></i>
                                Help & Support
                            </a>
                            <hr style="margin: 0.5rem 0; border-color: #e5e7eb;">
                            <a href="{{ route('logout') }}" class="dropdown-item"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                Sign Out
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <button class="mobile-menu-button" @click="mobileMenuOpen = true">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" :class="{ 'open': mobileMenuOpen }" x-show="mobileMenuOpen"
        @click.away="mobileMenuOpen = false">
        <div class="sidebar-header">
            <a href="/" class="logo">
                <i class="fas fa-briefcase"></i>
                Interlink
            </a>
            <button class="close-sidebar" @click="mobileMenuOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mobile-nav-links">
            <div style="padding: 1rem 0; border-bottom: 1px solid #e5e7eb; margin-bottom: 1rem;">
                <div style="font-weight: 600; margin-bottom: 0.5rem;">{{ $user->name ?? 'User' }}</div>
                <div style="color: #6b7280; font-size: 0.875rem;">{{ $user->email ?? '' }}</div>
            </div>
            @if($role === 'admin')
                <a href="/admin/dashboard" class="mobile-nav-link">Admin Dashboard</a>
            @endif
            @if($role === 'student')
                <a href="/student/dashboard" class="mobile-nav-link">Student Dashboard</a>
                <a href="/opportunities" class="mobile-nav-link">Opportunities</a>
                <a href="/applications" class="mobile-nav-link">My Applications</a>
                <a href="/community-service" class="mobile-nav-link">Community Service</a>
            @endif
            @if($role === 'organization')
                <a href="/organization/dashboard" class="mobile-nav-link">Organization Dashboard</a>
                <a href="/organization/opportunities" class="mobile-nav-link">Post Opportunity</a>
                <a href="/organization/applicants" class="mobile-nav-link">Applicants</a>
            @endif
            <a href="{{ route('profile.update.form') }}" class="mobile-nav-link"><i class="fas fa-user"></i> My Profile</a>
            <a href="#" class="mobile-nav-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="#" class="mobile-nav-link"><i class="fas fa-chart-line"></i> Analytics</a>
            <a href="#" class="mobile-nav-link"><i class="fas fa-question-circle"></i> Help & Support</a>
            <a href="{{ route('logout') }}" class="mobile-nav-link"
                onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </a>
            <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    </div>

</body>

</html>