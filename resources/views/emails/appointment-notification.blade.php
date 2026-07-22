<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Clinique Tamarix</title>
</head>
<body style="margin:0;padding:0;background:#faf7f5;font-family:Arial,Helvetica,sans-serif;color:#3d4852;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf7f5;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#153e60;padding:20px 24px;color:#ffffff;">
                            <div style="font-size:18px;font-weight:bold;">Clinique Médico-Chirurgicale Tamarix</div>
                            <div style="font-size:12px;font-style:italic;color:#e0b8bd;">« Nous plantons l'Espérance »</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <div style="font-size:15px;line-height:1.65;white-space:pre-line;">{{ $log->content }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 24px;">
                            <div style="border-left:4px solid #a55a63;background:#f6ecee;padding:12px 16px;font-size:13px;">
                                Ce message vous est envoyé par e-mail car votre numéro WhatsApp n'a pas pu être joint.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f2f4f7;padding:16px 24px;font-size:12px;color:#77808a;">
                            {{ setting('clinic_address') }}<br>
                            Tél. {{ setting('clinic_phone') }} — Urgences 24h/24 : {{ setting('emergency_phone') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
