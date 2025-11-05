@extends('layouts.app')

@section('title', 'Teste de Validação de E-mail')

@section('content')
<div class="min-h-[calc(100vh-80px)] py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                📧 Teste de Validação de E-mail
            </h1>
            <p class="text-lg text-gray-600">
                Demonstração da validação em tempo real de e-mails já cadastrados
            </p>
        </div>

        <!-- Cards de Teste -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Card de Teste Manual -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.17c.31.17.69.17 1.01 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 ml-3">Teste Manual</h2>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Digite um e-mail para testar</label>
                        <div class="relative">
                            <input type="email" id="test-email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="exemplo@dominio.com">
                            <div id="test-spinner" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                            </div>
                        </div>
                        <div id="test-feedback" class="mt-2 text-sm hidden"></div>
                    </div>
                </div>
            </div>
            
            <!-- Card de Exemplos -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 ml-3">Exemplos para Testar</h2>
                </div>
                
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-md">
                        <p class="text-sm font-medium text-gray-700">E-mails para testar (clique para usar):</p>
                    </div>
                    
                    <button onclick="testEmail('admin@pesqhub.com')" 
                            class="w-full text-left p-3 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition">
                        <span class="text-red-600 font-mono">admin@pesqhub.com</span>
                        <span class="text-red-500 text-xs ml-2">(deve estar cadastrado)</span>
                    </button>
                    
                    <button onclick="testEmail('usuario.novo@exemplo.com')" 
                            class="w-full text-left p-3 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition">
                        <span class="text-green-600 font-mono">usuario.novo@exemplo.com</span>
                        <span class="text-green-500 text-xs ml-2">(deve estar disponível)</span>
                    </button>
                    
                    <button onclick="testEmail('email-invalido')" 
                            class="w-full text-left p-3 bg-yellow-50 border border-yellow-200 rounded-md hover:bg-yellow-100 transition">
                        <span class="text-yellow-600 font-mono">email-invalido</span>
                        <span class="text-yellow-500 text-xs ml-2">(formato inválido)</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Card de Informações -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">ℹ️ Como Funciona</h3>
            <ul class="text-sm text-gray-700 space-y-2">
                <li><strong>• Validação em Tempo Real:</strong> Verifica o e-mail 500ms após parar de digitar</li>
                <li><strong>• Validação de Formato:</strong> Verifica se o e-mail tem formato válido</li>
                <li><strong>• Verificação no Banco:</strong> Consulta se o e-mail já está cadastrado</li>
                <li><strong>• Feedback Visual:</strong> Cores e ícones indicam o status da validação</li>
                <li><strong>• Prevenção de Submit:</strong> Botão desabilitado para e-mails inválidos ou já cadastrados</li>
            </ul>
            
            <div class="mt-4 p-4 bg-white rounded-md border border-blue-200">
                <p class="text-sm font-medium text-gray-800 mb-2">Estados da Validação:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">E-mail disponível</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">E-mail já cadastrado</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">Formato inválido</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Link para Cadastro Real -->
        <div class="mt-8 text-center">
            <a href="{{ route('register') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Ir para Cadastro Real
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupEmailValidation();
});

function testEmail(email) {
    const input = document.getElementById('test-email');
    input.value = email;
    input.focus();
    
    // Triggering input event to activate validation
    const event = new Event('input', { bubbles: true });
    input.dispatchEvent(event);
}

function setupEmailValidation() {
    const emailInput = document.getElementById('test-email');
    const emailFeedback = document.getElementById('test-feedback');
    const emailSpinner = document.getElementById('test-spinner');
    
    let emailCheckTimeout;
    
    function showFeedback(message, isError = false) {
        emailFeedback.textContent = message;
        emailFeedback.className = `mt-2 text-sm ${isError ? 'text-red-600' : 'text-green-600'}`;
        emailFeedback.classList.remove('hidden');
    }
    
    function hideFeedback() {
        emailFeedback.classList.add('hidden');
    }
    
    function showSpinner() {
        emailSpinner.classList.remove('hidden');
    }
    
    function hideSpinner() {
        emailSpinner.classList.add('hidden');
    }
    
    function updateEmailFieldStyle(isError = false, isSuccess = false) {
        emailInput.classList.remove('border-gray-300', 'border-red-500', 'border-green-500', 'border-yellow-500');
        
        if (isError) {
            emailInput.classList.add('border-red-500');
        } else if (isSuccess) {
            emailInput.classList.add('border-green-500');
        } else {
            emailInput.classList.add('border-gray-300');
        }
    }
    
    async function checkEmail(email) {
        // Validação básica de e-mail
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email) {
            hideFeedback();
            updateEmailFieldStyle();
            return;
        }
        
        if (!emailRegex.test(email)) {
            showFeedback('⚠️ Formato de e-mail inválido', true);
            updateEmailFieldStyle(true);
            return;
        }
        
        showSpinner();
        hideFeedback();
        
        try {
            const response = await fetch('{{ route("check.email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            
            hideSpinner();
            
            if (data.exists) {
                showFeedback('❌ Este e-mail já está cadastrado no sistema', true);
                updateEmailFieldStyle(true);
            } else {
                showFeedback('✅ E-mail disponível para cadastro', false);
                updateEmailFieldStyle(false, true);
            }
            
        } catch (error) {
            hideSpinner();
            showFeedback('⚠️ Erro ao verificar e-mail. Verifique sua conexão.', true);
            updateEmailFieldStyle(true);
        }
    }
    
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        clearTimeout(emailCheckTimeout);
        
        if (email.length === 0) {
            hideFeedback();
            hideSpinner();
            updateEmailFieldStyle();
            return;
        }
        
        emailCheckTimeout = setTimeout(() => {
            checkEmail(email);
        }, 500);
    });
    
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email) {
            clearTimeout(emailCheckTimeout);
            checkEmail(email);
        }
    });
}
</script>
@endsection
