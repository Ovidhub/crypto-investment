<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Container */
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        /* Header */
        .header {
            padding: 30px 20px;
            text-align: center;
            background-color: #4f46e5;
            color: white;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            color: #1e293b;
            line-height: 1.2;
        }

        h1 { font-size: 24px; margin-bottom: 24px; font-weight: 700; }
        h2 { font-size: 20px; margin-bottom: 16px; font-weight: 600; }
        h3 { font-size: 18px; margin-bottom: 16px; font-weight: 600; }
        
        p {
            margin: 0 0 16px;
            color: #334155;
        }
        
        strong {
            color: #1e293b;
            font-weight: 600;
        }
        
        /* Button */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 16px;
            text-align: center;
            transition: background-color 0.2s;
        }
        
        .button-primary {
            background-color: #4f46e5;
        }
        
        .button-secondary {
            background-color: #6B7280;
        }
        
        .button-success {
            background-color: #10B981;
        }
        
        /* Panel */
        .panel {
            margin: 30px 0;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        /* Info Box */
        .info-box {
            margin: 24px 0;
            padding: 16px;
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
        }
        
        .info-box p {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }
        
        /* Table */
        .table {
            width: 100%;
            margin: 30px 0;
            border-collapse: collapse;
        }
        
        .table th {
            padding: 12px;
            text-align: left;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #1e293b;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        
        .table tr:last-child td {
            border-bottom: none;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #64748b;
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
        
        .footer p {
            margin: 5px 0;
            color: #64748b;
        }
        
        /* Subcopy */
        .subcopy {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
            font-size: 14px;
            color: #64748b;
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .container {
                background-color: #1e293b;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }
            
            body {
                background-color: #0f172a;
                color: #f1f5f9;
            }
            
            h1, h2, h3, h4, h5, h6, strong {
                color: #f8fafc;
            }
            
            p {
                color: #e2e8f0;
            }
            
            .panel {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .footer, .footer p {
                background-color: #0f172a;
                color: #94a3b8;
            }
            
            .subcopy {
                color: #94a3b8;
                border-top: 1px solid #334155;
            }
            
            .info-box {
                background-color: #1e3a8a;
                border-left: 4px solid #3b82f6;
            }
            
            .info-box p {
                color: #93c5fd;
            }
            
            .table th {
                background-color: #1e293b;
                border-bottom: 1px solid #475569;
                color: #f8fafc;
            }
            
            .table td {
                border-bottom: 1px solid #475569;
                color: #e2e8f0;
            }
        }
        
        /* Mobile Responsiveness */
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                width: 100% !important;
                border-radius: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .button {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{ $header ?? '' }}
        
        <div class="content">
            {{ Illuminate\Mail\Markdown::parse($slot) }}
            {{ $subcopy ?? '' }}
        </div>
        
        {{ $footer ?? '' }}
    </div>
</body>
</html>
