<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address - {{ $site_name }}</title>
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
        
        /* Code Container */
        .verification-box {
            margin: 30px 0;
            text-align: center;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .verification-label {
            display: block;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .verification-code {
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
        
        /* Expires Info */
        .expires-info {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }
        
        /* Button */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0F9D58;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 16px;
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
            
            .verification-box {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .verification-code {
                color: #f8fafc;
                background-color: #1e293b;
                border: 1px solid #475569;
            }
            
            .footer, .footer p {
                background-color: #0f172a;
                color: #94a3b8;
            }
            
            .expires-info {
                color: #94a3b8;
                border-top: 1px solid #334155;
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
            <h1>Verify Your Email Address</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $name }},</h2>
            
            <p>Thank you for registering with <strong>{{ $site_name }}</strong>. To complete your registration and secure your account, please verify your email address by entering the verification code below:</p>
            
            <div class="verification-box">
                <span class="verification-label">Your Verification Code</span>
                <div class="verification-code">{{ $code }}</div>
            </div>
            
            <div class="info-box">
                <p>This verification code will expire in <strong>{{ $expires_in }}</strong>. Please verify your email address as soon as possible.</p>
            </div>
            
            <p>Once verified, you'll have full access to your account and all features of our platform.</p>
            
            <p>If you did not create an account with us, please disregard this email or contact our support team if you have any concerns.</p>
            
            <div class="expires-info">
                <p>Code expires: {{ $expires_in }} from now</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $site_name }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html> 