@extends('layouts.app')

@section('title', 'Confirmar E-mail - PesqHub')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-8">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-6">
            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Confirme seu E-mail</h2>
            <p class="text-gray-600 mt-2">Enviamos um código de 6 dígitos para</p>
            <p class="text-indigo-600 font-semibold mt-1">{{ session('email') ?? $email ?? '' }}</p>
        </div>

        <div id="alert-container"></div>

        <form id="tokenForm" class="space-y-6">
            @csrf
            <input type="hidden" id="email" name="email" value="{{ session('email') ?? $email ?? '' }}">
            
            <div>
                <label for="token" class="block text-sm font-medium text-gray-700 mb-2">
                    Código de Confirmação
                </label>
                <input 
                    type="text" 
                    id="token" 
                    name="token" 
                    maxlength="6"
                    pattern="[0-9]{6}"
                    placeholder="000000"
                    class="text-center text-2xl font-mono tracking-widest block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required 
                    autofocus>
                <p class="mt-2 text-sm text-gray-500">Digite o código de 6 dígitos recebido por e-mail</p>
            </div>

            <div>
                <button 
                    type="submit" 
                    id="confirmButton"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <span id="buttonText">Confirmar E-mail</span>
                    <svg id="buttonSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-6 text-center space-y-3">
            <p class="text-sm text-gray-600">Não recebeu o código?</p>
            <button 
                id="resendButton"
                class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm transition-colors">
                Reenviar código
            </button>
            <div id="resend-timer" class="hidden text-sm text-gray-500">
                Aguarde <span id="countdown">60</span>s para reenviar
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    Voltar para o login
                </a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tokenForm = document.getElementById('tokenForm');
    const tokenInput = document.getElementById('token');
    const emailInput = document.getElementById('email');
    const confirmButton = document.getElementById('confirmButton');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');
    const resendButton = document.getElementById('resendButton');
    const resendTimer = document.getElementById('resend-timer');
    const countdown = document.getElementById('countdown');
    const alertContainer = document.getElementById('alert-container');

    let countdownInterval;
    let timeLeft = 0;

    tokenInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    tokenInput.addEventListener('input', function(e) {
        if (this.value.length === 6) {
        }
    });

    // Confirmar token
    tokenForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const token = tokenInput.value.trim();
        const email = emailInput.value.trim();

        if (token.length !== 6) {
            showAlert('O código deve ter 6 dígitos', 'error');
            return;
        }

        setLoading(true);

        try {
            const response = await fetch('/confirm-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, token })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('E-mail confirmado com sucesso! Redirecionando...', 'success');
                setTimeout(() => {
                    // Redirecionar para URL específica ou login
                    window.location.href = data.redirect || '/login';
                }, 1500);
            } else {
                showAlert(data.message || 'Token inválido', 'error');
                tokenInput.value = '';
                tokenInput.focus();
            }
        } catch (error) {
            showAlert(' Erro ao verificar token. Tente novamente.', 'error');
        } finally {
            setLoading(false);
        }
    });

    // Reenviar token
    resendButton.addEventListener('click', async function() {
        if (timeLeft > 0) return;

        const email = emailInput.value.trim();
        
        if (!email) {
            showAlert('E-mail não encontrado', 'error');
            return;
        }

        try {
            const response = await fetch('/api/token/enviar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    email: email,
                    nome: 'Usuário' 
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert(' Novo código enviado para seu e-mail!', 'success');
                startCountdown(60);
            } else {
                showAlert(data.message || ' Erro ao reenviar código', 'error');
            }
        } catch (error) {
            showAlert(' Erro ao reenviar código. Tente novamente.', 'error');
        }
    });

    // Countdown para reenvio
    function startCountdown(seconds) {
        timeLeft = seconds;
        resendButton.classList.add('hidden');
        resendTimer.classList.remove('hidden');

        countdownInterval = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                resendButton.classList.remove('hidden');
                resendTimer.classList.add('hidden');
            }
        }, 1000);
    }

    // Mostrar alertas
    function showAlert(message, type) {
        const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        
        alertContainer.innerHTML = `
            <div class="${bgColor} border px-4 py-3 rounded mb-4">
                ${message}
            </div>
        `;

        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    // Loading state
    function setLoading(loading) {
        if (loading) {
            confirmButton.disabled = true;
            buttonText.textContent = 'Verificando...';
            buttonSpinner.classList.remove('hidden');
        } else {
            confirmButton.disabled = false;
            buttonText.textContent = 'Confirmar E-mail';
            buttonSpinner.classList.add('hidden');
        }
    }
});
</script>
@endsection
