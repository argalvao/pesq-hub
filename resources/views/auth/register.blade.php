@extends('layouts.app')

@section('title', 'Cadastro - PesqHub')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-8">
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

        {{-- Adicionado um ID ao formulário para o JavaScript --}}
        <form method="POST" action="{{ route('register') }}" id="registerForm">
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
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                           required>
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

            {{-- INÍCIO: Seção Termos LGPD --}}
            <div class="mt-6 space-y-4">
                <div>
                    <label for="lgpd_terms" class="block text-sm font-medium text-gray-700">Termos de Uso e Política de Privacidade (LGPD)</label>
                    <textarea id="lgpd_terms" name="lgpd_terms" readonly
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 resize-y" 
                              style="height: 200px;">
                        {{-- 
                            VErificar as leis LGPD. Eu criei uma gênerica
                        --}}ACEITO OS TERMOS DA LGPD

                        1. FINALIDADE, ACEITAÇÃO E ABRANGÊNCIA
                        1.1. Para o devido cumprimento da legislação brasileira aplicável à proteção dos dados pessoais, é necessário que o Usuário do website (ora em diante “Usuário”) aceite a presente Política de Privacidade e autorize a empresa a proceder ao tratamento dos seus dados pessoais.

                        2. COLETA DE DADOS
                        2.1. A empresa poderá coletar dados pessoais do Usuário quando este:
                        a) Se cadastrar no website;
                        b) Utilizar os serviços da plataforma.
                        2.2. Os dados coletados poderão incluir, mas não se limitam a: nome, e-mail, tipo de usuário.

                        3. USO DOS DADOS
                        3.1. Os dados pessoais coletados serão utilizados para as seguintes finalidades:
                        a) Processar e completar o cadastro do Usuário;
                        b) Permitir o acesso e uso das funcionalidades da plataforma;
                        c) Enviar comunicações sobre o serviço;
                        d) Melhorar a experiência do Usuário no website.

                        4. COMPARTILHAMENTO DE DADOS
                        4.1. A empresa não compartilhará os dados pessoais do Usuário com terceiros, exceto com o consentimento expresso do Usuário ou por força de lei.

                        5. DIREITOS DO TITULAR DOS DADOS
                        5.1. O Usuário tem o direito de solicitar o acesso, a correção, a exclusão ou a portabilidade dos seus dados pessoais, a qualquer momento, mediante solicitação enviada através dos canais de suporte da plataforma.

                        Ao marcar a caixa de aceitação, o Usuário declara ter lido e concordado integralmente com os termos desta Política de Privacidade.
                    </textarea>
                </div>

                <div class="flex items-center">
                    <input id="lgpd_accept" name="lgpd_accept" type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="lgpd_accept" class="ml-2 block text-sm text-gray-900">
                        Afirmo que li e aceito os termos da LGPD
                    </label>
                </div>

                {{-- Mensagem de erro que aparece se o checkbox não for marcado --}}
                <p id="lgpd_error" class="text-red-600 text-sm !mt-2 hidden">
                    Você precisa aceitar os termos para prosseguir com o cadastro.
                </p>
            </div>
            {{-- FIM: Seção Termos LGPD --}}


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

{{-- Script para validação do checkbox da LGPD --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const lgpdCheckbox = document.getElementById('lgpd_accept');
    const lgpdError = document.getElementById('lgpd_error');

    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            // Verifica se o checkbox NÃO está marcado
            if (!lgpdCheckbox.checked) {
                event.preventDefault(); // Impede o envio do formulário
                lgpdError.classList.remove('hidden'); // Mostra a mensagem de erro
            } else {
                lgpdError.classList.add('hidden'); // Oculta a mensagem de erro se estiver marcada
            }
        });
    }

    // Opcional: Oculta a mensagem de erro assim que o usuário marcar a caixa
    if (lgpdCheckbox) {
        lgpdCheckbox.addEventListener('change', function() {
            if (lgpdCheckbox.checked) {
                lgpdError.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection