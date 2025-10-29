@extends('emails.template')

@section('titulo', 'Confirmação de Cadastro - PesqHub UEFS')

@section('conteudo')
<div style="padding: 30px; background-color: #ffffff; border-radius: 10px; margin: 20px 0;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #4F46E5; font-size: 28px; margin: 0; font-weight: bold;">
            🔐 Confirmação de Cadastro
        </h1>
        <p style="color: #6B7280; font-size: 16px; margin: 10px 0 0 0;">
            PesqHub - Universidade Estadual de Feira de Santana
        </p>
    </div>

    <!-- Saudação -->
    <div style="margin-bottom: 25px;">
        <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0;">
            Olá <strong>{{ $nome_usuario }}</strong>,
        </p>
        <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 10px 0 0 0;">
            Seja bem-vindo(a) ao PesqHub! Para finalizar seu cadastro, utilize o código de confirmação abaixo:
        </p>
    </div>

    <!-- Token -->
    <div style="text-align: center; margin: 30px 0;">
        <div style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: white; padding: 20px; border-radius: 15px; display: inline-block; min-width: 200px;">
            <p style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
                Seu Código de Confirmação
            </p>
            <p style="margin: 10px 0 0 0; font-size: 36px; font-weight: bold; letter-spacing: 4px; font-family: 'Courier New', monospace;">
                {{ $token }}
            </p>
        </div>
    </div>

    <!-- Instruções -->
    <div style="background-color: #F3F4F6; padding: 20px; border-radius: 10px; margin: 25px 0;">
        <h3 style="color: #374151; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
            📋 Como usar o código:
        </h3>
        <ol style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">Volte para a página de cadastro</li>
            <li style="margin-bottom: 8px;">Digite o código de 6 dígitos no campo de confirmação</li>
            <li style="margin-bottom: 8px;">Clique em "Confirmar Cadastro"</li>
            <li>Pronto! Seu cadastro estará ativo</li>
        </ol>
    </div>

    <!-- Informações importantes -->
    <div style="background-color: #FEF3C7; border: 2px solid #F59E0B; padding: 15px; border-radius: 8px; margin: 25px 0;">
        <p style="color: #92400E; font-size: 14px; margin: 0; font-weight: 500;">
            ⚠️ <strong>Importante:</strong> Este código é válido por apenas <strong>5 minutos</strong> e só pode ser usado uma vez.
        </p>
    </div>

    <!-- Dados do cadastro -->
    <div style="margin: 25px 0;">
        <h3 style="color: #374151; font-size: 16px; margin: 0 0 10px 0; font-weight: 600;">
            📊 Dados do seu cadastro:
        </h3>
        <ul style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px; list-style-type: none;">
            <li style="margin-bottom: 5px;">✉️ <strong>E-mail:</strong> {{ $email_usuario }}</li>
            @if(isset($tipo_usuario))
            <li style="margin-bottom: 5px;">👤 <strong>Tipo:</strong> {{ ucfirst($tipo_usuario) }}</li>
            @endif
            <li style="margin-bottom: 5px;">🕐 <strong>Solicitado em:</strong> {{ date('d/m/Y \à\s H:i') }}</li>
        </ul>
    </div>
</div>

<!-- Footer informativo -->
<div style="text-align: center; margin-top: 30px; padding: 20px; background-color: #F9FAFB; border-radius: 10px;">
    <p style="color: #9CA3AF; font-size: 12px; margin: 0; line-height: 1.5;">
        Se você não solicitou este cadastro, pode ignorar este e-mail com segurança.<br>
        Este código expira automaticamente em 5 minutos.
    </p>
    <hr style="border: none; border-top: 1px solid #E5E7EB; margin: 15px 0;">
    <p style="color: #6B7280; font-size: 13px; margin: 0; font-weight: 500;">
        🎓 PesqHub - Sistema de Pesquisa Acadêmica<br>
        Universidade Estadual de Feira de Santana (UEFS)
    </p>
</div>
@endsection
