@extends('layouts.app')

@section('title', 'Cadastro - PesqHub')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-8">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        
        <!-- ETAPA 1: Formulário de Cadastro -->
        <div id="cadastro-step" class="step-container">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900">Criar Conta</h2>
                <p class="text-gray-600 mt-2">Preencha os dados para começar</p>
            </div>

            <form id="form-cadastro">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                        <input type="text" id="nome" name="nome" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required autofocus>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                        <input type="email" id="email" name="email" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                    </div>

                    <div>
                        <label for="tipo_permissao" class="block text-sm font-medium text-gray-700">Tipo de Usuário</label>
                        <select id="tipo_permissao" name="tipo_permissao" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                required>
                            <option value="">Selecione o tipo</option>
                            <option value="DA">Organizador</option>
                            <option value="BASICO">Estudante</option>
                        </select>
                    </div>

                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" id="senha" name="senha" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                        <p class="text-xs text-gray-500 mt-1">Mínimo de 6 caracteres</p>
                    </div>

                    <div>
                        <label for="senha_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                        <input type="password" id="senha_confirmation" name="senha_confirmation" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                    </div>
                </div>

                <button type="submit" id="btn-cadastro"
                        class="w-full mt-6 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Criar Conta
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Já tem uma conta?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Faça login</a>
            </div>
        </div>

        <!-- ETAPA 2: Confirmação de E-mail -->
        <div id="confirmacao-step" class="step-container hidden">
            <div class="text-center mb-6">
                <div class="bg-green-100 p-4 rounded-full w-20 h-20 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.17c.31.17.69.17 1.01 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Confirme seu E-mail</h3>
                <p class="text-gray-600">
                    Enviamos um código de 6 dígitos para<br>
                    <strong id="email-enviado" class="text-indigo-600"></strong>
                </p>
            </div>

            <form id="form-confirmacao">
                <div class="space-y-4">
                    <div>
                        <label for="token" class="block text-sm font-medium text-gray-700 text-center mb-2">
                            Código de Confirmação
                        </label>
                        <input type="text" id="token" name="token" 
                               class="block w-full px-3 py-3 border-2 border-gray-300 rounded-md text-center text-3xl font-mono tracking-widest focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               maxlength="6" pattern="[0-9]{6}" placeholder="000000" required>
                        <p class="text-xs text-gray-500 text-center mt-2">Digite o código recebido por e-mail</p>
                    </div>

                    <button type="submit" id="btn-confirmar"
                            class="w-full bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirmar Cadastro
                    </button>

                    <button type="button" onclick="reenviarToken()" 
                            class="w-full border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        Reenviar Código
                    </button>

                    <button type="button" onclick="voltarCadastro()" 
                            class="w-full text-sm text-gray-600 hover:text-gray-900 mt-2">
                        ← Voltar para cadastro
                    </button>
                </div>
            </form>

            <!-- Timer de Expiração -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    Código expira em: <strong id="timer" class="text-red-600">5:00</strong>
                </p>
            </div>
        </div>

        <!-- ETAPA 3: Sucesso -->
        <div id="sucesso-step" class="step-container hidden">
            <div class="text-center">
                <div class="bg-green-100 p-4 rounded-full w-20 h-20 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-green-600 mb-2">Cadastro Confirmado!</h3>
                <p class="text-gray-600 mb-6">Sua conta foi criada com sucesso.</p>
                <a href="{{ route('login') }}" 
                   class="inline-block bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Fazer Login Agora
                </a>
            </div>
        </div>

        <!-- Mensagens de Feedback -->
        <div id="feedback-message" class="hidden mt-4 p-4 rounded-md"></div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500">
                ← Voltar para a página inicial
            </a>
        </div>
    </div>
</div>

<script>
let emailUsuario = '';
let countdown;

// ETAPA 1: Cadastro
document.getElementById('form-cadastro').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btn-cadastro');
    btn.disabled = true;
    btn.textContent = 'Enviando...';
    
    const formData = new FormData(e.target);
    const data = {
        nome: formData.get('nome'),
        email: formData.get('email'),
        senha: formData.get('senha'),
        senha_confirmation: formData.get('senha_confirmation'),
        tipo_permissao: formData.get('tipo_permissao')
    };
    
    // Validação básica de senha
    if (data.senha !== data.senha_confirmation) {
        showFeedback('As senhas não coincidem', 'error');
        btn.disabled = false;
        btn.textContent = 'Criar Conta';
        return;
    }
    
    if (data.senha.length < 6) {
        showFeedback('A senha deve ter no mínimo 6 caracteres', 'error');
        btn.disabled = false;
        btn.textContent = 'Criar Conta';
        return;
    }
    
    try {
        const response = await fetch('/cadastro/solicitar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            emailUsuario = data.email;
            document.getElementById('email-enviado').textContent = emailUsuario;
            
            // Mudar para etapa 2
            document.getElementById('cadastro-step').classList.add('hidden');
            document.getElementById('confirmacao-step').classList.remove('hidden');
            
            // Iniciar timer de 5 minutos
            iniciarTimer(300);
            
            showFeedback(result.message, 'success');
        } else {
            showFeedback(result.message, 'error');
            btn.disabled = false;
            btn.textContent = 'Criar Conta';
        }
    } catch (error) {
        console.error('Erro:', error);
        showFeedback('Erro de conexão. Verifique se o servidor está rodando.', 'error');
        btn.disabled = false;
        btn.textContent = 'Criar Conta';
    }
});

