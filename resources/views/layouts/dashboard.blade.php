<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link rel="stylesheet" href="/css/dashboard-custom.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
        }
        .dashboard-main {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 2rem;
        }
        .dashboard-header {
            margin-bottom: 2rem;
        }
    </style>
    @stack('head')
</head>
<body>
    @include('layouts.navigation')
    <!-- Toast Notification for all dashboards -->
    <!--
        Toast Notification Block
        -----------------------
        This section displays a temporary success message ("toast") at the top right of the dashboard.
        - It appears only if there is a 'status' value in the session (set after login or other actions).
        - The high z-index (9999) ensures it appears above all other elements, including the navbar.
        - The green background and check icon indicate success.
        - The close button (×) lets users dismiss the toast manually.
        You can customize the style, position, or message as needed for your app.
    -->
    @if(session('status'))
        <div class="fixed top-4 right-4" style="z-index:9999;">
            <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
                <button onclick="this.parentElement.style.display='none'" class="ml-4 text-white">&times;</button>
            </div>
        </div>
    @endif
    <main class="dashboard-main">
        @hasSection('header')
            <div class="dashboard-header">@yield('header')</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
