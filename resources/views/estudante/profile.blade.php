@extends('layouts.app')

@section('title', 'Meu Perfil - PesqHub')

@section('content')
@php
    // Garantir que as variáveis existam para evitar "Undefined variable"
    if (!isset($userCompleto)) {
        $sessionUser = Session::get('user', []);
        $userCompleto = array_merge([
            'nome' => '',
            'email' => '',
            'id_curso' => '',
            'periodo' => '',
            'telefone' => '',
            'lattes' => '',
            'biografia' => '',
            'areas_interesse_ids' => []
        ], is_array($sessionUser) ? $sessionUser : []);
    }
    $cursos = $cursos ?? [];
    $areas = $areas ?? [];
@endphp
<div class="container mx-auto px-4 lg:px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Meu Perfil</h1>
        <a href="{{ route('basico.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
            ← Voltar ao Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            @if(session('error'))
                {{ session('error') }}
            @endif
            @if($errors->any())
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('basico.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações Básicas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome', $userCompleto['nome']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $userCompleto['email']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                    </div>
                </div>
            </div>

            <!-- Informações Acadêmicas -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações Acadêmicas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_curso" class="block text-sm font-medium text-gray-700 mb-2">Curso</label>
                        <select id="id_curso" name="id_curso" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione um curso</option>
                            @if(isset($cursos))
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso['id'] }}" {{ old('id_curso', $userCompleto['id_curso']) == $curso['id'] ? 'selected' : '' }}>
                                        {{ $curso['nome'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="periodo" class="block text-sm font-medium text-gray-700 mb-2">Período/Semestre</label>
                        <select id="periodo" name="periodo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione o período</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('periodo', $userCompleto['periodo'] ?? '') == $i ? 'selected' : '' }}>
                                    {{ $i }}º Período
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Informações de Contato -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações de Contato</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" value="{{ old('telefone', $userCompleto['telefone'] ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="(00) 00000-0000">
                    </div>

                    <div>
                        <label for="lattes" class="block text-sm font-medium text-gray-700 mb-2">Currículo Lattes</label>
                        <input type="url" id="lattes" name="lattes" value="{{ old('lattes', $userCompleto['lattes'] ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="http://lattes.cnpq.br/...">
                    </div>
                </div>
            </div>

            <!-- Áreas de Interesse -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Áreas de Interesse</h2>
                <div>
                    <label for="areas_interesse" class="block text-sm font-medium text-gray-700 mb-2">Selecione suas áreas de interesse</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3">
                        @if(isset($areas))
                            @foreach($areas as $area)
                                <label class="flex items-center space-x-2 text-sm">
                                    <input type="checkbox" name="areas_interesse_ids[]" value="{{ $area['id'] }}" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                           {{ in_array($area['id'], old('areas_interesse_ids', $userCompleto['areas_interesse_ids'] ?? [])) ? 'checked' : '' }}>
                                    <span>{{ $area['nome'] }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Biografia/Sobre -->
            <div class="pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Sobre Mim</h2>
                <div>
                    <label for="biografia" class="block text-sm font-medium text-gray-700 mb-2">Biografia/Descrição</label>
                    <textarea id="biografia" name="biografia" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Conte um pouco sobre você, seus objetivos acadêmicos, experiências, etc.">{{ old('biografia', $userCompleto['biografia'] ?? '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Máximo 500 caracteres</p>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-between items-center pt-6">
                <a href="{{ route('basico.dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 7) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }
    e.target.value = value;
});

// Contador de caracteres para biografia
document.getElementById('biografia').addEventListener('input', function (e) {
    const maxLength = 500;
    const currentLength = e.target.value.length;
    
    if (currentLength > maxLength) {
        e.target.value = e.target.value.substring(0, maxLength);
    }
});
</script>
@endsection
