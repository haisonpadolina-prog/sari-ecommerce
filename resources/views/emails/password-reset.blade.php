<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SARI Password Reset Code</title>
</head>
<body style="margin:0;background:#f6f3ed;font-family:Arial,Helvetica,sans-serif;color:#241f18;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f3ed;padding:32px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #e6ded2;border-radius:18px;overflow:hidden;">
                <tr>
                    <td style="padding:28px 30px 18px;text-align:center;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;color:#a96f06;text-transform:uppercase;">SARI Account Recovery</div>
                        <h1 style="margin:10px 0 8px;font-size:26px;line-height:1.2;color:#1f1b16;">Your verification code</h1>
                        <p style="margin:0;color:#756d63;font-size:14px;line-height:1.7;">Use this code to continue resetting your SARI password.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 30px 24px;text-align:center;">
                        <div style="display:inline-block;padding:16px 24px;border:1px solid #ead7ad;border-radius:14px;background:#fff8e9;font-size:32px;font-weight:800;letter-spacing:10px;color:#9b6505;">{{ $otp }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 30px 28px;text-align:center;color:#81786c;font-size:13px;line-height:1.7;">
                        This code expires in <strong style="color:#4a4034;">{{ $expiresInMinutes }} minutes</strong>. If you did not request a password reset, you can ignore this email.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
