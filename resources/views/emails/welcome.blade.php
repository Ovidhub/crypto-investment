<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $settings->site_name ?? config('app.name') }}</title>
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
            background-color: #0F9D58;
            color: white;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        /* Logo */
        .logo {
            display: block;
            margin: 0 auto 16px;
            width: 60px;
            height: 60px;
        }
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        /* Welcome Box */
        .welcome-box {
            margin: 30px 0;
            text-align: center;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .welcome-label {
            display: block;
            font-size: 18px;
            letter-spacing: 0.5px;
            color: #0F9D58;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .welcome-message {
            font-size: 16px;
            color: #1e293b;
            background-color: white;
            padding: 16px 24px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        /* Text Styles */
        h2 {
            color: #1e293b;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 16px;
        }
        
        p {
            margin: 0 0 16px;
            color: #334155;
        }
        
        strong {
            color: #1e293b;
            font-weight: 600;
        }
        
        /* Info Box */
        .info-box {
            margin: 24px 0;
            padding: 16px;
            background-color: #E8F5F0;
            border-left: 4px solid #0F9D58;
            border-radius: 4px;
        }
        
        .info-box p {
            margin: 0;
            color: #0B8046;
            font-size: 14px;
        }
        
        /* Button */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0F9D58;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 16px;
            text-align: center;
        }
        
        /* Button Container */
        .button-container {
            margin: 30px 0;
            text-align: center;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
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
        
        /* Dark Mode Support For Compatible Email Clients */
        @media (prefers-color-scheme: dark) {
            .container {
                background-color: #1e293b;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }
            
            body {
                background-color: #0f172a;
                color: #f1f5f9;
            }
            
            h2, strong {
                color: #f8fafc;
            }
            
            p {
                color: #e2e8f0;
            }
            
            .welcome-box {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .welcome-message {
                color: #f8fafc;
                background-color: #1e293b;
                border: 1px solid #475569;
            }
            
            .footer, .footer p {
                background-color: #0f172a;
                color: #94a3b8;
            }
            
            .info-box {
                background-color: #0B8046;
                border-left: 4px solid #0F9D58;
            }
            
            .info-box p {
                color: #E8F5F0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="margin-bottom: 20px;">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto; display: block;">
                    <circle cx="12" cy="12" r="10" stroke="white" stroke-width="1.5" fill="rgba(255,255,255,0.15)"/>
                    <path d="M9 12.75L11 14.75L15 9.75" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Welcome to {{ $settings->site_name ?? config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name }},</h2>
            
            <p>Thank you for joining <strong>{{ $settings->site_name ?? config('app.name') }}</strong>. We are excited to welcome you to our community. This is just the beginning of greater things to come.</p>
            
            <div class="welcome-box">
                <span class="welcome-label">Your Account is Ready</span>
                <div class="welcome-message">
                    Make a Deposit, Buy an Investment Plan and sit back to enjoy while we make your money work for you.
                </div>
            </div>
            
            <div class="info-box">
                <p>Your account has been successfully created and is ready to use. You can now log in and start exploring our platform.</p>
            </div>
            
            <p>We look forward to seeing you achieve your financial goals. Your experience is going to be nice and smooth. 🙂 No frustrations, no trouble.</p>
            
            <div class="button-container">
                <p style="font-weight: 600; margin-bottom: 15px;">Ready to get started?</p>
                <a href="{{ route('dashboard') }}" class="button" style="color: #ffffff !important;">Go to Your Dashboard</a>
            </div>
            
            <p>Thanks, and welcome.<br>
            The {{ $settings->site_name ?? config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->site_name ?? config('app.name') }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
