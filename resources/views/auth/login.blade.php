@extends('layouts.app')

@section('title', 'Login - PesqHub')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Login</h2>
            <p class="text-gray-600 mt-2">Digite suas credenciais para acessar o painel</p>
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

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                           required autofocus>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" id="password" name="password" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                           required>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Entrar
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500">
                ← Voltar para a página inicial
            </a>
        </div>

        <div class="mt-4 p-4 bg-gray-50 rounded-md">
            <p class="text-sm text-gray-600 text-center">
                <strong>Acesso de demonstração:</strong><br>
                E-mail: admin@pesqhub.com<br>
                Senha: admin123
            </p>
        </div>
    </div>
</div>
@endsection
