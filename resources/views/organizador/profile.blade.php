@extends('layouts.app')

@section('title', 'Meu Perfil - Organizador')

@section('content')
<div class="container mx-auto px-4 lg:px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Meu Perfil</h1>
        <a href="{{ route('organizador.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
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
            {{ session('error') }}
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
        <form method="POST" action="{{ route('organizador.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações Básicas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome', $professorCompleto['nome']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $professorCompleto['email']) }}" 
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
                        <label for="id_curso" class="block text-sm font-medium text-gray-700 mb-2">Curso *</label>
                        <select id="id_curso" name="id_curso" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Selecione um curso</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso['id'] }}" {{ old('id_curso', $professorCompleto['id_curso']) == $curso['id'] ? 'selected' : '' }}>
                                    {{ $curso['nome'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 mb-2">Departamento</label>
                        <input type="text" id="departamento" name="departamento" value="{{ old('departamento', $professorCompleto['departamento'] ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Informações de Contato -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações de Contato</h2>
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" value="{{ old('telefone', $professorCompleto['telefone'] ?? '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="(00) 00000-0000">
                </div>
            </div>

            <!-- Alteração de Senha (Opcional) -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Alteração de Senha (Opcional)</h2>
                <p class="text-sm text-gray-600 mb-4">Deixe em branco se não deseja alterar sua senha.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="senha_atual" class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Digite sua senha atual">
                    </div>

                    <div>
                        <label for="senha_nova" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                        <input type="password" id="senha_nova" name="senha_nova" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Digite sua nova senha">
                    </div>

                    <div>
                        <label for="senha_nova_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                        <input type="password" id="senha_nova_confirmation" name="senha_nova_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Confirme sua nova senha">
                    </div>
                </div>
            </div>

            <!-- Áreas de Interesse -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Áreas de Interesse</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($areas as $area)
                        <label class="flex items-center space-x-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="areas_interesse_ids[]" value="{{ $area['id'] }}" 
                                   {{ in_array($area['id'], old('areas_interesse_ids', $professorCompleto['areas_interesse_ids'] ?? [])) ? 'checked' : '' }}
                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm">{{ $area['nome'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Linhas de Pesquisa -->
            <div class="pb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Linhas de Pesquisa</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($linhas as $linha)
                        <label class="flex items-center space-x-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="linhas_pesquisa_ids[]" value="{{ $linha['id'] }}" 
                                   {{ in_array($linha['id'], old('linhas_pesquisa_ids', $professorCompleto['linhas_pesquisa_ids'] ?? [])) ? 'checked' : '' }}
                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm">{{ $linha['nome'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('organizador.dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
