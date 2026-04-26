<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #D5DEEF; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 24px; text-align: center; }
        .logo { font-size: 32px; font-weight: 900; color: #628ECB; letter-spacing: -1px; margin-bottom: 20px; }
        h2 { color: #1e293b; margin-bottom: 10px; }
        p { color: #64748b; line-height: 1.6; }
        .button { 
            display: inline-block; 
            padding: 14px 30px; 
            background: #628ECB; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 12px; 
            font-weight: 700; 
            margin: 25px 0;
            box-shadow: 0 4px 6px -1px rgba(98, 142, 203, 0.3);
        }
        .footer { font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px; }
    </style>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <div class="container">
                    <div class="logo">Centrivo</div>
                    
                    <h2>Halo {{ $nama }}!</h2>
                    <p>Terima kasih sudah bergabung dengan <b>Centrivo</b>. Langkah terakhir untuk memulai petualanganmu adalah dengan mengaktifkan akunmu.</p>
                    
                    <a href="{{ $activationUrl }}" class="button">Aktivasi Akun Sekarang</a>
                    
                    <p>Jika tombol di atas tidak berfungsi, kamu bisa menyalin tautan berikut ke browser:<br>
                       <small style="word-break: break-all; color: #628ECB;">{{ $activationUrl }}</small>
                    </p>

                    <p style="margin-top: 20px;">Jika kamu tidak merasa mendaftar di Centrivo, silakan abaikan email ini.</p>
                    
                    <div class="footer">
                        &copy; {{ date('Y') }} Centrivo. All rights reserved.
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>