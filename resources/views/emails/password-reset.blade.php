@extends('emails.template')

@section('title', 'Recuperação de Senha')

@section('content')
<p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
    Olá, <strong>{{ $nome }}</strong>
</p>

<p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
    Você solicitou a recuperação de senha da sua conta no PesqHub.
</p>

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <p style="margin: 0 0 15px; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
        Seu código de verificação é:
    </p>
    <div style="background: rgba(255, 255, 255, 0.95); border-radius: 8px; padding: 20px; margin: 0 auto; max-width: 250px;">
        <p style="margin: 0; font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #667eea; font-family: 'Courier New', monospace;">
            {{ $token }}
        </p>
    </div>
</div>

<p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
    Digite este código na página de recuperação de senha para continuar o processo de redefinição da sua senha.
</p>

<div style="background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
        <strong>Importante:</strong> Este código é válido por 15 minutos e pode ser usado no máximo 3 vezes.
    </p>
</div>

<p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
    Se você não solicitou a recuperação de senha, ignore este email. Sua senha permanecerá inalterada.
</p>

<p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
    Atenciosamente,<br>
    <strong style="color: #667eea;">Equipe PesqHub</strong>
</p>
@endsection

@push('footer-content')
<p style="margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
    Por questões de segurança, nunca compartilhe este código com terceiros.
</p>
@endpush
