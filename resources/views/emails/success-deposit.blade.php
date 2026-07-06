<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Notification</title>
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
        
        /* Deposit Info */
        .deposit-box {
            margin: 30px 0;
            padding: 24px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .deposit-amount {
            font-size: 28px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 8px;
        }
        
        .deposit-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: #dcfce7;
            color: #15803d;
            margin-top: 8px;
        }
        
        .deposit-status.pending {
            background-color: #fef9c3;
            color: #854d0e;
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
            
            .deposit-box {
                background-color: #334155;
                border: 1px solid #475569;
            }
            
            .deposit-amount {
                color: #34d399;
            }
            
            .deposit-status {
                background-color: #065f46;
                color: #d1fae5;
            }
            
            .deposit-status.pending {
                background-color: #713f12;
                color: #fef9c3;
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
                    <path d="M8 12L11 15L16 9" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Deposit Notification</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{$foramin ? 'Admin' : $user->name}},</h2>
            
            @if ($foramin)
            <p>This is to inform you of a successful deposit of {{$settings->currency.$deposit->amount}} from {{$user->name}}. {{ $deposit->status == "Processed" ? '' : ' Please login to process it.' }}</p>
            
            @if ($deposit->status != "Processed")
            <div class="button-container">
                <a href="" class="button" style="color: #ffffff !important;">Process Deposit</a>
            </div>
            @endif
            
            @else
            
            <div class="deposit-box">
                <div class="deposit-amount">{{$settings->currency.$deposit->amount}}</div>
                
                @if ($deposit->status == 'Processed')
                <div class="deposit-status">Confirmed</div>
                <p style="margin-top: 20px;">This is to inform you that your deposit has been received and confirmed. Your account balance has been credited with the specified amount.</p>
                @else
                <div class="deposit-status pending">Pending Confirmation</div>
                <p style="margin-top: 20px;">This is to inform you that your deposit is successful. Please wait while we confirm your deposit. You will receive a notification regarding the status of this transaction.</p>
                @endif
            </div>
            
            <div class="button-container">
                <a href="{{ route('deposits') }}" class="button" style="color: #ffffff !important;">Go to Dashboard</a>
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
