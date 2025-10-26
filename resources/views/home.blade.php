@extends('layouts.app')

@section('title', 'PesqHub - Encontre seu Orientador')

@section('content')
<div id="main-view" class="view active">
    <div class="container mx-auto px-4 lg:px-6 py-8">
        <div class="text-center mb-10 animate-fade-in">
            <h1 class="text-4xl font-bold text-indigo-700">Encontre seu Orientador</h1>
            <p class="text-lg text-gray-600 mt-2">Explore as linhas de pesquisa e conecte-se com professores da universidade.</p>
        </div>

        <!-- Tabs -->
        <div class="mb-8 border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 justify-center" aria-label="Tabs">
                <button id="tab-professores" class="tab-button whitespace-nowrap py-4 px-3 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500 transition-all duration-300">Buscar Professores</button>
                <button id="tab-linhas" class="tab-button whitespace-nowrap py-4 px-3 border-b-2 font-medium text-sm text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition-all duration-300">Consultar Linhas de Pesquisa</button>
            </nav>
        </div>

        <!-- Painel Professores -->
        <div id="search-content-professores" class="search-panel active">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Filtros -->
                <aside class="w-full md:w-1/4 lg:w-1/5 animate-fade-in">
                    <div class="bg-white p-5 rounded-xl shadow-sm sticky top-24">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-indigo-700">Filtros</h2>
                        <div id="filters-container" class="space-y-4">
                            <div>
                                <label for="search-filter" class="font-semibold block mb-2 text-sm">Nome do Professor</label>
                                <input type="text" id="search-filter" placeholder="Buscar por nome..." class="w-full px-3 py-2 border rounded-md text-sm focus:ring-2 focus:ring-indigo-400 transition-all">
                            </div>
                            <div>
                                <label for="course-filter" class="font-semibold block mb-2 text-sm">Curso</label>
                                <select id="course-filter" class="w-full px-3 py-2 border rounded-md text-sm focus:ring-2 focus:ring-indigo-400 transition-all">
                                    <option value="">Todos os Cursos</option>
                                </select>
                            </div>
                            <div>
                                <label for="interest-filter" class="font-semibold block mb-2 text-sm">Área de Interesse</label>
                                <select id="interest-filter" class="w-full px-3 py-2 border rounded-md text-sm focus:ring-2 focus:ring-indigo-400 transition-all">
                                    <option value="">Todas as Áreas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Lista Professores -->
                <div id="professor-list-container" class="w-full md:w-3/4 lg:w-4/5 animate-fade-in">
                    <div id="loading" class="text-center py-8 text-gray-600">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <p class="mt-2">Carregando professores...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel Linhas de Pesquisa -->
        <div id="search-content-linhas" class="search-panel hidden animate-fade-in">
            <input type="text" id="search-linhas-filter" placeholder="Buscar por nome da linha de pesquisa..." class="w-full max-w-lg mx-auto mb-8 px-4 py-2 border rounded-full text-sm focus:ring-2 focus:ring-indigo-400 transition-all">
            <div id="linhas-list-container" class="space-y-4">
                <div id="loading-linhas" class="text-center py-8 text-gray-600">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p class="mt-2">Carregando linhas de pesquisa...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Perfil Professor -->
<div id="professor-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative transform transition-all scale-95">
        <button id="close-profile-modal" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800 transition-all" aria-label="Fechar modal">&times;</button>
        <div id="professor-profile-content"></div>
    </div>
</div>