// ETAPA 2: Confirmação
document.getElementById('form-confirmacao').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btn-confirmar');
    btn.disabled = true;
    btn.textContent = 'Verificando...';
    
    const token = document.getElementById('token').value;
    
    if (token.length !== 6) {
        showFeedback('O código deve ter 6 dígitos', 'error');
        btn.disabled = false;
        btn.textContent = 'Confirmar Cadastro';
        return;
    }
    
    try {
        const response = await fetch('/cadastro/confirmar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: emailUsuario,
                token: token
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            clearInterval(countdown);
            
            // Mudar para etapa 3
            document.getElementById('confirmacao-step').classList.add('hidden');
            document.getElementById('sucesso-step').classList.remove('hidden');
            
            showFeedback(result.message, 'success');
            
            // Redirecionar após 3 segundos
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 3000);
        } else {
            showFeedback(result.message, 'error');
            btn.disabled = false;
            btn.textContent = 'Confirmar Cadastro';
            document.getElementById('token').value = '';
            document.getElementById('token').focus();
        }
    } catch (error) {
        console.error('Erro:', error);
        showFeedback('Erro de conexão. Tente novamente.', 'error');
        btn.disabled = false;
        btn.textContent = 'Confirmar Cadastro';
    }
});

// Reenviar token
async function reenviarToken() {
    try {
        const response = await fetch('/cadastro/reenviar-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: emailUsuario })
        });
        
        const result = await response.json();
        showFeedback(result.message, result.success ? 'success' : 'error');
        
        if (result.success) {
            document.getElementById('token').value = '';
            document.getElementById('token').focus();
            iniciarTimer(300); // Reiniciar timer
        }
    } catch (error) {
        console.error('Erro:', error);
        showFeedback('Erro ao reenviar código.', 'error');
    }
}

// Voltar para cadastro
function voltarCadastro() {
    if (confirm('Tem certeza? Você perderá o progresso atual e precisará iniciar o cadastro novamente.')) {
        clearInterval(countdown);
        document.getElementById('confirmacao-step').classList.add('hidden');
        document.getElementById('cadastro-step').classList.remove('hidden');
        document.getElementById('form-cadastro').reset();
        document.getElementById('btn-cadastro').disabled = false;
        document.getElementById('btn-cadastro').textContent = 'Criar Conta';
        document.getElementById('token').value = '';
        hideFeedback();
    }
}

// Timer de expiração
function iniciarTimer(segundos) {
    clearInterval(countdown);
    let tempo = segundos;
    
    const timerElement = document.getElementById('timer');
    
    countdown = setInterval(() => {
        const minutos = Math.floor(tempo / 60);
        const segs = tempo % 60;
        timerElement.textContent = `${minutos}:${segs.toString().padStart(2, '0')}`;
        
        // Mudar cor quando estiver acabando
        if (tempo <= 60) {
            timerElement.classList.add('text-red-600', 'font-bold');
        }
        
        if (tempo <= 0) {
            clearInterval(countdown);
            timerElement.textContent = '0:00';
            showFeedback('Código expirado. Clique em "Reenviar Código" para receber um novo.', 'error');
        }
        tempo--;
    }, 1000);
}

// Feedback visual
function showFeedback(message, type) {
    const feedbackDiv = document.getElementById('feedback-message');
    feedbackDiv.className = `mt-4 p-4 rounded-md ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}`;
    feedbackDiv.textContent = message;
    feedbackDiv.classList.remove('hidden');
    
    // Auto-hide após 5 segundos apenas para mensagens de sucesso
    if (type === 'success') {
        setTimeout(() => {
            feedbackDiv.classList.add('hidden');
        }, 5000);
    }
}

function hideFeedback() {
    document.getElementById('feedback-message').classList.add('hidden');
}

// Permitir apenas números no token
document.getElementById('token').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
});

// Auto-focus no token quando a etapa 2 aparecer
document.getElementById('token').addEventListener('focus', function(e) {
    e.target.select();
});
</script>
@endsection
