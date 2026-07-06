<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Plan Expired</title>
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
        
        /* Summary Box */
        .summary-box {
            margin: 30px 0;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .summary-item {
            display: flex;
            margin-bottom: 16px;
        }
        
        .summary-item:last-child {
            margin-bottom: 0;
        }
        
        .summary-label {
            font-weight: 600;
            color: #64748b;
            width: 100px;
            flex-shrink: 0;
        }
        
        .summary-value {
            color: #1e293b;
            font-weight: 600;
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
            
            .summary-box {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .summary-label {
                color: #94a3b8;
            }
            
            .summary-value {
                color: #f8fafc;
            }
            
            .footer, .footer p {
                background-color: #0f172a;
                color: #94a3b8;
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
                    <path d="M12 7v6l3 3" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Investment Plan Expired</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $demo->receiver_name }},</h2>
            
            <p>This is to notify you that your investment plan ({{ $demo->receiver_plan }} plan) has expired and your capital for this plan has been added to your account for withdrawal.</p>
            
            <div class="summary-box">
                <div class="summary-item">
                    <div class="summary-label">Plan:</div>
                    <div class="summary-value">{{ $demo->receiver_plan }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Amount:</div>
                    <div class="summary-value">{{ $demo->received_amount }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Date:</div>
                    <div class="summary-value">{{ $demo->date }}</div>
                </div>
            </div>
            
            <p>You can now withdraw your funds or reinvest them into a new plan. Thank you for your continued trust in our services.</p>
        </div>
        
        <div class="footer">
            <p>Kind regards,<br>{{ $demo->sender }}</p>
            <p>&copy; {{ date('Y') }} {{ $demo->sender }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
