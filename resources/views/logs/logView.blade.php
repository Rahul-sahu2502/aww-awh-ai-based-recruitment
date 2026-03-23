<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Laravel Log Viewer</title>
        <style>
            body {
                background-color: #1e1e1e;
                color: #00ff00;
                font-family: monospace;
                padding: 20px;
            }

            .container {
                max-width: 100%;
                overflow-x: auto;
                white-space: pre-wrap;
                background-color: #111;
                border: 1px solid #444;
                padding: 20px;
                margin-top: 20px;
            }

            .btn-clear {
                background-color: #ff4444;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .status {
                background-color: #333;
                color: #0f0;
                padding: 10px;
                margin-bottom: 10px;
                border-left: 5px solid #0f0;
            }

            .error-line {
                color: #ff4d4d;
                font-weight: bold;
            }
        </style>
    </head>

    <body>

        <h1>Laravel Log Viewer</h1>

        @if (session('status'))
            <div class="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('log.clear') }}">
            @csrf
            <button type="submit" class="btn-clear" onclick="return confirm('Are you sure you want to clear the log?')">
                Clear Log
            </button>
        </form>

        <div class="container">
            @if ($logMissing)
                <p style="color: orange;">⚠️ Log file not found.</p>
            @else
                {!! highlightLog($logContent) !!}
            @endif
        </div>

        {{-- Blade में ही function define --}}
        @php
            function highlightLog($logContent)
            {
                $logContent = e($logContent);
                $logContent = preg_replace(
                    '/(\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/m',
                    '<span class="error-line">$1</span>',
                    $logContent,
                );
                return nl2br($logContent);
            }
        @endphp

    </body>

</html>
