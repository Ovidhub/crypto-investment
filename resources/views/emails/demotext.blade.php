<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      background-color: #f5f8fa;
      margin: 0;
      padding: 0;
      color: #0a0b0d;
      -webkit-font-smoothing: antialiased;
    }
    * {
      box-sizing: border-box;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(15, 157, 88, 0.1);
    }
    .header {
      background-color: #0F9D58;
      color: #ffffff;
      text-align: center;
      padding: 40px 20px 20px;
    }
    .header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: 700;
    }
    .content {
      padding: 30px;
    }
    h2 {
      font-size: 22px;
      margin-top: 0;
      color: #0a0b0d;
    }
    p {
      margin: 0 0 16px;
      color: #4a4a4a;
      font-size: 14px;
      line-height: 1.5;
    }
    strong {
      font-weight: 600;
      color: #0a0b0d;
    }
    .footer {
      background: #f5f8fa;
      text-align: center;
      padding: 20px;
      font-size: 13px;
      color: #8c8c8c;
      border-top: 1px solid #e6ecf0;
    }
    .footer p {
      margin: 5px 0;
    }

    @media (prefers-color-scheme: dark) {
      body {
        background-color: #0a0b0d;
        color: #f5f5f5;
      }
      .container {
        background: #1c1d1f;
        box-shadow: 0 2px 8px rgba(15, 157, 88, 0.2);
      }
      .header {
        background-color: #0F9D58;
      }
      h2, strong {
        color: #ffffff;
      }
      p {
        color: #c0c0c0;
      }
      .footer {
        background: #0a0b0d;
        color: #757575;
        border-top: 1px solid #2d2d2d;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Welcome to {{ $demo->sender }}!</h1>
    </div>

    <div class="content">
      <h2>We're excited to have you here!</h2>

      <p>Your registration was successful and we are thrilled to welcome you to the <strong>{{ $demo->sender }}</strong> community.</p>

      <p style="font-size:12px;">Your system generated password: <strong>{{ $demo->password }}</strong></p>

      <p style="font-size:12px;">Please make sure to update your password to one of your preference for security purposes.</p>

      <p>If you need any assistance, feel free to reach out to us at <br><strong>{{ $demo->contact_email }}</strong></p>

      <p>Kind regards,<br><strong>{{ $demo->sender }}</strong></p>
    </div>

    <div class="footer">
      <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
      <p>This is an automated email. Please do not reply.</p>
    </div>
  </div>
</body>
</html>
