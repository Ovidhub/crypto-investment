<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Authentication Code</title>
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
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        /* Code Box */
        .code-box {
            margin: 30px 0;
            text-align: center;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .code-label {
            display: block;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .auth-code {
            display: inline-block;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 6px;
            color: #1e293b;
            background-color: white;
            padding: 16px 32px;
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
            
            .code-box {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .auth-code {
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
                    <path d="M12 8V13" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 16V16.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>2FA Authentication Code</h1>
        </div>
        
        <div class="content">
            <h2>Security Verification</h2>
            
            <p>A temporary 2FA code request has been made using your account. Please authenticate using the following code:</p>
            
            <div class="code-box">
                <span class="code-label">Your Authentication Code</span>
                <div class="auth-code">{!! $demo->message !!}</div>
            </div>
            
            <div class="info-box">
                <p>This code will expire shortly. Do not share this code with anyone, including our staff. Our team will never ask for your authentication code.</p>
            </div>
            
            <p>If you didn't request this code, please secure your account immediately by changing your password.</p>
        </div>
        
        <div class="footer">
            <p>Thanks,<br>{{ $demo->sender }}</p>
            <p>&copy; {{ date('Y') }} {{ $demo->sender }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
