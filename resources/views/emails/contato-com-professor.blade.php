@extends('emails.template')

@section('title', 'Novo Contato de Estudante - PesqHub UEFS')

@section('content')
<div style="padding: 30px 0;">
    <h2 style="color: #2c5282; margin-bottom: 25px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
        📧 Novo Contato de Estudante
    </h2>
    
    <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #4299e1;">
        <p style="margin: 0; color: #2d3748; font-size: 16px;">
            Olá <strong>{{ $nome_professor }}</strong>,
        </p>
        <p style="margin: 10px 0 0 0; color: #4a5568; font-size: 14px;">
            Você recebeu uma nova mensagem através do PesqHub UEFS.
        </p>
    </div>
    
    <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin-bottom: 25px;">
        <h3 style="color: #2d3748; margin-top: 0; margin-bottom: 20px; font-size: 18px;">
            👤 Dados do Estudante
        </h3>
        
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">Nome:</strong>
            <span style="color: #2d3748; margin-left: 10px;">{{ $nome_estudante }}</span>
        </div>
        
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">E-mail:</strong>
            <span style="color: #2d3748; margin-left: 10px;">
                <a href="mailto:{{ $email_estudante }}" style="color: #4299e1; text-decoration: none;">
                    {{ $email_estudante }}
                </a>
            </span>
        </div>
        
        @if(isset($curso_estudante))
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">Curso:</strong>
            <span style="color: #2d3748; margin-left: 10px;">{{ $curso_estudante }}</span>
        </div>
        @endif
    </div>
    
    <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin-bottom: 25px;">
        <h3 style="color: #2d3748; margin-top: 0; margin-bottom: 20px; font-size: 18px;">
            💬 Mensagem
        </h3>
        
        <div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; border-left: 3px solid #4299e1;">
            <p style="margin: 0; color: #2d3748; line-height: 1.6; white-space: pre-wrap;">{{ $mensagem }}</p>
        </div>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="mailto:{{ $email_estudante }}?subject=Re: {{ $assunto ?? 'Contato via PesqHub UEFS' }}" 
           style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: transform 0.2s;">
            ↩️ Responder Estudante
        </a>
    </div>
    
    <div style="background-color: #f0f4f8; padding: 20px; border-radius: 8px; margin-top: 30px;">
        <p style="margin: 0; color: #4a5568; font-size: 14px; text-align: center;">
            <strong>💡 Dica:</strong> Você pode responder diretamente clicando no botão acima ou respondendo este e-mail.
        </p>
        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 12px; text-align: center;">
            Este contato foi enviado através do sistema PesqHub UEFS em {{ date('d/m/Y \à\s H:i') }}.
        </p>
    </div>
</div>
@endsection
