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
    <main class="dashboard-main">
        @hasSection('header')
            <div class="dashboard-header">@yield('header')</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
