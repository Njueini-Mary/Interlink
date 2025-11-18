<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ML Matching Test Report</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; background: #f4f8fb; color: #222; }
        h1 { color: #007bff; margin-bottom: 0.5em; }
        h2 { color: #444; border-bottom: 2px solid #e3e3e3; padding-bottom: 2px; margin-top: 1.5em; }
        .section { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #e3e3e3; padding: 18px 24px; margin-bottom: 24px; }
        .label { color: #888; font-weight: 500; }
        .value { color: #222; font-weight: 600; }
        .score-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .score-table th, .score-table td { padding: 10px 14px; text-align: center; }
        .score-table th { background: #e3f0fa; color: #007bff; font-size: 1.1em; }
        .score-table td { background: #f8fafc; font-size: 1.1em; }
        .badge { display: inline-block; background: #007bff; color: #fff; border-radius: 12px; padding: 2px 10px; margin: 2px 2px 2px 0; font-size: 0.95em; }
        .xai-message { background: #f6f8e8; color: #4b5e0e; border-left: 4px solid #b6d43a; padding: 10px 16px; border-radius: 6px; margin: 10px 0; }
        .status-pass { color: #28a745; font-weight: bold; font-size: 1.1em; }
    </style>
</head>
<body>
    <h1>ML Matching Test Report</h1>
    <div class="section" style="background:#e3f0fa;">
        <h2 style="margin-top:0;">Table of Contents</h2>
        <ol>
            @foreach($allResults as $idx => $result)
                <li><b>Student:</b> {{ $result['student']->user->name ?? $result['student']->name ?? 'N/A' }} &mdash; <b>Opportunity:</b> {{ $result['opportunity']->title }}</li>
            @endforeach
        </ol>
    </div>
    @foreach($allResults as $idx => $result)
    <div class="section">
        <h2 style="color:#007bff;">Report #{{ $idx+1 }}</h2>
        <div style="display:flex;gap:40px;flex-wrap:wrap;">
            <div style="flex:1;min-width:260px;">
                <h3 style="margin-bottom:0.2em;">Student</h3>
                <span class="label">Name:</span> <span class="value">{{ $result['student']->user->name ?? $result['student']->name ?? 'N/A' }}</span><br>
                <span class="label">Course:</span> <span class="value">{{ $result['student']->course }}</span><br>
                <span class="label">Academic Field:</span> <span class="value">{{ $result['student']->academic_field }}</span><br>
                <span class="label">GPA:</span> <span class="value">{{ $result['student']->gpa }}</span><br>
                <span class="label">Interests:</span> <span class="value">{{ $result['student']->interests }}</span><br>
                <span class="label">Activities:</span> <span class="value">{{ $result['student']->activities }}</span><br>
                <span class="label">Organizations:</span> <span class="value">{{ $result['student']->organizations }}</span><br>
                <span class="label">Skills:</span>
                @if(count($result['studentSkills']))
                    @foreach($result['studentSkills'] as $skill)
                        <span class="badge">{{ $skill }}</span>
                    @endforeach
                @else
                    <span class="value">None</span>
                @endif
            </div>
            <div style="flex:1;min-width:260px;">
                <h3 style="margin-bottom:0.2em;">Opportunity</h3>
                <span class="label">Title:</span> <span class="value">{{ $result['opportunity']->title }}</span><br>
                <span class="label">Industry:</span> <span class="value">{{ $result['opportunity']->industry }}</span><br>
                <span class="label">Min GPA:</span> <span class="value">{{ $result['opportunity']->min_gpa }}</span><br>
                <span class="label">Interests:</span> <span class="value">{{ $result['opportunity']->interests }}</span><br>
                <span class="label">Activities:</span> <span class="value">{{ $result['opportunity']->activities }}</span><br>
                <span class="label">Organizations:</span> <span class="value">{{ $result['opportunity']->organizations }}</span><br>
                <span class="label">Skills:</span>
                @if(count($result['opportunitySkills']))
                    @foreach($result['opportunitySkills'] as $skill)
                        <span class="badge">{{ $skill }}</span>
                    @endforeach
                @else
                    <span class="value">None</span>
                @endif
            </div>
        </div>
        <div style="margin-top:18px;">
            <h3 style="margin-bottom:0.2em;">ML Scores</h3>
            <table class="score-table">
                <tr><th>Random Forest</th><th>TFIDF</th><th>LogReg</th></tr>
                <tr>
                    <td style="color:#007bff;font-weight:bold;">{{ $result['mlScores']['rf'] }}</td>
                    <td style="color:#17a2b8;font-weight:bold;">{{ $result['mlScores']['tfidf'] }}</td>
                    <td style="color:#fd7e14;font-weight:bold;">{{ $result['mlScores']['logreg'] }}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top:18px;">
            <h3 style="margin-bottom:0.2em;">XAI & Feature Matches</h3>
            @php $features = ['skills','course','year','university','academic_field','gpa','interests']; @endphp
            <table class="score-table" style="margin-bottom:10px;">
                <tr>
                    <th>Feature</th>
                    <th>Matched</th>
                    <th>Missing</th>
                </tr>
                @foreach($features as $feature)
                <tr>
                    <td style="font-weight:bold;text-transform:capitalize;">{{ str_replace('_',' ',$feature) }}</td>
                    <td>
                        @if(isset($result['featureMatches'][$feature]['matched']) && count($result['featureMatches'][$feature]['matched']))
                            @foreach($result['featureMatches'][$feature]['matched'] as $val)
                                <span class="badge" style="background:#28a745;">{{ $val }}</span>
                            @endforeach
                        @else
                            <span class="value">None</span>
                        @endif
                    </td>
                    <td>
                        @if(isset($result['featureMatches'][$feature]['missing']) && count($result['featureMatches'][$feature]['missing']))
                            @foreach($result['featureMatches'][$feature]['missing'] as $val)
                                <span class="badge" style="background:#dc3545;">{{ $val }}</span>
                            @endforeach
                        @else
                            <span class="value">None</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            <div class="xai-message">{{ $result['matchMessage'] }}</div>
        </div>
        <div style="margin-top:18px;">
            <h3 style="margin-bottom:0.2em;">Test Status</h3>
            <span class="status-pass">All assertions passed.</span>
        </div>
    </div>
    @endforeach
</body>
</html>