<!-- Modal Genérico -->
<div id="generic-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="generic-modal-content" class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-y-auto relative"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const App = {
        data: {
            professores: [],
            linhasPesquisa: [],
            filteredProfessores: [],
            filteredLinhas: []
        },

        async init() {
            await this.loadData();
            this.setupEventListeners();
            this.renderFilters();
            this.renderProfessores();
            this.renderLinhasPesquisa();
        },

        async loadData() {
            try {
                const res = await fetch('/api/data');
                const data = await res.json();
                Object.assign(this.data, {
                    professores: data.professores || [],
                    linhasPesquisa: data.linhas_pesquisa || [],
                    filteredProfessores: data.professores || [],
                    filteredLinhas: data.linhas_pesquisa || []
                });
            } catch (error) {
                console.error(error);
                document.getElementById('loading').innerHTML = '<p class="text-red-600">Erro ao carregar dados. Tente novamente.</p>';
            }
        },

        setupEventListeners() {
            // Tabs
            ['professores', 'linhas'].forEach(tab => {
                document.getElementById(`tab-${tab}`).addEventListener('click', () => this.showTab(tab));
            });

            // Filtros
            document.getElementById('search-filter').addEventListener('input', () => this.filterProfessores());
            document.getElementById('course-filter').addEventListener('change', () => this.filterProfessores());
            document.getElementById('interest-filter').addEventListener('change', () => this.filterProfessores());
            document.getElementById('search-linhas-filter').addEventListener('input', () => this.filterLinhas());

            // Modal perfil
            document.getElementById('close-profile-modal').addEventListener('click', () => this.closeProfileModal());
            document.getElementById('professor-list-container').addEventListener('click', e => {
                const card = e.target.closest('.professor-card');
                if (card) this.showProfessorProfile(parseInt(card.dataset.id));
            });
            // Login modal
            document.getElementById('login-trigger-btn')?.addEventListener('click', () => this.showLoginModal());

        },

        showTab(tab) {
            document.querySelectorAll('.search-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById(`search-content-${tab}`).classList.remove('hidden');
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('text-indigo-600', 'border-indigo-500'));
            document.getElementById(`tab-${tab}`).classList.add('text-indigo-600', 'border-indigo-500');
        },

        renderFilters() {
            const courses = [...new Set(this.data.professores.map(p => p.curso))].filter(Boolean);
            const interests = [...new Set(this.data.professores.flatMap(p => p.areas_interesse || []))].filter(Boolean);

            const fillSelect = (id, items, label) => {
                const el = document.getElementById(id);
                el.innerHTML = `<option value="">Todas ${label}</option>`;
                items.forEach(i => el.insertAdjacentHTML('beforeend', `<option value="${i}">${i}</option>`));
            };
            fillSelect('course-filter', courses, 'os Cursos');
            fillSelect('interest-filter', interests, 'as Áreas');
        },

        filterProfessores() {
            const search = document.getElementById('search-filter').value.toLowerCase();
            const curso = document.getElementById('course-filter').value;
            const interesse = document.getElementById('interest-filter').value;

            this.data.filteredProfessores = this.data.professores.filter(p =>
                p.nome.toLowerCase().includes(search) &&
                (!curso || p.curso === curso) &&
                (!interesse || (p.areas_interesse || []).includes(interesse))
            );
            this.renderProfessores();
        },

        filterLinhas() {
            const search = document.getElementById('search-linhas-filter').value.toLowerCase();
            this.data.filteredLinhas = this.data.linhasPesquisa.filter(l => l.nome.toLowerCase().includes(search));
            this.renderLinhasPesquisa();
        },

        renderProfessores() {
            const c = document.getElementById('professor-list-container');
            document.getElementById('loading')?.remove();

            if (!this.data.filteredProfessores.length) {
                c.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Nenhum professor encontrado.</div>';
                return;
            }

            c.innerHTML = this.data.filteredProfessores.map(p => `
                <div class="professor-card bg-white p-4 rounded-lg shadow-sm mb-4 flex items-start gap-4 hover:shadow-md hover:scale-[1.01] transition-all cursor-pointer" data-id="${p.id}">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center text-2xl font-bold text-indigo-700">${this.getInitials(p.nome)}</div>
                    <div>
                        <h3 class="text-lg font-bold text-indigo-800">${p.nome}</h3>
                        <p class="text-sm text-gray-600">${p.curso}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            ${(p.areas_interesse || []).map(a => `<span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full">${a}</span>`).join('')}
                        </div>
                    </div>
                </div>
            `).join('');
        },

        renderLinhasPesquisa() {
            const c = document.getElementById('linhas-list-container');
            document.getElementById('loading-linhas')?.remove();

            if (!this.data.filteredLinhas.length) {
                c.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Nenhuma linha de pesquisa encontrada.</div>';
                return;
            }

            c.innerHTML = this.data.filteredLinhas.map(l => {
                const profs = this.data.professores.filter(p => p.linhas_pesquisa_ids?.includes(l.id))
                    .map(p => `<span class="text-sm text-indigo-600 font-medium">${p.nome}</span>`).join(', ');
                return `
                    <div class="bg-white p-5 rounded-lg shadow-sm hover:shadow-md transition-all">
                        <h3 class="text-lg font-bold text-indigo-800">${l.nome}</h3>
                        <p class="text-sm text-gray-600 mt-1">${l.descricao}</p>
                        <div class="mt-3 border-t pt-2">
                            <p class="text-xs font-semibold text-gray-500">Professores associados:</p>
                            <p class="text-sm text-gray-800 mt-1">${profs || 'Nenhum professor associado.'}</p>
                        </div>
                    </div>`;
            }).join('');
        },

        async showProfessorProfile(id) {
            const p = this.data.professores.find(p => p.id === id);
            if (!p) return;

            const linhas = this.data.linhasPesquisa.filter(l => p.linhas_pesquisa_ids?.includes(l.id));
            const content = `
                <div class="p-8">
                    <div class="flex flex-col sm:flex-row gap-6 items-start">
                        <div class="w-32 h-32 bg-indigo-100 rounded-full flex items-center justify-center text-5xl font-bold text-indigo-700">${this.getInitials(p.nome)}</div>
                        <div>
                            <h1 class="text-4xl font-bold">${p.nome}</h1>
                            <p class="text-lg text-gray-600 mt-1">${p.curso}</p>
                            <p class="text-gray-500 mt-2"><strong>E-mail:</strong> protegido</p>
                            <p class="text-gray-500"><strong>Telefone:</strong> ${p.telefone || 'Não informado'}</p>
                            <button onclick="App.showContactModal('${p.nome}')" class="mt-6 bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition">Entrar em Contato</button>
                        </div>
                    </div>
                    <div class="mt-10 border-t pt-6">
                        <h2 class="text-2xl font-bold mb-4">Linhas de Pesquisa</h2>
                        ${linhas.length ? linhas.map(l => `
                            <div class="bg-gray-50 p-4 rounded-md border mb-3">
                                <h4 class="font-semibold">${l.nome}</h4>
                                <p class="text-sm text-gray-600 mt-1">${l.descricao}</p>
                            </div>`).join('') : '<p class="text-gray-500">Nenhuma linha associada.</p>'}
                    </div>
                </div>`;
            document.getElementById('professor-profile-content').innerHTML = content;
            this.toggleModal('professor-profile-modal', true);
        },

        toggleModal(id, show) {
            const m = document.getElementById(id);
            m.classList.toggle('hidden', !show);
            m.classList.toggle('flex', show);
        },

        closeProfileModal() {
            this.toggleModal('professor-profile-modal', false);
        },

        showContactModal(name) {
            const c = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800 transition-colors">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-4">Entrar em Contato com ${name}</h2>
                    <form onsubmit="App.sendContact(event)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="nome" placeholder="Seu Nome" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                            <input type="email" name="email" placeholder="Seu E-mail" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        </div>
                        <input type="text" name="assunto" placeholder="Assunto" class="w-full mt-4 px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        <textarea name="mensagem" placeholder="Mensagem" rows="5" class="w-full mt-4 px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required></textarea>
                        <button type="submit" class="w-full mt-6 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition">Enviar</button>
                    </form>
                </div>`;
            this.showGenericModal(c);
        },

        showLoginModal() {
            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800 transition-colors" aria-label="Fechar modal">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center mb-2">Login de Administrador</h2>
                    <p class="text-center text-sm text-gray-500 mb-6">Digite suas credenciais para acessar o painel.</p>
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="email" name="email" placeholder="E-mail" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        <input type="password" name="password" placeholder="Senha" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        <button type="submit" class="w-full mt-4 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-all">
                            Entrar
                        </button>
                    </form>
                </div>`;
            this.showGenericModal(content);
        },

        showGenericModal(content) {
            document.getElementById('generic-modal-content').innerHTML = content;
            this.toggleModal('generic-modal', true);
        },

        hideGenericModal() {
            this.toggleModal('generic-modal', false);
        },

        sendContact(e) {
            e.preventDefault();
            alert('Mensagem enviada com sucesso! (Simulação)');
            this.hideGenericModal();
        },

        getInitials(name) {
            return name.match(/\b(\w)/g).join('').slice(0, 2).toUpperCase();
        }
    };

    window.App = App;
    App.init();
});

// Microanimações Tailwind personalizadas
document.head.insertAdjacentHTML('beforeend', `
<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(10px);} to {opacity:1; transform:translateY(0);} }
.animate-fade-in { animation: fade-in .4s ease-in-out; }
</style>
`);
</script>
@endpush
