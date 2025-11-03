@extends('layouts.app')

@section('title', 'Área do Estudante - PesqHub')

@section('content')
<div class="container mx-auto px-4 lg:px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Área do Estudante</h1>
        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
            Estudante
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

    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-6 rounded-lg mb-8">
        <h2 class="text-2xl font-bold mb-2">Bem-vindo, {{ $user['nome'] }}!</h2>
        <p class="text-indigo-100">Explore professores e linhas de pesquisa para encontrar seu orientador ideal.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-3xl font-bold text-indigo-600">
                {{ isset($professores) ? count($professores) : 0 }}
            </div>
            <div class="text-sm text-gray-600">Professores Disponíveis</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-3xl font-bold text-green-600">
                {{ isset($linhasPesquisa) ? count($linhasPesquisa) : 0 }}
            </div>
            <div class="text-sm text-gray-600">Linhas de Pesquisa</div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-3xl font-bold text-purple-600">
                {{ isset($professores) ? count(array_filter($professores, function($p) { return !empty($p['areas_interesse']); })) : 0 }}
            </div>
            <div class="text-sm text-gray-600">Com Áreas Definidas</div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Professores Recentes -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Professores em Destaque</h2>

            @if(isset($professores) && count($professores) > 0)
                <div class="space-y-4">
                    @foreach(array_slice($professores, 0, 3) as $professor)
                        <div class="border rounded-md p-4 hover:bg-gray-50 cursor-pointer" onclick="showProfessorModal({{ json_encode($professor) }})">
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-lg font-bold text-indigo-700">
                                    {{ substr($professor['nome'], 0, 1) }}
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-semibold text-indigo-800">{{ $professor['nome'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $professor['curso'] }}</p>
                                    @if(!empty($professor['areas_interesse']))
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach(array_slice($professor['areas_interesse'], 0, 2) as $area)
                                                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $area['nome'] }}</span>
                                            @endforeach
                                            @if(count($professor['areas_interesse']) > 2)
                                                <span class="text-xs text-gray-500">+{{ count($professor['areas_interesse']) - 2 }} mais</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}" class="text-indigo-600 hover:underline">Ver todos os professores</a>
                </div>
            @else
                <p class="text-gray-500">Nenhum professor disponível no momento.</p>
            @endif
        </div>

        <!-- Linhas de Pesquisa -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Linhas de Pesquisa</h2>

            @if(isset($linhasPesquisa) && count($linhasPesquisa) > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($linhasPesquisa as $linha)
                        <div class="p-3 border rounded-md hover:bg-gray-50">
                            <h4 class="font-semibold text-indigo-800">{{ $linha['nome'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $linha['descricao'] }}</p>
                            @php
                                $professoresAssociados = array_filter($professores ?? [], function($p) use ($linha) {
                                    return in_array($linha['id'], $p['linhas_pesquisa_ids'] ?? []);
                                });
                            @endphp
                            @if(count($professoresAssociados) > 0)
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ count($professoresAssociados) }} professor(es) associado(s)
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}#linhas" class="text-indigo-600 hover:underline">Explorar todas as linhas</a>
                </div>
            @else
                <p class="text-gray-500">Nenhuma linha de pesquisa disponível.</p>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-2xl font-bold mb-4">Ações Rápidas</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                🔍 Buscar Professores
            </a>
            <a href="{{ route('home') }}#linhas" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                📚 Explorar Linhas de Pesquisa
            </a>
            <button onclick="showContactModal()" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                📧 Contato Geral
            </button>
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Professor -->
<div id="professor-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
        <button onclick="hideProfessorModal()" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800 z-10">&times;</button>
        <div id="professor-modal-content" class="p-8">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Modal de Contato -->
<div id="contact-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg relative">
        <button onclick="hideContactModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-4">Contato</h2>
            <form onsubmit="sendContact(event)">
                <div class="space-y-4">
                    <input type="text" name="nome" placeholder="Seu Nome" class="w-full px-3 py-2 border rounded-md" required>
                    <input type="email" name="email" placeholder="Seu E-mail" class="w-full px-3 py-2 border rounded-md" required>
                    <input type="text" name="assunto" placeholder="Assunto" class="w-full px-3 py-2 border rounded-md" required>
                    <textarea name="mensagem" placeholder="Digite sua mensagem (mínimo 5 caracteres)" rows="4" class="w-full px-3 py-2 border rounded-md" required></textarea>
                </div>
                <button type="submit" class="w-full mt-6 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Enviar Mensagem
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showProfessorModal(professor) {
    const linhasPesquisa = @json($linhasPesquisa ?? []);
    const professorLinhas = linhasPesquisa.filter(linha => 
        organizador.linhas_pesquisa_ids && organizador.linhas_pesquisa_ids.includes(linha.id)
    );

    const content = `
        <div class="flex items-start space-x-4 mb-6">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center text-3xl font-bold text-indigo-700">
                ${organizador.nome.charAt(0)}
            </div>
            <div class="flex-grow">
                <h2 class="text-3xl font-bold">${organizador.nome}</h2>
                <p class="text-xl text-gray-600">${organizador.curso}</p>
                <p class="text-gray-500 mt-1">📞 ${organizador.telefone || 'Não informado'}</p>
            </div>
        </div>
        
        ${organizador.areas_interesse && organizador.areas_interesse.length > 0 ? `
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Áreas de Interesse</h3>
                <div class="flex flex-wrap gap-2">
                    ${organizador.areas_interesse.map(area => 
                        `<span class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded-full">${area}</span>`
                    ).join('')}
                </div>
            </div>
        ` : ''}

        ${professorLinhas.length > 0 ? `
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Linhas de Pesquisa</h3>
                <div class="space-y-3">
                    ${professorLinhas.map(linha => `
                        <div class="bg-gray-50 p-3 rounded-md">
                            <h4 class="font-semibold">${linha.nome}</h4>
                            <p class="text-sm text-gray-600 mt-1">${linha.descricao}</p>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : ''}

        <div class="flex justify-end">
            <button onclick="showContactProfessorModal('${organizador.nome}')" 
                    class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                Entrar em Contato
            </button>
        </div>
    `;

    document.getElementById('organizador-modal-content').innerHTML = content;
    document.getElementById('organizador-modal').classList.remove('hidden');
    document.getElementById('organizador-modal').classList.add('flex');
}

function hideProfessorModal() {
    document.getElementById('organizador-modal').classList.add('hidden');
    document.getElementById('organizador-modal').classList.remove('flex');
}

function showContactModal() {
    document.getElementById('contact-modal').classList.remove('hidden');
    document.getElementById('contact-modal').classList.add('flex');
}

function hideContactModal() {
    document.getElementById('contact-modal').classList.add('hidden');
    document.getElementById('contact-modal').classList.remove('flex');
}

let currentProfessorData = null;

function showContactProfessorModal(professorName) {
    hideProfessorModal();

    // Buscar dados completos do organizador
    const professores = @json($professores ?? []);
    currentProfessorData = professores.find(p => p.nome === professorName);

    // Customize the contact modal for the specific organizador
    const form = document.querySelector('#contact-modal form');
    const assuntoField = form.querySelector('input[name="assunto"]');
    assuntoField.value = `Contato com ${professorName}`;
    showContactModal();
}

async function sendContact(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    // Validação frontend para mensagem
    const mensagem = formData.get('mensagem');
    if (mensagem.length < 5) {
        alert('A mensagem deve ter pelo menos 5 caracteres.');
        return;
    }

    // Determinar se é contato específico com organizador ou geral
    const isContactProfessor = currentProfessorData !== null;

    try {
        let response;

        if (isContactProfessor) {
            // Usar rota específica para contato com organizador
            response = await fetch('/contact-professor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email_professor: currentProfessorData.email,
                    nome_professor: currentProfessorData.nome,
                    nome_estudante: formData.get('nome'),
                    email_estudante: formData.get('email'),
                    mensagem: formData.get('mensagem'),
                    assunto: formData.get('assunto')
                })
            });
        } else {
            // Usar rota genérica para contato geral
            response = await fetch('/send-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to: 'admin@example.com', // Email do administrador
                    subject: formData.get('assunto'),
                    message: `Nome: ${formData.get('nome')}\nEmail: ${formData.get('email')}\n\nMensagem:\n${formData.get('mensagem')}`,
                    from_name: formData.get('nome'),
                    from_email: formData.get('email')
                })
            });
        }

        const result = await response.json();

        if (result.success) {
            alert('E-mail enviado com sucesso!');
            hideContactModal();
            form.reset();
            currentProfessorData = null;
        } else {
            alert('Erro ao enviar e-mail: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao enviar e-mail:', error);
        alert('Erro de conexão. Tente novamente.');
    }
}
</script>
@endpush
