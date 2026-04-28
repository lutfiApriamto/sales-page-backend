<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reset Password — SalesGen.ai</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:40px 16px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">

                <!-- HEADER / BRAND -->
                <tr>
                    <td align="center" style="padding-bottom:32px;">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:#4f46e5;border-radius:10px;width:36px;height:36px;text-align:center;vertical-align:middle;">
                                    <span style="color:white;font-size:18px;font-weight:700;line-height:36px;">✦</span>
                                </td>
                                <td style="padding-left:10px;">
                                    <span style="font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-0.5px;">
                                        SalesGen<span style="color:#4f46e5;">.ai</span>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- CARD -->
                <tr>
                    <td style="background:#ffffff;border-radius:20px;border:1px solid #e2e8f0;padding:48px 40px;">

                        <!-- Icon -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding-bottom:24px;">
                                    <div style="width:64px;height:64px;background:#eef2ff;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:28px;line-height:64px;text-align:center;">
                                        🔑
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- Greeting -->
                        <p style="margin:0 0 8px;font-size:22px;font-weight:800;color:#0f172a;text-align:center;letter-spacing:-0.5px;">
                            Reset Password Anda
                        </p>
                        <p style="margin:0 0 28px;font-size:14px;color:#64748b;text-align:center;line-height:1.6;">
                            Halo, <strong style="color:#0f172a;">{{ $name }}</strong>! Kami menerima permintaan reset password untuk akun Anda.
                        </p>

                        <!-- Divider -->
                        <div style="height:1px;background:#f1f5f9;margin-bottom:28px;"></div>

                        <!-- Body text -->
                        <p style="margin:0 0 24px;font-size:14px;color:#475569;line-height:1.7;text-align:center;">
                            Klik tombol di bawah untuk membuat password baru. Link ini hanya berlaku selama <strong style="color:#0f172a;">60 menit</strong> dan hanya dapat digunakan satu kali.
                        </p>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $url }}"
                                       style="display:inline-block;background:#4f46e5;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 36px;border-radius:12px;letter-spacing:0.2px;">
                                        Reset Password Sekarang →
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Divider -->
                        <div style="height:1px;background:#f1f5f9;margin-bottom:24px;"></div>

                        <!-- Warning -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background:#fefce8;border:1px solid #fef08a;border-radius:10px;padding:14px 16px;">
                                    <p style="margin:0;font-size:12px;color:#854d0e;line-height:1.6;">
                                        ⚠️ Jika Anda tidak merasa meminta reset password, abaikan email ini. Akun Anda tetap aman.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Fallback URL -->
                        <p style="margin:0;font-size:11px;color:#94a3b8;text-align:center;line-height:1.7;">
                            Tombol tidak berfungsi? Copy dan paste URL berikut ke browser Anda:<br/>
                            <a href="{{ $url }}" style="color:#4f46e5;word-break:break-all;font-size:11px;">{{ $url }}</a>
                        </p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td align="center" style="padding-top:24px;">
                        <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.7;">
                            © 2026 SalesGen AI Lab. Built for professionals.<br/>
                            Email ini dikirim secara otomatis, mohon tidak membalas.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>