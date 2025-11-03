@extends('layouts.app')

@section('title', 'Painel do Organizador - PesqHub')

@section('content')
<div class="container mx-auto px-4 lg:px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Painel do Organizador</h1>
        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
            Organizador
        </span>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') ?? $error }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Perfil do Organizador -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Meu Perfil</h2>

            @if(isset($professor))
                <div class="mb-4 p-4 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>Status:</strong> Perfil público ativo - visível para estudantes
                    </p>
                </div>

                <div class="space-y-3 mb-6">
                    <div>
                        <strong>Nome:</strong> {{ $professor['nome'] }}
                    </div>
                    <div>
                        <strong>Email:</strong> {{ $professor['email'] }}
                    </div>
                    <div>
                        <strong>Telefone:</strong> {{ $professor['telefone'] ?? 'Não informado' }}
                    </div>
                    <div>
                        <strong>Curso:</strong> {{ $professor['curso'] ?? 'Não informado' }}
                    </div>
                    <div>
                        <strong>Áreas de Interesse:</strong>
                        <div class="mt-1 flex flex-wrap gap-2">
                            @if(!empty($professor['areas_interesse']))
                                @foreach($professor['areas_interesse'] as $area)
                                    <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $area }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-500">Nenhuma área definida</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-4 p-4 bg-yellow-50 rounded-md">
                    <p class="text-sm text-yellow-800">
                        <strong>Perfil não encontrado:</strong> Complete seu perfil para aparecer na busca pública
                    </p>
                </div>
            @endif

            <button onclick="showEditProfileModal()" class="bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                {{ isset($professor) ? 'Editar Perfil' : 'Completar Perfil' }}
            </button>
        </div>

        <!-- Linhas de Pesquisa -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Linhas de Pesquisa Disponíveis</h2>

            @if(isset($linhasPesquisa) && count($linhasPesquisa) > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($linhasPesquisa as $linha)
                        <div class="p-3 border rounded-md hover:bg-gray-50">
                            <h4 class="font-semibold text-indigo-800">{{ $linha['nome'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $linha['descricao'] }}</p>
                            @if(isset($professor) && in_array($linha['id'], $professor['linhas_pesquisa_ids'] ?? []))
                                <span class="inline-block mt-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                    Associado
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Nenhuma linha de pesquisa disponível.</p>
            @endif
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-3xl font-bold text-indigo-600">
                {{ isset($professor) && !empty($professor['linhas_pesquisa_ids']) ? count($professor['linhas_pesquisa_ids']) : 0 }}
            </div>
            <div class="text-sm text-gray-600">Linhas de Pesquisa</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-3xl font-bold text-green-600">
                {{ isset($professor) ? (isset($professor['areas_interesse']) ? count($professor['areas_interesse']) : 0) : 0 }}
            </div>
            <div class="text-sm text-gray-600">Áreas de Interesse</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-3xl font-bold text-purple-600">
                {{ isset($professor) ? 'Ativo' : 'Inativo' }}
            </div>
            <div class="text-sm text-gray-600">Status do Perfil</div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Perfil -->
<div id="edit-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
        <button onclick="hideEditProfileModal()" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800 z-10">&times;</button>
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-6">{{ isset($professor) ? 'Editar' : 'Completar' }} Perfil</h2>

            <form method="POST" action="{{ route('organizador.profile.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                        <input type="text" name="nome" value="{{ $professor['nome'] ?? '' }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" name="telefone" value="{{ $professor['telefone'] ?? '' }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Curso</label>
                        <input type="text" name="curso" value="{{ $professor['curso'] ?? '' }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Áreas de Interesse (separadas por vírgula)</label>
                        <input type="text" name="areas_interesse"
                               value="{{ isset($professor['areas_interesse']) ? implode(', ', $professor['areas_interesse']) : '' }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                               placeholder="Inteligência Artificial, Machine Learning, etc.">
                    </div>

                    @if(isset($linhasPesquisa) && count($linhasPesquisa) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Linhas de Pesquisa</label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border rounded-md p-3">
                            @foreach($linhasPesquisa as $linha)
                                <label class="flex items-start space-x-2">
                                    <input type="checkbox" name="linhas_pesquisa_ids[]" value="{{ $linha['id'] }}"
                                           {{ isset($professor) && in_array($linha['id'], $professor['linhas_pesquisa_ids'] ?? []) ? 'checked' : '' }}
                                           class="mt-1">
                                    <div>
                                        <div class="font-medium">{{ $linha['nome'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $linha['descricao'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" onclick="hideEditProfileModal()"
                            class="px-4 py-2 text-gray-600 border rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        Salvar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showEditProfileModal() {
    document.getElementById('edit-profile-modal').classList.remove('hidden');
    document.getElementById('edit-profile-modal').classList.add('flex');
}

function hideEditProfileModal() {
    document.getElementById('edit-profile-modal').classList.add('hidden');
    document.getElementById('edit-profile-modal').classList.remove('flex');
}
</script>
@endpush
