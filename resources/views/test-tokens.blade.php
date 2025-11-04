@extends('layouts.app')

@section('title', 'Teste do Sistema de Tokens')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🔐 Sistema de Confirmação por Token
            </h1>
            <p class="text-lg text-gray-600">
                Demonstração do sistema de envio e verificação de tokens por e-mail
            </p>
        </div>

        <!-- Cards de Teste -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Card 1: Enviar Token -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.17c.31.17.69.17 1.01 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 ml-3">Enviar Token</h2>
                </div>
                
                <form id="enviarTokenForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" id="email_envio" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Digite seu e-mail" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <input type="text" id="nome_envio" name="nome" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Seu nome completo" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuário</label>
                        <select id="tipo_envio" name="tipo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="estudante">Estudante</option>
                            <option value="professor">Professor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        📧 Enviar Token por E-mail
                    </button>
                </form>
                
                <div id="resultadoEnvio" class="mt-4 hidden"></div>
            </div>
            
            <!-- Card 2: Verificar Token -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 ml-3">Verificar Token</h2>
                </div>
                
                <form id="verificarTokenForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" id="email_verificacao" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Digite seu e-mail" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Token (6 dígitos)</label>
                        <input type="text" id="token_verificacao" name="token" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-lg font-mono"
                               placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-green-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                        ✅ Verificar Token
                    </button>
                </form>
                
                <div id="resultadoVerificacao" class="mt-4 hidden"></div>
            </div>
        </div>
        
        <!-- Card de Status -->
        <div class="mt-8 bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center mb-6">
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 ml-3">Consultar Status do Token</h2>
            </div>
            
            <div class="flex space-x-4">
                <input type="email" id="email_consulta" 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Digite o e-mail para consultar">
                <button onclick="consultarToken()" 
                        class="bg-purple-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-purple-700 transition duration-200">
                    🔍 Consultar
                </button>
            </div>
            
            <div id="resultadoConsulta" class="mt-4 hidden"></div>
        </div>
        
        <!-- Informações -->
        <div class="mt-8 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">ℹ️ Informações Importantes</h3>
            <ul class="text-sm text-gray-700 space-y-2">
                <li><strong>• Validade:</strong> Tokens expiram em <strong>5 minutos</strong></li>
                <li><strong>• Tentativas:</strong> Máximo de <strong>3 tentativas</strong> por token</li>
                <li><strong>• Formato:</strong> Token possui <strong>6 dígitos numéricos</strong></li>
                <li><strong>• Uso único:</strong> Token é <strong>removido</strong> após verificação correta</li>
                <li><strong>• Cache:</strong> Tokens são salvos em <strong>cache do Laravel</strong></li>
            </ul>
        </div>
    </div>
</div>

<script>
// Enviar Token
document.getElementById('enviarTokenForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = {
        email: formData.get('email'),
        nome: formData.get('nome'),
        tipo: formData.get('tipo')
    };
    
    try {
        const response = await fetch('/token/teste', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        mostrarResultado('resultadoEnvio', result, response.ok);
        
        if (response.ok && result.success) {
            // Auto-preencher e-mail na verificação
            document.getElementById('email_verificacao').value = data.email;
            document.getElementById('email_consulta').value = data.email;
        }
    } catch (error) {
        mostrarResultado('resultadoEnvio', {message: 'Erro de conexão: ' + error.message}, false);
    }
});

// Verificar Token
document.getElementById('verificarTokenForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = {
        email: formData.get('email'),
        token: formData.get('token')
    };
    
    try {
        const response = await fetch('/token/verificar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        mostrarResultado('resultadoVerificacao', result, response.ok);
    } catch (error) {
        mostrarResultado('resultadoVerificacao', {message: 'Erro de conexão: ' + error.message}, false);
    }
});

// Consultar Token
async function consultarToken() {
    const email = document.getElementById('email_consulta').value;
    
    if (!email) {
        mostrarResultado('resultadoConsulta', {message: 'Digite um e-mail'}, false);
        return;
    }
    
    try {
        const response = await fetch(`/token/consultar?email=${encodeURIComponent(email)}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            mostrarResultado('resultadoConsulta', result.data, true);
        } else {
            mostrarResultado('resultadoConsulta', result, false);
        }
    } catch (error) {
        mostrarResultado('resultadoConsulta', {message: 'Erro de conexão: ' + error.message}, false);
    }
}

// Função auxiliar para mostrar resultados
function mostrarResultado(elementId, data, isSuccess) {
    const element = document.getElementById(elementId);
    element.className = `mt-4 p-4 rounded-md ${isSuccess ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'}`;
    
    let html = `<pre class="text-sm whitespace-pre-wrap">${JSON.stringify(data, null, 2)}</pre>`;
    
    element.innerHTML = html;
    element.classList.remove('hidden');
    
    // Auto-hide após 10 segundos
    setTimeout(() => {
        element.classList.add('hidden');
    }, 10000);
}

// Permitir apenas números no campo de token
document.getElementById('token_verificacao').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
