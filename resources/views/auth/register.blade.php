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
                        <option value="2" {{ old('nivel_permissao') == '2' ? 'selected' : '' }}>Professor</option>
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
@endsection
