@extends('layouts.app')

@section('title', 'PesqHub - Encontre seu Orientador')

@section('content')
<div id="main-view" class="view active">
    <div class="container mx-auto px-4 lg:px-6 py-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold">Encontre seu Orientador</h1>
            <p class="text-lg text-gray-600 mt-2">Explore as linhas de pesquisa e conecte-se com professores da universidade.</p>
        </div>
        
        <div class="mb-8 border-b border-gray-200">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button id="tab-professores" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-indigo-600 hover:border-indigo-300 active">Buscar Professores</button>
                <button id="tab-linhas" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-indigo-600 hover:border-indigo-300">Consultar Linhas de Pesquisa</button>
            </nav>
        </div>

        <div id="search-content-professores" class="search-panel active">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Filters -->
                <aside class="w-full md:w-1/4 lg:w-1/5">
                    <div class="bg-white p-4 rounded-lg shadow-sm sticky top-24">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Filtros</h2>
                        <div id="filters-container" class="space-y-4">
                            <div>
                                <label for="search-filter" class="font-semibold block mb-2 text-sm">Nome do Professor</label>
                                <input type="text" id="search-filter" placeholder="Buscar por nome..." class="w-full px-3 py-2 border rounded-md text-sm">
                            </div>
                            <div>
                                <label for="course-filter" class="font-semibold block mb-2 text-sm">Curso</label>
                                <select id="course-filter" class="w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="">Todos os Cursos</option>
                                </select>
                            </div>
                            <div>
                                <label for="interest-filter" class="font-semibold block mb-2 text-sm">Área de Interesse</label>
                                <select id="interest-filter" class="w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="">Todas as Áreas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Professor List -->
                <div id="professor-list-container" class="w-full md:w-3/4 lg:w-4/5">
                    <div id="loading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <p class="mt-2 text-gray-600">Carregando professores...</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="search-content-linhas" class="search-panel hidden">
            <div>
                <input type="text" id="search-linhas-filter" placeholder="Buscar por nome da linha de pesquisa..." class="w-full max-w-lg mx-auto mb-8 px-4 py-2 border rounded-full text-sm">
                <div id="linhas-list-container" class="space-y-4">
                    <div id="loading-linhas" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <p class="mt-2 text-gray-600">Carregando linhas de pesquisa...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Professor Profile Modal -->
