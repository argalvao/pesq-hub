@extends('emails.template')

@section('title', 'Conta Ativada - PesqHub UEFS')

@section('content')
<div style="padding: 30px; background-color: #ffffff; border-radius: 10px; margin: 20px 0;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #4F46E5; font-size: 28px; margin: 0; font-weight: bold;">
            ✅ Conta Ativada com Sucesso!
        </h1>
        <p style="color: #6B7280; font-size: 16px; margin: 10px 0 0 0;">
            PesqHub - Universidade Estadual de Feira de Santana
        </p>
    </div>

    <!-- Saudação -->
    <div style="margin-bottom: 25px;">
        <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0;">
            Olá <strong>{{ $usuario->nome }}</strong>,
        </p>
        <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 10px 0 0 0;">
            Temos o prazer de informar que sua conta no PesqHub UEFS foi ativada com sucesso!
        </p>
    </div>

    <!-- Informações de acesso -->
    <div style="background-color: #F3F4F6; padding: 20px; border-radius: 10px; margin: 25px 0;">
        <h3 style="color: #374151; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
            🔑 Suas informações de acesso:
        </h3>
        <p style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0;">
            <strong>Email:</strong> {{ $usuario->email }}<br>
            <strong>Senha:</strong> Use a senha que você cadastrou durante o registro
        </p>
    </div>

    <!-- Recursos disponíveis -->
    <div style="margin: 25px 0;">
        <h3 style="color: #374151; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
            🎯 Agora você pode:
        </h3>
        <ul style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">Acessar completamente a plataforma</li>
            <li style="margin-bottom: 8px;">Buscar professores e linhas de pesquisa</li>
            <li style="margin-bottom: 8px;">Entrar em contato com pesquisadores</li>
            <li>Gerenciar e atualizar seu perfil</li>
        </ul>
    </div>

    <!-- Botão de acesso -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $loginUrl }}" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: white; text-decoration: none; padding: 15px 30px; border-radius: 25px; font-weight: 600; font-size: 16px; display: inline-block; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); transition: all 0.3s ease;">
            Acessar Plataforma
        </a>
    </div>

    <!-- Dica -->
    <div style="background-color: #EFF6FF; border-left: 4px solid #3B82F6; padding: 15px; border-radius: 5px; margin: 25px 0;">
        <p style="color: #1E40AF; font-size: 14px; line-height: 1.6; margin: 0;">
            <strong>💡 Dica:</strong> Complete seu perfil para melhorar sua experiência e ajudar outros pesquisadores a encontrá-lo mais facilmente!
        </p>
    </div>

    <!-- Rodapé -->
    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
        <p style="color: #9CA3AF; font-size: 12px; margin: 0;">
            Este é um email automático. Se você não solicitou essa ativação, entre em contato conosco.
        </p>
    </div>
</div>
@endsection
