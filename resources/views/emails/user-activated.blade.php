@extends('emails.template')

@section('title', 'Conta Ativada - PesqHub UEFS')

@section('content')
<div style="padding: 30px; background-color: #ffffff; border-radius: 10px; margin: 20px 0;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #10B981; font-size: 28px; margin: 0; font-weight: bold;">
            ✅ Conta Ativada com Sucesso!
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
        <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 15px 0 0 0;">
            Ótima notícia! Sua conta no PesqHub foi <strong>ativada com sucesso</strong> pelo nosso administrador. 
            Agora você já pode acessar todas as funcionalidades da plataforma! 🎉
        </p>
    </div>

    <!-- Status da Conta -->
    <div style="text-align: center; margin: 30px 0;">
        <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 20px; border-radius: 15px; display: inline-block;">
            <p style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
                Status da Conta
            </p>
            <p style="margin: 10px 0 0 0; font-size: 24px; font-weight: bold;">
                🟢 ATIVA
            </p>
        </div>
    </div>

    <!-- Informações da Conta -->
    <div style="background-color: #F0FDF4; padding: 20px; border-radius: 10px; margin: 25px 0; border-left: 4px solid #10B981;">
        <h3 style="color: #065F46; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
            📋 Informações da sua conta:
        </h3>
        <ul style="color: #047857; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px; list-style: none;">
            <li style="margin-bottom: 8px;"><strong>📧 E-mail:</strong> {{ $email_usuario }}</li>
            <li style="margin-bottom: 8px;"><strong>👤 Nome:</strong> {{ $nome_usuario }}</li>
            <li style="margin-bottom: 8px;"><strong>✅ Status:</strong> Conta Ativada</li>
            <li><strong>🔐 Acesso:</strong> Liberado para login</li>
        </ul>
    </div>

    <!-- Call to Action -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $login_url }}" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: white; text-decoration: none; padding: 15px 30px; border-radius: 10px; display: inline-block; font-weight: bold; font-size: 16px; transition: all 0.3s ease;">
            🚀 Acessar o PesqHub
        </a>
    </div>

    <!-- Instruções -->
    <div style="background-color: #F8FAFC; padding: 20px; border-radius: 10px; margin: 25px 0;">
        <h3 style="color: #374151; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
            🎯 O que você pode fazer agora:
        </h3>
        <ul style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">🔍 Explorar o catálogo de professores e pesquisadores</li>
            <li style="margin-bottom: 8px;">📨 Entrar em contato com professores por suas áreas de interesse</li>
            <li style="margin-bottom: 8px;">🎓 Descobrir oportunidades de pesquisa na UEFS</li>
            <li style="margin-bottom: 8px;">📚 Navegar pelas diferentes linhas de pesquisa disponíveis</li>
            <li>⚙️ Gerenciar suas informações de perfil</li>
        </ul>
    </div>

    <!-- Informações de Suporte -->
    <div style="background-color: #FEF3C7; padding: 15px; border-radius: 10px; margin: 25px 0; border: 1px solid #F59E0B;">
        <p style="color: #92400E; font-size: 14px; line-height: 1.5; margin: 0; text-align: center;">
            <strong>💡 Dica:</strong> Use suas credenciais de cadastro para fazer login na plataforma.
            Se precisar de ajuda, entre em contato com nossa equipe de suporte.
        </p>
    </div>

    <!-- Footer -->
    <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
        <p style="color: #9CA3AF; font-size: 12px; line-height: 1.5; margin: 0;">
            Este e-mail foi enviado automaticamente pelo sistema PesqHub.<br>
            Se você não solicitou esta ativação, entre em contato conosco imediatamente.
        </p>
        <p style="color: #9CA3AF; font-size: 12px; margin: 10px 0 0 0;">
            © {{ date('Y') }} PesqHub - Universidade Estadual de Feira de Santana
        </p>
    </div>
</div>
@endsection
