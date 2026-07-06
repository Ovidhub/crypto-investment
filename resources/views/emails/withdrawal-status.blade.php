<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Status Update</title>
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
        
        /* Withdrawal Info */
        .withdrawal-box {
            margin: 30px 0;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .withdrawal-amount {
            font-size: 28px;
            font-weight: 700;
            color: #0F9D58;
            margin-bottom: 8px;
        }
        
        .withdrawal-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: #E8F5F0;
            color: #0B8046;
            margin-top: 8px;
        }
        
        .withdrawal-status.processed {
            background-color: #dcfce7;
            color: #15803d;
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
            
            .withdrawal-box {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .withdrawal-amount {
                color: #E8F5F0;
            }
            
            .withdrawal-status {
                background-color: #0B8046;
                color: #E8F5F0;
            }
            
            .withdrawal-status.processed {
                background-color: #065f46;
                color: #d1fae5;
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
                    <path d="M12 7v2M12 15v2M7.05 11.2a4 4 0 0 1 3.87-3.2h2.16a4 4 0 0 1 0 8h-2.16a4 4 0 0 1-3.87-3.2" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <h1>Withdrawal Status Update</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{$foramin ? 'Admin' : $user->name}},</h2>
            
            @if ($foramin)
            <p>This is to inform you that {{$user->name}} has made a withdrawal request of {{$settings->currency.$withdrawal->amount}}. Kindly login to your account to review and take necessary action.</p>
            
            <div class="button-container">
                <a href="" class="button" style="color: #ffffff !important;">Review Withdrawal</a>
            </div>
            
            @else
            
            <div class="withdrawal-box">
                <div class="withdrawal-amount">{{$settings->currency.$withdrawal->amount}}</div>
                
                @if ($deposit->status == 'Processed')
                <div class="withdrawal-status processed">Approved</div>
                <p style="margin-top: 20px;">This is to inform you that your withdrawal request has been approved and funds have been sent to your selected account.</p>
                @else
                <div class="withdrawal-status">Pending</div>
                <p style="margin-top: 20px;">This is to inform you that your withdrawal request is successful. Please wait while we process your request. You will receive a notification regarding the status of your request.</p>
                @endif
            </div>
            
            <div class="button-container">
                <a href="{{ route('withdrawalsdeposits') }}" class="button" style="color: #ffffff !important;">Go to Dashboard</a>
            </div>
            @endif
            
        </div>
        
        <div class="footer">
            <p>Thanks,<br>{{ config('app.name') }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
