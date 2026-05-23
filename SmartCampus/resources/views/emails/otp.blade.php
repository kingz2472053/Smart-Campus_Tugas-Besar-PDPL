<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #4F46E5;
        }
        .content {
            font-size: 16px;
            color: #333333;
            line-height: 1.6;
        }
        .otp-box {
            background-color: #F8FAFC;
            border: 1px dashed #CBD5E1;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #1E293B;
            letter-spacing: 5px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>SmartCampus</h2>
        </div>
        
        <div class="content">
            <p>Halo, <strong>{{ $user->name }}</strong>,</p>
            <p>Sistem kami mendeteksi upaya login ke akun Anda. Untuk melanjutkan, silakan masukkan kode verifikasi (OTP) berikut:</p>
            
            <div class="otp-box">
                {{ $otpCode }}
            </div>
            
            <p>Kode ini hanya berlaku selama <strong>5 menit</strong>. Jangan pernah membagikan kode ini kepada siapa pun, termasuk admin kampus.</p>
            
            <p>Jika Anda tidak merasa melakukan login, Anda dapat mengabaikan email ini.</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} SmartCampus. Mata Kuliah PDPL.<br>
            Pesan otomatis ini dihasilkan oleh Sistem SmartCampus.
        </div>
    </div>
</body>
</html>
