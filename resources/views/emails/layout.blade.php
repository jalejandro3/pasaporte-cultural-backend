<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Correo Electrónico')</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; padding: 0; margin: 0;">
    <tr>
        <td align="center" style="padding: 20px;">
            <img src="{{ config('app.frontend_url') }}/images/unir-logo.svg" alt="UNIR Logo" style="height: 50px; display: block;">
        </td>
    </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4; padding: 20px; margin: 0;">
    <tr>
        <td align="center">
            <table width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <tr>
                    <td>
                        @yield('content')
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #000000; padding: 20px; color: #ffffff; text-align: center;">
    <tr>
        <td>
            <p style="font-size: 12px; margin: 0;">
                Has recibido este correo como usuario registrado de <a href="https://unir.net" style="color: #ffffff; text-decoration: underline;">UNIR.NET</a>.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