<div id="professor-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative">
        <button id="close-profile-modal" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800 z-10">&times;</button>
        <div id="professor-profile-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                const response = await fetch('/api/data');
                const data = await response.json();
                this.data.professores = data.professores || [];
                this.data.linhasPesquisa = data.linhas_pesquisa || [];
                this.data.filteredProfessores = this.data.professores;
                this.data.filteredLinhas = this.data.linhasPesquisa;
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
                document.getElementById('loading').innerHTML = '<p class="text-red-600">Erro ao carregar dados. Tente novamente.</p>';
            }
        },

        setupEventListeners() {
            // Tab switching
            document.getElementById('tab-professores').addEventListener('click', () => this.showTab('professores'));
            document.getElementById('tab-linhas').addEventListener('click', () => this.showTab('linhas'));

            // Search filters
            document.getElementById('search-filter').addEventListener('input', () => this.filterProfessores());
            document.getElementById('course-filter').addEventListener('change', () => this.filterProfessores());
            document.getElementById('interest-filter').addEventListener('change', () => this.filterProfessores());
            document.getElementById('search-linhas-filter').addEventListener('input', () => this.filterLinhas());

            // Professor profile modal
            document.getElementById('close-profile-modal').addEventListener('click', () => this.closeProfileModal());
            document.getElementById('professor-list-container').addEventListener('click', (e) => {
                const card = e.target.closest('.professor-card');
                if (card) {
                    this.showProfessorProfile(parseInt(card.dataset.id));
                }
            });

            // Login modal
            document.getElementById('login-trigger-btn')?.addEventListener('click', () => this.showLoginModal());
        },

        showTab(tab) {
            document.querySelectorAll('.search-panel').forEach(panel => panel.classList.add('hidden'));
            document.getElementById(`search-content-${tab}`).classList.remove('hidden');
            
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');
        },

        renderFilters() {
            const courses = [...new Set(this.data.professores.map(p => p.curso))];
            const interests = [...new Set(this.data.professores.flatMap(p => p.areas_interesse || []))];

            const courseSelect = document.getElementById('course-filter');
            courseSelect.innerHTML = '<option value="">Todos os Cursos</option>';
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course;
                option.textContent = course;
                courseSelect.appendChild(option);
            });

            const interestSelect = document.getElementById('interest-filter');
            interestSelect.innerHTML = '<option value="">Todas as Áreas</option>';
            interests.forEach(interest => {
                const option = document.createElement('option');
                option.value = interest;
                option.textContent = interest;
                interestSelect.appendChild(option);
            });
        },

        filterProfessores() {
            const searchTerm = document.getElementById('search-filter').value.toLowerCase();
            const selectedCourse = document.getElementById('course-filter').value;
            const selectedInterest = document.getElementById('interest-filter').value;

            this.data.filteredProfessores = this.data.professores.filter(professor => {
                const matchesSearch = professor.nome.toLowerCase().includes(searchTerm);
                const matchesCourse = !selectedCourse || professor.curso === selectedCourse;
                const matchesInterest = !selectedInterest || (professor.areas_interesse && professor.areas_interesse.includes(selectedInterest));
                
                return matchesSearch && matchesCourse && matchesInterest;
            });

            this.renderProfessores();
        },

        filterLinhas() {
            const searchTerm = document.getElementById('search-linhas-filter').value.toLowerCase();
            this.data.filteredLinhas = this.data.linhasPesquisa.filter(linha => 
                linha.nome.toLowerCase().includes(searchTerm)
            );
            this.renderLinhasPesquisa();
        },

        renderProfessores() {
            const container = document.getElementById('professor-list-container');
            document.getElementById('loading')?.remove();

            if (this.data.filteredProfessores.length === 0) {
                container.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Nenhum professor encontrado para os filtros selecionados.</div>';
                return;
            }

            container.innerHTML = this.data.filteredProfessores.map(professor => `
                <div class="bg-white p-4 rounded-lg shadow-sm mb-4 flex items-start space-x-4 hover:shadow-md transition-shadow cursor-pointer professor-card" data-id="${professor.id}">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex-shrink-0 flex items-center justify-center text-2xl font-bold text-indigo-700">
                        ${this.getInitials(professor.nome)}
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-bold text-indigo-800">${professor.nome}</h3>
                        <p class="text-sm text-gray-600">${professor.curso}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            ${(professor.areas_interesse || []).map(area => 
                                `<span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2 py-1 rounded-full">${area}</span>`
                            ).join('')}
                        </div>
                    </div>
                </div>
            `).join('');
        },

        renderLinhasPesquisa() {
            const container = document.getElementById('linhas-list-container');
            document.getElementById('loading-linhas')?.remove();

            if (this.data.filteredLinhas.length === 0) {
                container.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Nenhuma linha de pesquisa encontrada.</div>';
                return;
            }

            container.innerHTML = this.data.filteredLinhas.map(linha => {
                const professoresAssociados = this.data.professores
                    .filter(p => p.linhas_pesquisa_ids && p.linhas_pesquisa_ids.includes(linha.id))
                    .map(p => `<span class="text-sm text-indigo-600 font-medium">${p.nome}</span>`)
                    .join(', ');

                return `
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <h3 class="text-lg font-bold text-indigo-800">${linha.nome}</h3>
                        <p class="text-sm text-gray-600 mt-1">${linha.descricao}</p>
                        <div class="mt-3 border-t pt-2">
                            <p class="text-xs font-semibold text-gray-500">Professores associados:</p>
                            <p class="text-sm text-gray-800 mt-1">${professoresAssociados || 'Nenhum professor associado no momento.'}</p>
                        </div>
                    </div>
                `;
            }).join('');
        },

        async showProfessorProfile(professorId) {
            const professor = this.data.professores.find(p => p.id === professorId);
            if (!professor) return;

            const linhasProfessor = this.data.linhasPesquisa.filter(linha => 
                professor.linhas_pesquisa_ids && professor.linhas_pesquisa_ids.includes(linha.id)
            );

            const content = `
                <div class="p-8">
                    <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-8">
                        <div class="w-32 h-32 bg-indigo-100 rounded-full flex-shrink-0 flex items-center justify-center text-5xl font-bold text-indigo-700">
                            ${this.getInitials(professor.nome)}
                        </div>
                        <div class="flex-grow">
                            <h1 class="text-4xl font-bold">${professor.nome}</h1>
                            <p class="text-xl text-gray-600 mt-1">${professor.curso}</p>
                            <div class="mt-4 text-gray-700 space-y-1">
                                <p><strong>E-mail:</strong> <span class="text-gray-500">protegido (use o formulário de contato)</span></p>
                                <p><strong>Telefone:</strong> ${professor.telefone || 'Não informado'}</p>
                            </div>
                            <button onclick="App.showContactModal('${professor.nome}')" class="mt-6 bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Entrar em Contato
                            </button>
                        </div>
                    </div>
                    <div class="mt-10 border-t pt-6">
                        <h2 class="text-2xl font-bold mb-4">Linhas de Pesquisa</h2>
                        <div class="space-y-4">
                            ${linhasProfessor.map(linha => `
                                <div class="bg-gray-50 p-4 rounded-md border">
                                    <h4 class="font-semibold">${linha.nome}</h4>
                                    <p class="text-sm text-gray-600 mt-1">${linha.descricao}</p>
                                </div>
                            `).join('') || '<p class="text-gray-500">Nenhuma linha de pesquisa associada.</p>'}
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('professor-profile-content').innerHTML = content;
            document.getElementById('professor-profile-modal').classList.remove('hidden');
            document.getElementById('professor-profile-modal').classList.add('flex');
        },

        closeProfileModal() {
            document.getElementById('professor-profile-modal').classList.add('hidden');
            document.getElementById('professor-profile-modal').classList.remove('flex');
        },

        showContactModal(professorName) {
            // Encontrar dados completos do professor
            const professor = this.data.professores.find(p => p.nome === professorName);
            
            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-4">Entrar em Contato com ${professorName}</h2>
                    <form onsubmit="App.sendContact(event)" data-professor-email="${professor.email}" data-professor-name="${professorName}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="nome" placeholder="Seu Nome" class="w-full px-3 py-2 border rounded-md" required>
                            <input type="email" name="email" placeholder="Seu E-mail" class="w-full px-3 py-2 border rounded-md" required>
                        </div>
                        <input type="text" name="assunto" placeholder="Assunto" class="w-full mt-4 px-3 py-2 border rounded-md" required>
                        <textarea name="mensagem" placeholder="Digite sua mensagem (mínimo 5 caracteres)" rows="5" class="w-full mt-4 px-3 py-2 border rounded-md" required></textarea>
                        <button type="submit" class="w-full mt-6 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Enviar Mensagem
                        </button>
                    </form>
                </div>
            `;
            this.showGenericModal(content);
        },

        showLoginModal() {
            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center mb-2">Login de Administrador</h2>
                    <p class="text-center text-sm text-gray-500 mb-6">Digite suas credenciais para acessar o painel.</p>
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <input type="email" name="email" placeholder="E-mail" class="w-full px-3 py-2 border rounded-md" required>
                            <input type="password" name="password" placeholder="Senha" class="w-full px-3 py-2 border rounded-md" required>
                        </div>
                        <button type="submit" class="w-full mt-6 bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Entrar
                        </button>
                    </form>
                </div>
            `;
            this.showGenericModal(content);
        },

        showGenericModal(content) {
            document.getElementById('generic-modal-content').innerHTML = content;
            document.getElementById('generic-modal').classList.remove('hidden');
            document.getElementById('generic-modal').classList.add('flex');
        },

        hideGenericModal() {
            document.getElementById('generic-modal').classList.add('hidden');
            document.getElementById('generic-modal').classList.remove('flex');
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
            return name.match(/\b(\w)/g).join('');
        }
    };

    // Make App globally available
    window.App = App;
    App.init();
});
</script>
@endpush
