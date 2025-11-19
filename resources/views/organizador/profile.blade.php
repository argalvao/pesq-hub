@extends('layouts.app')

@section('title', 'Editar Perfil - PesqHub')

@section('content')
<div class="container mx-auto px-4 lg:px-6 py-4 md:py-8">
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl md:text-3xl font-bold">Editar Perfil</h1>
        <a href="{{ route('organizador.dashboard') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center gap-2 text-sm md:text-base">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="hidden sm:inline">Voltar ao Dashboard</span>
            <span class="sm:hidden">Voltar</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    {{ session('error') }}
                    @if($errors->any())
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
        <form method="POST" action="{{ route('organizador.profile.update') }}">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div class="mb-6 md:mb-8">
                <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4 text-gray-800 border-b pb-2">Informações Básicas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome', $organizadorData['nome']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $organizadorData['email']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                               required>
                    </div>
                </div>
            </div>

            <!-- Informações Acadêmicas -->
            <div class="mb-6 md:mb-8">
                <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4 text-gray-800 border-b pb-2">Informações Acadêmicas</h2>
                <div class="max-w-full md:max-w-md">
                    <label for="id_curso" class="block text-sm font-medium text-gray-700 mb-1">
                        Curso <span class="text-red-500">*</span>
                    </label>
                    <select id="id_curso" 
                            name="id_curso" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                            required>
                        <option value="">Selecione um curso</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso['id'] }}" {{ old('id_curso', $organizadorData['id_curso']) == $curso['id'] ? 'selected' : '' }}>
                                {{ $curso['nome'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Alteração de Senha -->
            <div class="mb-6 md:mb-8">
                <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4 text-gray-800 border-b pb-2">Alteração de Senha</h2>
                <p class="text-xs md:text-sm text-gray-600 mb-4">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Deixe em branco se não deseja alterar sua senha.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                    <div>
                        <label for="senha_atual" class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                        <input type="password" 
                               id="senha_atual" 
                               name="senha_atual" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="senha_nova" class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                        <input type="password" 
                               id="senha_nova" 
                               name="senha_nova" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="Mínimo 8 caracteres">
                    </div>

                    <div>
                        <label for="senha_nova_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                        <input type="password" 
                               id="senha_nova_confirmation" 
                               name="senha_nova_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 md:pt-6 border-t border-gray-200">
                <a href="{{ route('organizador.dashboard') }}" 
                   class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors font-medium text-center order-2 sm:order-1">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium flex items-center justify-center gap-2 order-1 sm:order-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

