<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PesqHub UEFS')</title>
    @php
        // Define o assunto do e-mail se especificado no template filho
        if (isset($__env) && $__env->hasSection('subject')) {
            $this->subject($__env->yieldContent('subject'));
        }
    @endphp
    <style>
        /* Reset e base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        
        /* Container principal */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .header .subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .header .institution {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        /* Conteúdo */
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 20px;
        }
        
        /* Botões */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #4fd1c7 0%, #81c784 100%);
        }
        
        /* Cards de informação */
        .info-card {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .info-card h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        /* Alertas */
        .alert {
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #f0fff4;
            border-color: #38a169;
            color: #2f855a;
        }
        
        .alert-info {
            background-color: #ebf8ff;
            border-color: #3182ce;
            color: #2c5282;
        }
        
        .alert-warning {
            background-color: #fffbeb;
            border-color: #ed8936;
            color: #c05621;
        }
        
        /* Footer */
        .footer {
            background-color: #2d3748;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
        }
        
        .footer-content {
            margin-bottom: 20px;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: #81c784;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        
        .footer-divider {
            height: 1px;
            background-color: #4a5568;
            margin: 20px 0;
        }
        
        .footer-small {
            font-size: 12px;
            color: #718096;
        }
        
        /* Responsivo */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header .logo {
                font-size: 28px;
            }
        }
        
        /* Utilitários */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .mb-20 { margin-bottom: 20px; }
        .mt-20 { margin-top: 20px; }
        .p-20 { padding: 20px; }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">📚 PesqHub</div>
            <div class="subtitle">Sistema de Pesquisa e Extensão</div>
            <div class="institution">Universidade Estadual de Feira de Santana</div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="content">
            @hasSection('greeting')
                @yield('greeting')
            @endif
            
            @yield('content')
            
            @hasSection('action')
                <div class="text-center mt-20">
                    @yield('action')
                </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <strong>PesqHub - UEFS</strong><br>
                Sistema de Gerenciamento de Pesquisa e Extensão
            </div>
            
            @hasSection('footer-links')
                <div class="footer-links">
                    @yield('footer-links')
                </div>
            @endif
            
            <div class="footer-divider"></div>
            
            <div class="footer-small">
                <p>Este e-mail foi enviado automaticamente pelo sistema PesqHub.</p>
                <p>© {{ date('Y') }} Universidade Estadual de Feira de Santana - UEFS</p>
                <p>
                    <strong>Data/Hora:</strong> {{ date('d/m/Y H:i:s') }} | 
                    <strong>Ambiente:</strong> {{ config('app.env') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>