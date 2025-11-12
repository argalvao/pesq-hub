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
                <div id="organizador-list-container" class="w-full md:w-3/4 lg:w-4/5 animate-fade-in">
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
<div id="organizador-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative transform transition-all scale-95">
        <button id="close-profile-modal" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800 transition-all" aria-label="Fechar modal">&times;</button>
        <div id="organizador-profile-content"></div>
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
            document.getElementById('organizador-list-container').addEventListener('click', e => {
                const card = e.target.closest('.organizador-card');
                if (card) {
                    const id = card.dataset.id;
                    this.showProfessorProfile(id);
                }
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
            const interests = [...new Set(this.data.professores.flatMap(p => (p.areas_interesse || []).map(a => a.nome)))].filter(Boolean);

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
                (!interesse || (p.areas_interesse || []).some(a => a.nome === interesse))
            );
            this.renderProfessores();
        },

        filterLinhas() {
            const search = document.getElementById('search-linhas-filter').value.toLowerCase();
            this.data.filteredLinhas = this.data.linhasPesquisa.filter(l => l.nome.toLowerCase().includes(search));
            this.renderLinhasPesquisa();
        },

        renderProfessores() {
            const c = document.getElementById('organizador-list-container');
            document.getElementById('loading')?.remove();

            if (!this.data.filteredProfessores.length) {
                c.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Nenhum organizador encontrado.</div>';
                return;
            }

            try {
                c.innerHTML = this.data.filteredProfessores.map(p => `
                    <div class="organizador-card bg-white p-4 rounded-lg shadow-sm mb-4 flex items-start gap-4 hover:shadow-md hover:scale-[1.01] transition-all cursor-pointer" data-id="${p.id}">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center text-2xl font-bold text-indigo-700">${this.getInitials(p.nome)}</div>
                        <div>
                            <h3 class="text-lg font-bold text-indigo-800">${p.nome}</h3>
                            <p class="text-sm text-gray-600">${p.curso}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                ${(p.areas_interesse || []).map(a => `<span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full">${a.nome}</span>`).join('')}
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Erro ao renderizar professores:', error);
                c.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-red-500">Erro ao carregar professores.</div>';
            }
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
                            <p class="text-sm text-gray-800 mt-1">${profs || 'Nenhum organizador associado.'}</p>
                        </div>
                    </div>`;
            }).join('');
        },

        async showProfessorProfile(id) {
            const p = this.data.professores.find(p => p.id === id);
            if (!p) {
                console.error('Professor não encontrado com ID:', id);
                return;
            }

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
            document.getElementById('organizador-profile-content').innerHTML = content;
            this.toggleModal('organizador-profile-modal', true);
        },

        toggleModal(id, show) {
            const m = document.getElementById(id);
            m.classList.toggle('hidden', !show);
            m.classList.toggle('flex', show);
        },

        closeProfileModal() {
            this.toggleModal('organizador-profile-modal', false);
        },

        showContactModal(professorName) {
            // Encontrar dados completos do organizador
            const professor = this.data.professores.find(p => p.nome === professorName);

            if (!professor || !professor.email) {
                alert('Erro: Dados do organizador não encontrados ou e-mail não disponível.');
                return;
            }

            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-4">Entrar em Contato com ${professorName}</h2>
                    <form onsubmit="App.sendContact(event)" data-professor-email="${professor.email}" data-professor-name="${professorName}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="nome" placeholder="Seu Nome" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                            <input type="email" name="email" placeholder="Seu E-mail" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        </div>
                        <input type="text" name="assunto" placeholder="Assunto" class="w-full mt-4 px-3 py-2 border rounded-md" required>
                        <textarea name="mensagem" placeholder="Digite sua mensagem (mínimo 5 caracteres)" rows="5" class="w-full mt-4 px-3 py-2 border rounded-md" required></textarea>
                        <button type="submit" class="w-full mt-6 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Enviar Mensagem
                        </button>
                    </form>
                </div>`;
            this.showGenericModal(content);
        },

        showLoginModal() {
            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800 transition-colors" aria-label="Fechar modal">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center mb-2">Login de Administrador</h2>
                    <p class="text-center text-sm text-gray-500 mb-6">Digite suas credenciais para acessar o painel.</p>
                    
                    <!-- Alert container -->
                    <div id="login-alert" class="hidden mb-4 p-4 rounded-lg"></div>
                    
                    <form id="login-form" class="space-y-4">
                        <input type="email" id="login-email" name="email" placeholder="E-mail" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        <input type="password" id="login-password" name="password" placeholder="Senha" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        <div class="text-right mb-4">
                            <button type="button" onclick="App.showForgotPasswordModal()" class="text-sm text-indigo-600 hover:underline">
                                Esqueci minha senha
                            </button>
                        </div>
                        <button type="submit" id="login-submit-btn" class="w-full mt-4 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-all">
                            <span id="login-btn-text">Entrar</span>
                            <span id="login-btn-loading" class="hidden">
                                <svg class="inline w-4 h-4 mr-2 animate-spin" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Entrando...
                            </span>
                        </button>
                    </form>
                </div>`;
            this.showGenericModal(content);
            
            // Setup login form handler
            setTimeout(() => {
                this.setupLoginForm();
            }, 100);
        },

        setupLoginForm() {
            const form = document.getElementById('login-form');
            const alertContainer = document.getElementById('login-alert');
            const submitBtn = document.getElementById('login-submit-btn');
            const btnText = document.getElementById('login-btn-text');
            const btnLoading = document.getElementById('login-btn-loading');
            
            if (!form) return;
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                
                // Hide previous alert
                this.hideLoginAlert();
                
                // Show loading state
                this.setLoginLoading(true);
                
                try {
                    const response = await fetch('/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ email, password })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showLoginAlert('✅ Login realizado com sucesso! Redirecionando...', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        this.showLoginAlert('❌ ' + data.message, 'error');
                    }
                    
                } catch (error) {
                    console.error('Erro no login:', error);
                    this.showLoginAlert('❌ Erro de conexão. Tente novamente.', 'error');
                } finally {
                    this.setLoginLoading(false);
                }
            });
        },

        showLoginAlert(message, type) {
            const alertContainer = document.getElementById('login-alert');
            if (!alertContainer) return;
            
            alertContainer.className = `mb-4 p-4 rounded-lg ${
                type === 'success' 
                    ? 'bg-green-100 border border-green-400 text-green-700' 
                    : 'bg-red-100 border border-red-400 text-red-700'
            }`;
            alertContainer.textContent = message;
            alertContainer.classList.remove('hidden');
        },

        hideLoginAlert() {
            const alertContainer = document.getElementById('login-alert');
            if (alertContainer) {
                alertContainer.classList.add('hidden');
            }
        },

        setLoginLoading(loading) {
            const submitBtn = document.getElementById('login-submit-btn');
            const btnText = document.getElementById('login-btn-text');
            const btnLoading = document.getElementById('login-btn-loading');
            
            if (!submitBtn || !btnText || !btnLoading) return;
            
            if (loading) {
                submitBtn.disabled = true;
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
            } else {
                submitBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        },

        showForgotPasswordModal() {
            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800 transition-colors" aria-label="Fechar modal">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center mb-2">Recuperar Senha</h2>
                    <p class="text-center text-sm text-gray-500 mb-6">Digite seu e-mail para receber um código de verificação</p>
                    <form id="forgot-password-form" class="space-y-4">
                        <input type="email" id="forgot-email" placeholder="E-mail" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" required>
                        <button type="submit" class="w-full mt-4 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-all">
                            Enviar Código
                        </button>
                    </form>
                    <div class="text-center mt-4">
                        <button type="button" onclick="App.showLoginModal()" class="text-sm text-indigo-600 hover:underline">
                            Voltar ao Login
                        </button>
                    </div>
                </div>`;
            this.showGenericModal(content);

            // Adicionar evento de submit ao formulário
            document.getElementById('forgot-password-form').addEventListener('submit', (e) => this.handleForgotPassword(e));
        },

        async handleForgotPassword(event) {
            event.preventDefault();

            const email = document.getElementById('forgot-email').value;
            const submitBtn = event.target.querySelector('button[type="submit"]');

            // Desabilitar botão e mostrar loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            try {
                const response = await fetch('/api/password/send-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Código de verificação enviado! Verifique seu e-mail.');
                    this.showPasswordResetModal(email);
                } else {
                    alert(result.message || 'Erro ao enviar código. Tente novamente.');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro de conexão. Tente novamente.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Código';
            }
        },

        showPasswordResetModal(email) {
            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800 transition-colors" aria-label="Fechar modal">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center mb-2">Redefinir Senha</h2>
                    <p class="text-center text-sm text-gray-500 mb-6">Digite o código de 6 dígitos enviado para seu e-mail</p>
                    <form id="reset-password-form" class="space-y-4">
                        <input type="hidden" id="reset-email" value="${email}">
                        <div>
                            <label class="block text-sm font-medium mb-2">Código de Verificação</label>
                            <input type="text" id="reset-token" placeholder="000000" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400 text-center text-2xl tracking-widest" maxlength="6" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Nova Senha</label>
                            <input type="password" id="reset-password" placeholder="Nova senha" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" minlength="6" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Confirmar Senha</label>
                            <input type="password" id="reset-password-confirmation" placeholder="Confirme a nova senha" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-400" minlength="6" required>
                        </div>
                        <button type="submit" class="w-full mt-4 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-all">
                            Redefinir Senha
                        </button>
                    </form>
                    <div class="text-center mt-4">
                        <button type="button" onclick="App.showForgotPasswordModal()" class="text-sm text-indigo-600 hover:underline">
                            Reenviar Código
                        </button>
                    </div>
                </div>`;
            this.showGenericModal(content);

            // Adicionar evento de submit ao formulário
            document.getElementById('reset-password-form').addEventListener('submit', (e) => this.handlePasswordReset(e));

            // Formatação automática do token
            document.getElementById('reset-token').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
        },

        async handlePasswordReset(event) {
            event.preventDefault();

            console.log('handlePasswordReset iniciado');

            const email = document.getElementById('reset-email').value;
            const token = document.getElementById('reset-token').value;
            const password = document.getElementById('reset-password').value;
            const passwordConfirmation = document.getElementById('reset-password-confirmation').value;
            const submitBtn = event.target.querySelector('button[type="submit"]');

            console.log('Dados coletados:', { email, token, password: '***', passwordConfirmation: '***' });

            // Validações frontend
            if (token.length !== 6) {
                console.log('Token inválido - comprimento:', token.length);
                alert('O código deve ter 6 dígitos.');
                return;
            }

            if (password !== passwordConfirmation) {
                console.log('Senhas não conferem');
                alert('As senhas não conferem.');
                return;
            }

            if (password.length < 6) {
                console.log('Senha muito curta:', password.length);
                alert('A senha deve ter pelo menos 6 caracteres.');
                return;
            }

            console.log('Validações OK, enviando requisição...');

            // Desabilitar botão e mostrar loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Redefinindo...';

            try {
                const requestData = {
                    email: email,
                    token: token,
                    password: password,
                    password_confirmation: passwordConfirmation
                };

                console.log('Enviando para:', '/api/password/update');
                console.log('Request data:', { ...requestData, password: '***', password_confirmation: '***' });

                const response = await fetch('/api/password/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);

                // Verificar se é JSON antes de fazer parse
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);

                let result;
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                    console.log('Response JSON:', result);
                } else {
                    const text = await response.text();
                    console.log('Response TEXT (não é JSON):', text);
                    throw new Error('Resposta não é JSON válida. Server retornou: ' + text.substring(0, 200));
                }

                if (response.ok) {
                    alert('Senha redefinida com sucesso! Você pode fazer login com a nova senha.');
                    this.hideGenericModal();
                    this.showLoginModal();
                } else {
                    alert(result.message || 'Erro ao redefinir senha. Verifique os dados e tente novamente.');
                }
            } catch (error) {
                console.error('Erro detalhado:', error);
                console.error('Stack trace:', error.stack);
                console.error('Response status:', error.response?.status);
                console.error('Response text:', error.response?.text);
                alert('Erro de conexão. Tente novamente. Verifique o console para detalhes.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Redefinir Senha';
            }
        },

        showGenericModal(content) {
            document.getElementById('generic-modal-content').innerHTML = content;
            this.toggleModal('generic-modal', true);
        },

        hideGenericModal() {
            this.toggleModal('generic-modal', false);
        },

        async sendContact(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // Validação frontend para mensagem
            const mensagem = formData.get('mensagem');
            if (mensagem.length < 5) {
                alert('A mensagem deve ter pelo menos 5 caracteres.');
                return;
            }

            // Verificar se é contato com professor específico
            const professorEmail = form.dataset.professorEmail;
            const professorName = form.dataset.professorName;

            try {
                let response;

                if (professorEmail && professorName) {
                    // Contato específico com professor
                    response = await fetch('/contact-professor', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            email_professor: professorEmail,
                            nome_professor: professorName,
                            nome_estudante: formData.get('nome'),
                            email_estudante: formData.get('email'),
                            mensagem: formData.get('mensagem'),
                            assunto: formData.get('assunto')
                        })
                    });
                } else {
                    // Contato geral
                    response = await fetch('/send-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            to: 'admin@example.com',
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
                    this.hideGenericModal();
                } else {
                    alert('Erro ao enviar e-mail: ' + (result.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao enviar e-mail:', error);
                alert('Erro de conexão. Tente novamente.');
            }
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
