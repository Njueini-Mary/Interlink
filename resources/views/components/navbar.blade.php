<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Interlink Placement System - Connect Students & Organizations</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary: #4f46e5;
        --primary-dark: #4338ca;
        --secondary: #0ea5e9;
        --dark: #1f2937;
        --light: #f9fafb;
        --gray: #6b7280;
        --light-gray: #e5e7eb;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--dark);
        background-color: var(--light);
        line-height: 1.6;
    }

    .outside-nav {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: relative;
        z-index: 10;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .nav-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 80px;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
    }

    .nav-menu {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .nav-link {
        color: var(--dark);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        padding: 0.5rem 0;
    }

    .nav-link:hover {
        color: var(--primary);
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--primary);
        transition: width 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
        border: 1px solid var(--primary);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .btn-outline {
        background-color: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background-color: var(--primary);
        color: white;
    }

    .mobile-menu-button {
        display: none;
        background: none;
        border: none;
        color: var(--dark);
        font-size: 1.5rem;
        cursor: pointer;
    }

    .dropdown-menu {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        z-index: 20;
        transform: translateY(-100%);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .dropdown-menu.open {
        transform: translateY(0);
    }

    .dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--light-gray);
    }

    .close-menu {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--dark);
        cursor: pointer;
    }

    .dropdown-links {
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
    }

    .dropdown-link {
        padding: 1rem 0;
        color: var(--dark);
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px solid var(--light-gray);
    }

    .dropdown-link:last-child {
        border-bottom: none;
    }

    .dropdown-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 0 1.5rem 1.5rem;
    }

    .hero {
        padding: 4rem 0;
        background-color: white;
    }

    .hero-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--gray);
        margin-bottom: 2rem;
        max-width: 600px;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .hero-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }

        .mobile-menu-button {
            display: block;
        }

        .hero-title {
            font-size: 2.25rem;
        }

        .hero-buttons {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>
    <!-- Outside Navigation Bar -->
    <nav class="outside-nav" x-data="{ mobileMenuOpen: false }">
        <div class="container">
            <div class="nav-content">
                <a href="/" class="logo">
                    <i class="fas fa-briefcase"></i>
                    Interlink
                </a>

                <div class="nav-menu">
                    @guest
                    <a href="/about" class="nav-link">How It Works</a>
                    @endguest
                    {{-- <a href="/students" class="nav-link">Browse Students</a> --}}

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <!-- <a href="/admin/users" class="nav-link">Manage Users</a> -->
                            <a href="/admin/organizations" class="nav-link">Organizations</a>
                            <a href="/admin/students" class="nav-link">Students</a>
                            <a href="/admin/opportunities" class="nav-link">Opportunities</a>
                            <a href="/admin/applications" class="nav-link">Applications</a>
                            <a href="/admin/notifications" class="nav-link">Notifications</a>
                            <a href="/admin/activities" class="nav-link">Activities</a>
                        @elseif(auth()->user()->role === 'organization')
                            <a href="/organization/dashboard" class="nav-link">Org Dashboard</a>
                            <a href="/opportunities" class="nav-link">My Opportunities</a>
                            <a href="/opportunities/create" class="nav-link">Post Opportunity</a>
                            <a href="/applications" class="nav-link">Applications</a>
                            <a href="/students" class="nav-link">Find Students</a>
                        @elseif(auth()->user()->role === 'student')
                            <a href="/student/dashboard" class="nav-link">Student Dashboard</a>
                            <a href="/opportunities" class="nav-link">Browse Opportunities</a>
                            <a href="/applications" class="nav-link">My Applications</a>
                            <a href="/organizations" class="nav-link">Organizations</a>
                        @endif
                        <a href="/dashboard" class="btn btn-primary">Back to Dashboard</a>
                    @else
                        <a href="/login" class="btn btn-outline">Log In</a>
                        <a href="/register" class="btn btn-primary">Sign Up</a>
                    @endauth
                </div>

                <button class="mobile-menu-button" @click="mobileMenuOpen = true">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Dropdown -->
    <div class="dropdown-menu" :class="{ 'open': mobileMenuOpen }" x-show="mobileMenuOpen">
        <div class="dropdown-header">
            <a href="#" class="logo">
                <i class="fas fa-briefcase"></i>
                Interlink
            </a>
            <button class="close-menu" @click="mobileMenuOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="dropdown-links">
            <a href="#" class="dropdown-link">How It Works</a>
            <a href="/students" class="dropdown-link">Browse Students</a>
            <a href="/organizations" class="dropdown-link">For Organizations</a>
            <a href="/students" class="dropdown-link">For Students</a>
        </div>

        <div class="dropdown-buttons">
            <a href="#" class="btn btn-outline" style="width: 100%; text-align: center;">Log In</a>
            <a href="#" class="btn btn-primary" style="width: 100%; text-align: center;">Sign Up</a>
        </div>
    </div>


</body>

</html>