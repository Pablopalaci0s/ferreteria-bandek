<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:32px 16px;">
<tr>
<td align="center">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e5e7eb;">

<tr>
<td style="background:#991b1b; padding:20px 28px;">
    <span style="color:#ffffff; font-size:18px; font-weight:700;">Ferretería BANDEK</span>
</td>
</tr>

<tr>
<td style="padding:28px;">

    <p style="font-size:15px; color:#111827; margin:0 0 16px;">Hola {{ $usuario->name }},</p>

    <p style="font-size:14px; color:#374151; line-height:1.6; margin:0 0 20px;">
        Un administrador restableció la contraseña de tu cuenta del panel de Ferretería BANDEK.
        Esta es tu contraseña temporal:
    </p>

    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:14px 18px; text-align:center; margin-bottom:20px;">
        <span style="font-size:20px; font-weight:700; letter-spacing:0.05em; color:#111827; font-family: monospace;">{{ $contrasenaTemporal }}</span>
    </div>

    <p style="font-size:14px; color:#374151; line-height:1.6; margin:0 0 20px;">
        Por seguridad, al iniciar sesión con esta contraseña se te va a pedir que la cambiés
        por una propia antes de poder usar el panel.
    </p>

    <p style="font-size:13px; color:#9ca3af; line-height:1.6; margin:0;">
        Si no esperabas este correo, avisale a un administrador de Ferretería BANDEK.
    </p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
