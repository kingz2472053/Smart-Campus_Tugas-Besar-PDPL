<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemberitahuan Baru SmartCampus</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f4f5f7; color: #333333;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin: 30px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;">
        <!-- Header -->
        <tr>
            <td bgcolor="#4F46E5" style="padding: 30px 20px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">SmartCampus</h1>
                <p style="color: #c7d2fe; margin: 5px 0 0 0; font-size: 14px;">Sistem Manajemen Tugas Terintegrasi</p>
            </td>
        </tr>
        
        <!-- Body -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; line-height: 24px; margin: 0 0 20px 0; color: #1e293b;">
                    Halo, <strong>{{ $user->name }}</strong>,
                </p>
                <p style="font-size: 16px; line-height: 24px; margin: 0 0 30px 0; color: #334155;">
                    Ada pemberitahuan baru untuk Anda terkait aktivitas di SmartCampus:
                </p>
                
                <!-- Notification Box -->
                <div style="background-color: #f8fafc; border-left: 4px solid #4F46E5; padding: 20px; border-radius: 4px; margin-bottom: 30px;">
                    <p style="font-size: 15px; line-height: 22px; margin: 0; color: #0f172a; font-weight: 500;">
                        {{ $notificationMessage }}
                    </p>
                </div>
                
                <p style="font-size: 15px; line-height: 22px; margin: 0 0 10px 0; color: #64748b;">
                    Silakan masuk ke dashboard SmartCampus untuk melihat detail informasi selengkapnya.
                </p>
                
                <div style="text-align: center; margin-top: 35px; margin-bottom: 20px;">
                    <a href="{{ url('/login') }}" style="background-color: #4F46E5; color: #ffffff; text-decoration: none; padding: 12px 30px; font-size: 15px; font-weight: 600; border-radius: 6px; display: inline-block; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
                        Buka SmartCampus
                    </a>
                </div>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td bgcolor="#f8fafc" style="padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 18px;">
                    Email ini dikirim secara otomatis oleh sistem SmartCampus.<br>
                    Mohon tidak membalas email ini secara langsung.
                </p>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #94a3b8; font-weight: 600;">
                    &copy; 2026 SmartCampus PDPL Team. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
