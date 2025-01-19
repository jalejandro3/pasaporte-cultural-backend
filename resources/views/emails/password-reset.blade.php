@extends('emails.layout')

@section('title', 'Recuperación de Contraseña')

@section('content')
    <h2 style="color: #333333; text-align: center;">Recuperación de Contraseña</h2>
    <p style="color: #666666; font-size: 16px; line-height: 1.5;">
        Hola,<br><br>
        Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:
    </p>
    <p style="text-align: center; margin: 20px 0;">
        <a href="{{ $resetLink }}" style="background-color: #007bb5; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;">
            Restablecer Contraseña
        </a>
    </p>
    <p style="color: #666666; font-size: 14px; line-height: 1.5;">
        Si no realizaste esta solicitud, puedes ignorar este correo.
    </p>
@endsection
