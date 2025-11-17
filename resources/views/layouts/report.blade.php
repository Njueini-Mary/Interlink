<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? (isset($orgName) ? $orgName . ' Report' : 'Report') }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #222; margin: 0; padding: 0; }
        .report-container { max-width: 1100px; margin: 32px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 32px; }
        .report-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .report-title { font-size: 2rem; font-weight: 700; color: #4f46e5; }
        .report-meta { font-size: 1rem; color: #64748b; }
        .report-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .report-table th, .report-table td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; }
        .report-table th { background: #f1f5f9; color: #334155; font-weight: 600; }
        .report-table tr:nth-child(even) { background: #f9fafb; }
        .report-table tr:hover { background: #eef2ff; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 0.85em; font-weight: 500; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .report-footer { margin-top: 32px; text-align: right; color: #64748b; font-size: 0.95rem; }
    </style>
    @stack('report-styles')
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <div class="report-title">{{ $title ?? 'Report' }}</div>
            <div class="report-meta">
                Generated: {{ now()->format('Y-m-d H:i') }}<br>
                @if(isset($meta)) {!! $meta !!} @endif
            </div>
        </div>
        <div class="report-content">
            @yield('report-content')
        </div>
        <div class="report-footer">
            Interlink Platform &copy; {{ date('Y') }}
        </div>
    </div>
    @stack('report-scripts')
</body>
</html>
