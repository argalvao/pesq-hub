<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro - PesqHub</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .token-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .token-label {
            color: #ffffff;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .token-code {
            font-size: 48px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .token-info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            margin-top: 15px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .warning-text {
            color: #856404;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer-text {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        .footer-link {
            color: #667eea;
            text-decoration: none;
        }
        .footer-link:hover {
            text-decoration: underline;
        }
        .steps {
            margin: 30px 0;
        }
        .step {
            display: flex;
            align-items: start;
            margin-bottom: 20px;
        }
        .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-content {
            flex: 1;
            color: #666666;
            font-size: 15px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PesqHub</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">
                Universidade Estadual de Feira de Santana
            </p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Olá, <strong>{{ $nome }}</strong>!
            </div>

            <div class="message">
                Estamos quase lá! Para concluir seu cadastro no <strong>PesqHub</strong>, 
                precisamos confirmar seu endereço de e-mail.
            </div>

            <!-- Token Box -->
            <div class="token-box">
                <div class="token-label">SEU CÓDIGO DE CONFIRMAÇÃO</div>
                <div class="token-code">{{ $token }}</div>
                <div class="token-info">
                    Este código expira em 5 minutos
                </div>
            </div>

            <!-- Steps -->
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        Retorne à página de cadastro
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        Digite o código de 6 dígitos no campo indicado
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        Clique em "Confirmar Cadastro" e pronto!
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="warning">
                <div class="warning-title">Importante</div>
                <div class="warning-text">
                    Se você não solicitou este cadastro, ignore este e-mail. 
                    Nenhuma conta será criada sem a confirmação do código.
                </div>
            </div>

            <div class="message">
                Tem dúvidas? Entre em contato conosco através do e-mail 
                <a href="mailto:abel@ecomp.uefs.br" style="color: #667eea; text-decoration: none;">
                    abel@ecomp.uefs.br
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Este é um e-mail automático do sistema <strong>PesqHub</strong><br>
                Universidade Estadual de Feira de Santana - UEFS<br>
                <a href="http://localhost:8001" class="footer-link">www.pesqhub.uefs.br</a>
            </p>
        </div>
    </div>
</body>
</html>
