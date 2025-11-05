@extends('layouts.app')

@section('title', 'Cadastro - PesqHub')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Criar Conta</h2>
            <p class="text-gray-600 mt-2">Cadastre-se para acessar o sistema</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                           required autofocus>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                        <div id="email-spinner" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                        </div>
                    </div>
                    <div id="email-feedback" class="mt-1 text-sm hidden"></div>
                </div>

                <div>
                    <label for="nivel_permissao" class="block text-sm font-medium text-gray-700">Tipo de Usuário</label>
                    <select id="nivel_permissao" name="nivel_permissao" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                            required>
                        <option value="">Selecione o tipo</option>
                        <option value="2" {{ old('nivel_permissao') == '2' ? 'selected' : '' }}>Organizador</option>
                        <option value="3" {{ old('nivel_permissao') == '3' ? 'selected' : '' }}>Estudante</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" id="password" name="password" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                           required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                           required>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Criar Conta
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Já tem uma conta?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Faça login</a>
            </p>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500">
                ← Voltar para a página inicial
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('email-feedback');
    const emailSpinner = document.getElementById('email-spinner');
    const submitButton = document.querySelector('button[type="submit"]');
    
    let emailCheckTimeout;
    let isEmailValid = false;
    
    function showFeedback(message, isError = false) {
        emailFeedback.textContent = message;
        emailFeedback.className = `mt-1 text-sm ${isError ? 'text-red-600' : 'text-green-600'}`;
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
    
    function updateEmailFieldStyle(isError = false) {
        if (isError) {
            emailInput.classList.remove('border-gray-300', 'focus:border-indigo-500', 'border-green-500', 'focus:border-green-500');
            emailInput.classList.add('border-red-500', 'focus:border-red-500');
        } else if (isEmailValid) {
            emailInput.classList.remove('border-gray-300', 'focus:border-indigo-500', 'border-red-500', 'focus:border-red-500');
            emailInput.classList.add('border-green-500', 'focus:border-green-500');
        } else {
            emailInput.classList.remove('border-red-500', 'focus:border-red-500', 'border-green-500', 'focus:border-green-500');
            emailInput.classList.add('border-gray-300', 'focus:border-indigo-500');
        }
    }
    
    function updateSubmitButton() {
        if (isEmailValid) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
    
    async function checkEmail(email) {
        // Validação básica de e-mail
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email || !emailRegex.test(email)) {
            if (email && !emailRegex.test(email)) {
                showFeedback('⚠️ Formato de e-mail inválido', true);
                isEmailValid = false;
                updateEmailFieldStyle(true);
            } else {
                hideFeedback();
                isEmailValid = false;
                updateEmailFieldStyle();
            }
            updateSubmitButton();
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
                showFeedback('❌ Este e-mail já está cadastrado no sistema. Tente fazer login ou use outro e-mail.', true);
                isEmailValid = false;
                updateEmailFieldStyle(true);
            } else {
                showFeedback('✅ E-mail disponível para cadastro', false);
                isEmailValid = true;
                updateEmailFieldStyle(false);
            }
            
        } catch (error) {
            hideSpinner();
            showFeedback('⚠️ Erro ao verificar e-mail. Tente novamente.', true);
            isEmailValid = false;
            updateEmailFieldStyle(true);
        }
        
        updateSubmitButton();
    }
    
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Limpar timeout anterior
        clearTimeout(emailCheckTimeout);
        
        if (email.length === 0) {
            hideFeedback();
            hideSpinner();
            isEmailValid = false;
            updateEmailFieldStyle();
            updateSubmitButton();
            return;
        }
        
        // Aguardar 500ms após parar de digitar
        emailCheckTimeout = setTimeout(() => {
            checkEmail(email);
        }, 500);
    });
    
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && email.includes('@')) {
            clearTimeout(emailCheckTimeout);
            checkEmail(email);
        }
    });
    
    // Inicializar botão como desabilitado
    updateSubmitButton();
    
    // Verificar se há valor inicial (old input)
    if (emailInput.value) {
        checkEmail(emailInput.value);
    }
});
</script>
@endsection
