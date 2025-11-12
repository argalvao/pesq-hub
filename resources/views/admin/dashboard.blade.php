@extends('layouts.app')

@section('title', 'Dashboard Admin - PesqHub')

@section('content')
    <div class="container mx-auto px-4 lg:px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Painel do Administrador</h1>

        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $error }}
            </div>
        @endif

        <div class="flex flex-col md:flex-row gap-8">
            <aside class="w-full md:w-1/4 lg:w-1/5">
                <nav class="bg-white p-4 rounded-lg shadow-sm space-y-2 sticky top-24">
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md admin-panel-trigger active" data-panel="linhas">
                        Gerenciar Linhas de Pesquisa
                    </a>
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md admin-panel-trigger" data-panel="professores">
                        Gerenciar Professores
                    </a>
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md admin-panel-trigger" data-panel="usuarios">
                        Gerenciar Usuários
                    </a>
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md admin-panel-trigger" data-panel="areas">
                        Gerenciar Áreas de Pesquisa
                    </a>
                </nav>
            </aside>

            <div class="w-full md:w-3/4 lg:w-4/5">
                <div id="admin-content">
                    <div id="panel-linhas" class="admin-panel active">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-bold">Linhas de Pesquisa</h2>
                                <button id="add-linha-btn" class="bg-green-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-green-600 text-sm">
                                    Adicionar Nova
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table id="linhas-table" class="w-full text-left text-sm">
                                    <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-3 font-semibold">Nome</th>
                                        <th class="p-3 font-semibold">Descrição</th>
                                        <th class="p-3 font-semibold">Área de Pesquisa</th>
                                        <th class="p-3 font-semibold">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody id="linhas-tbody">
                                    <tr>
                                        <td colspan="4" class="p-3 text-center">
                                            <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                                            Carregando...
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="panel-professores" class="admin-panel hidden">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-bold">Professores</h2>
                                <button id="add-professor-btn" class="bg-green-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-green-600 text-sm">
                                    Adicionar Novo
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table id="professores-table" class="w-full text-left text-sm">
                                    <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-3 font-semibold">Nome</th>
                                        <th class="p-3 font-semibold">Curso</th>
                                        <th class="p-3 font-semibold">Email</th>
                                        <th class="p-3 font-semibold">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody id="professores-tbody">
                                    <tr>
                                        <td colspan="4" class="p-3 text-center">
                                            <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                                            Carregando...
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="panel-usuarios" class="admin-panel hidden">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-bold">Usuários</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table id="usuarios-table" class="w-full text-left text-sm">
                                    <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-3 font-semibold">Nome</th>
                                        <th class="p-3 font-semibold">Email</th>
                                        <th class="p-3 font-semibold">Curso</th>
                                        <th class="p-3 font-semibold">Status</th>
                                        <th class="p-3 font-semibold">Permissão</th>
                                        <th class="p-3 font-semibold">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody id="usuarios-tbody">
                                    <tr>
                                        <td colspan="6" class="p-3 text-center">
                                            <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                                            Carregando...
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="panel-areas" class="admin-panel hidden">
                        <x-manage-areas-panel base-path="/admin" />
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const AdminPanel = {
                data: {
                    linhasPesquisa: [],
                    professores: [],
                    cursos: [],
                    areasPesquisa: [],
                    usuarios: [], // NOVO DADO
                    currentPanel: 'linhas'
                },

                async init() {
                    this.setupEventListeners();
                    // Carregar todos os dados necessários
                    await Promise.all([
                        this.loadLinhasPesquisa(),
                        this.loadProfessores(),
                        this.loadCursos(),
                        this.loadAreasPesquisa(),
                        this.loadUsuarios() // NOVO LOAD
                    ]);
                },

                setupEventListeners() {
                    // Panel navigation
                    document.querySelectorAll('.admin-panel-trigger').forEach(trigger => {
                        trigger.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.showPanel(trigger.dataset.panel);
                        });
                    });

                    // Add buttons
                    document.getElementById('add-linha-btn').addEventListener('click', () => this.showLinhaModal());
                    document.getElementById('add-professor-btn').addEventListener('click', () => this.showProfessorModal());

                    // Table actions: Linhas
                    document.getElementById('linhas-tbody').addEventListener('click', (e) => {
                        if (e.target.classList.contains('edit-linha-btn')) {
                            this.showLinhaModal(e.target.dataset.id);
                        } else if (e.target.classList.contains('delete-linha-btn')) {
                            this.deleteLinha(e.target.dataset.id);
                        }
                    });

                    // Table actions: Professores
                    document.getElementById('professores-tbody').addEventListener('click', (e) => {
                        if (e.target.classList.contains('edit-professor-btn')) {
                            this.showProfessorModal(e.target.dataset.id);
                        } else if (e.target.classList.contains('delete-professor-btn')) {
                            this.deleteProfessor(e.target.dataset.id);
                        }
                    });

                    // NOVO EVENT LISTENER: Usuários
                    document.getElementById('usuarios-tbody').addEventListener('click', (e) => {
                        if (e.target.classList.contains('activate-user-btn')) {
                            this.ativarUsuario(e.target.dataset.id);
                        } else if (e.target.classList.contains('deactivate-user-btn')) {
                            this.desativarUsuario(e.target.dataset.id);
                        }
                    });
                },

                showPanel(panel) {
                    this.currentPanel = panel;

                    // Update sidebar
                    document.querySelectorAll('.sidebar-link').forEach(link => {
                        link.classList.toggle('active', link.dataset.panel === panel);
                    });

                    // Update panels
                    document.querySelectorAll('.admin-panel').forEach(panelEl => {
                        panelEl.classList.toggle('active', panelEl.id === `panel-${panel}`);
                        panelEl.classList.toggle('hidden', panelEl.id !== `panel-${panel}`);
                    });
                },

                // =============== FUNÇÕES DE CARREGAMENTO DE DADOS ===============

                async loadLinhasPesquisa() {
                    // ... (código existente - sem alteração)
                    try {
                        const response = await fetch('/admin/linhas-pesquisa');
                        const result = await response.json();

                        if (result.success) {
                            this.data.linhasPesquisa = result.data;
                            this.renderLinhasTable();
                        } else {
                            this.showError('Erro ao carregar linhas de pesquisa');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão ao carregar linhas');
                    }
                },

                async loadProfessores() {
                    // ... (código existente - sem alteração)
                    try {
                        const response = await fetch('/admin/professores');
                        const result = await response.json();

                        if (result.success) {
                            this.data.professores = result.data;
                            this.renderProfessoresTable();
                        } else {
                            this.showError('Erro ao carregar professores');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão ao carregar professores');
                    }
                },

                async loadCursos() {
                    // ... (código existente - sem alteração)
                    try {
                        const response = await fetch('/admin/cursos');
                        const result = await response.json();
                        if (result.success) {
                            this.data.cursos = result.data;
                        } else {
                            this.showError('Erro ao carregar cursos');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão ao carregar cursos');
                    }
                },

                async loadAreasPesquisa() {
                    // ... (código existente - sem alteração)
                    try {
                        const response = await fetch('/admin/areas-pesquisa');
                        const result = await response.json();
                        if (result.success) {
                            this.data.areasPesquisa = result.data;
                        } else {
                            this.showError('Erro ao carregar áreas de pesquisa');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão ao carregar áreas de pesquisa');
                    }
                },

                // NOVA FUNÇÃO: Carregar Usuários
                async loadUsuarios() {
                    try {
                        // !! ATENÇÃO: Esta rota /admin/usuarios precisa ser criada no seu web.php !!
                        const response = await fetch('/admin/usuarios');
                        const result = await response.json();

                        if (result.success) {
                            this.data.usuarios = result.data;
                            this.renderUsuariosTable();
                        } else {
                            this.showError('Erro ao carregar usuários. Verifique se a rota GET /admin/usuarios existe.');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão ao carregar usuários');
                    }
                },

                // =============== FUNÇÕES DE RENDERIZAÇÃO ===============

                renderLinhasTable() {
                    // ... (código existente - sem alteração)
                    const tbody = document.getElementById('linhas-tbody');

                    if (this.data.linhasPesquisa.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="p-3 text-center text-gray-500">Nenhuma linha de pesquisa cadastrada</td></tr>';
                        return;
                    }

                    tbody.innerHTML = this.data.linhasPesquisa.map(linha => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">${linha.nome}</td>
                    <td class="p-3 text-gray-600">${linha.descricao || ''}</td>
                    <td class="p-3 text-gray-600">${linha.area_pesquisa || 'N/A'}</td>
                    <td class="p-3 space-x-2">
                        <button data-id="${linha.id}" class="edit-linha-btn text-blue-600 hover:underline">Editar</button>
                        <button data-id="${linha.id}" class="delete-linha-btn text-red-600 hover:underline">Excluir</button>
                    </td>
                </tr>
            `).join('');
                },

                renderProfessoresTable() {
                    // ... (código existente - sem alteração)
                    const tbody = document.getElementById('professores-tbody');

                    if (this.data.professores.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="p-3 text-center text-gray-500">Nenhum professor cadastrado</td></tr>';
                        return;
                    }

                    tbody.innerHTML = this.data.professores.map(professor => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">${professor.nome}</td>
                    <td class="p-3 text-gray-600">${professor.curso || 'N/A'}</td>
                    <td class="p-3 text-gray-600">${professor.email}</td>
                    <td class="p-3 space-x-2">
                        <button data-id="${professor.id}" class="edit-professor-btn text-blue-600 hover:underline">Editar</button>
                        <button data-id="${professor.id}" class="delete-professor-btn text-red-600 hover:underline">Excluir</button>
                    </td>
                </tr>
            `).join('');
                },

                // NOVA FUNÇÃO: Renderizar Tabela de Usuários
                renderUsuariosTable() {
                    const tbody = document.getElementById('usuarios-tbody');

                    if (this.data.usuarios.length === 0) {
                        // ALTERAÇÃO AQUI (colspan)
                        tbody.innerHTML = '<tr><td colspan="6" class="p-3 text-center text-gray-500">Nenhum usuário cadastrado</td></tr>';
                        return;
                    }
                    
                    // ALTERAÇÃO AQUI (adicionada nova <td> para usuario.level)
                    tbody.innerHTML = this.data.usuarios.map(usuario => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">${usuario.nome}</td>
                    <td class="p-3 text-gray-600">${usuario.email}</td>
                    <td class="p-3 text-gray-600">${usuario.curso || 'N/A'}</td>
                    <td class="p-3">
                        ${usuario.ativo
                        ? '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Ativo</span>'
                        : '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Inativo</span>'
                    }
                    </td>
                    <td class="p-3 text-gray-600">${this.formatUserLevel(usuario.level)}</td>
                    <td class="p-3 space-x-2">
                        ${usuario.ativo
                        ? `<button data-id="${usuario.id}" class="deactivate-user-btn text-red-600 hover:underline">Desativar</button>`
                        : `<button data-id="${usuario.id}" class="activate-user-btn text-green-600 hover:underline">Ativar</button>`
                    }
                    </td>
                </tr>
            `).join('');
                },

                // NOVA FUNÇÃO AUXILIAR ADICIONADA
                formatUserLevel(level) {
                    if (!level) return 'N/A';
                    // Capitaliza a primeira letra (ex: "basico" -> "Basico", "organizador" -> "Organizador")
                    return level.charAt(0).toUpperCase() + level.slice(1);
                },

                // =============== MODAIS E SALVAMENTO ===============

                showLinhaModal(id = null) {
                    // ... (código existente - sem alteração)
                    const isEditing = id !== null;
                    const linha = isEditing
                        ? this.data.linhasPesquisa.find(l => l.id === id)
                        : { nome: '', descricao: '', id_area_pesquisa: null };

                    const areasOptions = this.data.areasPesquisa.map(area =>
                        `<option value="${area.id}" ${linha.id_area_pesquisa === area.id ? 'selected' : ''}>
                    ${area.nome}
                </option>`
                    ).join('');

                    const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                <form onsubmit="AdminPanel.saveLinha(event)" class="p-8" data-id="${isEditing ? linha.id : ''}">
                    <h2 class="text-2xl font-bold mb-4">${isEditing ? 'Editar' : 'Adicionar'} Linha de Pesquisa</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium block mb-1">Nome</label>
                            <input type="text" name="nome" class="w-full px-3 py-2 border rounded-md" value="${linha.nome}" required>
                        </div>
                        <div>
                            <label class="text-sm font-medium block mb-1">Descrição</label>
                            <textarea name="descricao" rows="4" class="w-full px-3 py-2 border rounded-md">${linha.descricao || ''}</textarea>
                        </div>
                        <div>
                            <label class="text-sm font-medium block mb-1">Área de Pesquisa</label>
                            <select name="id_area_pesquisa" class="w-full px-3 py-2 border rounded-md" required>
                                <option value="" disabled ${!linha.id_area_pesquisa ? 'selected' : ''}>Selecione uma área</option>
                                ${areasOptions}
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="App.hideGenericModal()" class="px-4 py-2 text-gray-600 border rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Salvar
                        </button>
                    </div>
                </form>
            `;
                    App.showGenericModal(content);
                },

                showProfessorModal(id = null) {
                    // ... (código existente - sem alteração)
                    const isEditing = id !== null;
                    const professor = isEditing
                        ? this.data.professores.find(p => p.id === id)
                        : { nome: '', email: '', telefone: '', id_curso: null, departamento: '', areas_interesse_ids: [], linhas_pesquisa_ids: [] };

                    const linhasOptions = this.data.linhasPesquisa.map(linha =>
                        `<option value="${linha.id}" ${professor.linhas_pesquisa_ids.includes(linha.id) ? 'selected' : ''}>${linha.nome}</option>`
                    ).join('');

                    const cursosOptions = this.data.cursos.map(curso =>
                        `<option value="${curso.id}" ${professor.id_curso === curso.id ? 'selected' : ''}>${curso.nome}</option>`
                    ).join('');

                    const areasCheckboxes = this.data.areasPesquisa.map(area => `
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="areas_interesse_ids" value="${area.id}" 
                                   ${professor.areas_interesse_ids.includes(area.id) ? 'checked' : ''}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm">${area.nome}</span>
                        </label>
                    `).join('');

                    const linhasCheckboxes = this.data.linhasPesquisa.map(linha => `
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="linhas_pesquisa_ids" value="${linha.id}" 
                                   ${professor.linhas_pesquisa_ids.includes(linha.id) ? 'checked' : ''}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm">${linha.nome}</span>
                        </label>
                    `).join('');

                    const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                <form onsubmit="AdminPanel.saveProfessor(event)" class="p-8" data-id="${isEditing ? professor.id : ''}">
                    <h2 class="text-2xl font-bold mb-6">${isEditing ? 'Editar' : 'Adicionar'} Professor</h2>
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                        <input type="text" name="nome" placeholder="Nome Completo" class="w-full px-3 py-2 border rounded-md" value="${professor.nome}" required>
                        
                        <div>
                            <div class="relative">
                                <input type="email" id="professor-email" name="email" placeholder="E-mail" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${professor.email}" required>
                                <div id="professor-email-spinner" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                                </div>
                            </div>
                            <div id="professor-email-feedback" class="mt-1 text-sm hidden"></div>
                        </div>
                        
                        <div>
                            <div class="relative">
                                <input type="tel" id="professor-telefone" name="telefone" placeholder="Telefone (ex: (11) 99999-9999)" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${professor.telefone || ''}">
                            </div>
                            <div id="professor-telefone-feedback" class="mt-1 text-sm hidden"></div>
                        </div>

                        <select name="id_curso" class="w-full px-3 py-2 border rounded-md" required>
                            <option value="" disabled ${!professor.id_curso ? 'selected' : ''}>Selecione um curso</option>
                            ${cursosOptions}
                        </select>

                        <input type="text" name="departamento" placeholder="Departamento" class="w-full px-3 py-2 border rounded-md" value="${professor.departamento || ''}">

                        <div>
                            <label class="text-sm font-medium block mb-2">Áreas de Interesse</label>
                            <div class="border rounded-md p-2 max-h-48 overflow-y-auto bg-gray-50">
                                ${areasCheckboxes}
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium block mb-2">Linhas de Pesquisa</label>
                            <div class="border rounded-md p-2 max-h-48 overflow-y-auto bg-gray-50">
                                ${linhasCheckboxes}
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="App.hideGenericModal()" class="px-4 py-2 text-gray-600 border rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Salvar Professor
                        </button>
                    </div>
                </form>
            `;
                    App.showGenericModal(content);
                    
                    // Configurar validação de e-mail após o modal ser mostrado
                    setTimeout(() => {
                        this.setupEmailValidation(isEditing, professor.email);
                        this.setupPhoneValidation(isEditing, professor.telefone || '', professor.id || '');
                    }, 100);
                },

                // Função para configurar validação de e-mail no modal do professor
                setupEmailValidation(isEditing, currentEmail) {
                    const emailInput = document.getElementById('professor-email');
                    const emailFeedback = document.getElementById('professor-email-feedback');
                    const emailSpinner = document.getElementById('professor-email-spinner');
                    const submitButton = document.querySelector('button[type="submit"]');
                    
                    if (!emailInput || !emailFeedback || !emailSpinner || !submitButton) {
                        return; // Elementos não encontrados
                    }
                    
                    let emailCheckTimeout;
                    let isEmailValid = isEditing; // Se estiver editando, assume que o e-mail atual é válido
                    
                    function showFeedback(message, isError = false) {
                        emailFeedback.textContent = message;
                        emailFeedback.className = `mt-1 text-sm ${isError ? 'text-red-600' : 'text-green-600'}`;
                        emailFeedback.classList.remove('hidden');
                    }
                    
                    function hideFeedback() {
                        emailFeedback.classList.add('hidden');
                    }
                    
                    function showSpinner() {
                        emailSpinner.classList.remove('hidden');
                    }
                    
                    function hideSpinner() {
                        emailSpinner.classList.add('hidden');
                    }
                    
                    function updateEmailFieldStyle(isError = false) {
                        if (isError) {
                            emailInput.classList.remove('border-gray-300', 'focus:border-indigo-500', 'border-green-500', 'focus:border-green-500');
                            emailInput.classList.add('border-red-500', 'focus:border-red-500');
                        } else if (isEmailValid) {
                            emailInput.classList.remove('border-gray-300', 'focus:border-indigo-500', 'border-red-500', 'focus:border-red-500');
                            emailInput.classList.add('border-green-500', 'focus:border-green-500');
                        } else {
                            emailInput.classList.remove('border-red-500', 'focus:border-red-500', 'border-green-500', 'focus:border-green-500');
                            emailInput.classList.add('border-gray-300', 'focus:border-indigo-500');
                        }
                    }
                    
                    function updateSubmitButton() {
                        if (isEmailValid) {
                            submitButton.disabled = false;
                            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        } else {
                            submitButton.disabled = true;
                            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                    
                    async function checkEmail(email) {
                        // Validação básica de e-mail
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        
                        if (!email || !emailRegex.test(email)) {
                            if (email && !emailRegex.test(email)) {
                                showFeedback('⚠️ Formato de e-mail inválido', true);
                                isEmailValid = false;
                                updateEmailFieldStyle(true);
                            } else {
                                hideFeedback();
                                isEmailValid = false;
                                updateEmailFieldStyle();
                            }
                            updateSubmitButton();
                            return;
                        }
                        
                        // Se estivermos editando e o e-mail não mudou, é válido
                        if (isEditing && email === currentEmail) {
                            showFeedback('✅ E-mail atual', false);
                            isEmailValid = true;
                            updateEmailFieldStyle(false);
                            updateSubmitButton();
                            return;
                        }
                        
                        showSpinner();
                        hideFeedback();
                        
                        try {
                            const response = await fetch('{{ route("check.email") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ email: email })
                            });
                            
                            const data = await response.json();
                            
                            hideSpinner();
                            
                            if (data.exists) {
                                showFeedback('❌ Este e-mail já está em uso. Use outro e-mail.', true);
                                isEmailValid = false;
                                updateEmailFieldStyle(true);
                            } else {
                                showFeedback('✅ E-mail disponível', false);
                                isEmailValid = true;
                                updateEmailFieldStyle(false);
                            }
                            
                        } catch (error) {
                            hideSpinner();
                            showFeedback('⚠️ Erro ao verificar e-mail. Tente novamente.', true);
                            isEmailValid = false;
                            updateEmailFieldStyle(true);
                        }
                        
                        updateSubmitButton();
                    }
                    
                    emailInput.addEventListener('input', function() {
                        const email = this.value.trim();
                        
                        // Limpar timeout anterior
                        clearTimeout(emailCheckTimeout);
                        
                        if (email.length === 0) {
                            hideFeedback();
                            hideSpinner();
                            isEmailValid = false;
                            updateEmailFieldStyle();
                            updateSubmitButton();
                            return;
                        }
                        
                        // Aguardar 500ms após parar de digitar
                        emailCheckTimeout = setTimeout(() => {
                            checkEmail(email);
                        }, 500);
                    });
                    
                    emailInput.addEventListener('blur', function() {
                        const email = this.value.trim();
                        if (email && email.includes('@')) {
                            clearTimeout(emailCheckTimeout);
                            checkEmail(email);
                        }
                    });
                    
                    // Inicializar validação
                    updateSubmitButton();
                    if (isEditing && currentEmail) {
                        checkEmail(currentEmail);
                    }
                },

                setupPhoneValidation: function(isEditing, currentPhone, professorId) {
                    const phoneInput = document.getElementById('professor-telefone');
                    const phoneFeedback = document.getElementById('professor-telefone-feedback');
                    
                    if (!phoneInput || !phoneFeedback) {
                        return;
                    }
                    
                    let phoneCheckTimeout;
                    
                    function showPhoneFeedback(message, isError) {
                        if (isError === undefined) isError = false;
                        phoneFeedback.textContent = message;
                        phoneFeedback.className = 'mt-1 text-sm ' + (isError ? 'text-red-600' : 'text-green-600');
                        phoneFeedback.classList.remove('hidden');
                    }
                    
                    function hidePhoneFeedback() {
                        phoneFeedback.classList.add('hidden');
                    }
                    
                    function updatePhoneFieldStyle(isError) {
                        if (isError === undefined) isError = false;
                        phoneInput.classList.remove('border-red-500', 'focus:border-red-500', 'border-green-500', 'focus:border-green-500');
                        if (isError) {
                            phoneInput.classList.add('border-red-500', 'focus:border-red-500');
                        } else if (phoneInput.value.trim()) {
                            phoneInput.classList.add('border-green-500', 'focus:border-green-500');
                        }
                    }
                    
                    function formatPhone(value) {
                        const cleaned = value.replace(/\D/g, '');
                        if (cleaned.length <= 10) {
                            return cleaned.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
                        } else {
                            return cleaned.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                        }
                    }
                    
                    function validatePhoneFormat(phone) {
                        const cleaned = phone.replace(/\D/g, '');
                        
                        if (!cleaned) {
                            hidePhoneFeedback();
                            updatePhoneFieldStyle();
                            return { valid: true, message: '' };
                        }
                        
                        if (cleaned.length < 10) {
                            return { valid: false, message: '⚠️ Telefone deve ter pelo menos 10 dígitos' };
                        }
                        
                        if (cleaned.length > 11) {
                            return { valid: false, message: '⚠️ Telefone deve ter no máximo 11 dígitos' };
                        }
                        
                        const ddd = cleaned.substring(0, 2);
                        const validDDDs = [
                            '11', '12', '13', '14', '15', '16', '17', '18', '19',
                            '21', '22', '24', '27', '28',
                            '31', '32', '33', '34', '35', '37', '38',
                            '41', '42', '43', '44', '45', '46',
                            '47', '48', '49', '51', '53', '54', '55',
                            '61', '62', '63', '64', '65', '66', '67',
                            '68', '69', '71', '73', '74', '75', '77',
                            '79', '81', '82', '83', '84', '85', '86',
                            '87', '88', '89', '91', '92', '93', '94',
                            '95', '96', '97', '98', '99'
                        ];
                        
                        if (!validDDDs.includes(ddd)) {
                            return { valid: false, message: '⚠️ DDD inválido' };
                        }
                        
                        return { valid: true, message: '' };
                    }
                    
                    async function validatePhone(phone) {
                        const formatValidation = validatePhoneFormat(phone);
                        
                        if (!formatValidation.valid) {
                            showPhoneFeedback(formatValidation.message, true);
                            updatePhoneFieldStyle(true);
                            return;
                        }
                        
                        if (!phone.trim()) {
                            hidePhoneFeedback();
                            updatePhoneFieldStyle();
                            return;
                        }
                        
                        const cleaned = phone.replace(/\D/g, '');
                        const currentCleaned = currentPhone.replace(/\D/g, '');
                        
                        if (isEditing && cleaned === currentCleaned) {
                            showPhoneFeedback('✅ Telefone atual', false);
                            updatePhoneFieldStyle(false);
                            return;
                        }
                        
                        try {
                            const response = await fetch('{{ route("check.phone") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ 
                                    telefone: cleaned,
                                    professor_id: professorId 
                                })
                            });
                            
                            const data = await response.json();
                            console.log('Resposta da API:', data);
                            
                            if (data.exists) {
                                showPhoneFeedback('❌ Este telefone já está cadastrado', true);
                                updatePhoneFieldStyle(true);
                            } else {
                                showPhoneFeedback('✅ Telefone disponível', false);
                                updatePhoneFieldStyle(false);
                            }
                            
                        } catch (error) {
                            showPhoneFeedback('⚠️ Erro ao verificar telefone', true);
                            updatePhoneFieldStyle(true);
                        }
                    }
                    
                    phoneInput.addEventListener('input', function(e) {
                        let value = e.target.value;
                        const formatted = formatPhone(value);
                        if (formatted !== value) {
                            e.target.value = formatted;
                        }
                        
                        clearTimeout(phoneCheckTimeout);
                        phoneCheckTimeout = setTimeout(function() {
                            validatePhone(e.target.value);
                        }, 500);
                    });
                    
                    phoneInput.addEventListener('blur', function() {
                        clearTimeout(phoneCheckTimeout);
                        validatePhone(this.value);
                    });
                    
                    if (phoneInput.value) {
                        phoneInput.value = formatPhone(phoneInput.value);
                        validatePhone(phoneInput.value);
                    }
                },

                async saveLinha(event) {
                    // ... (código existente - sem alteração)
                    event.preventDefault();
                    const form = event.target;
                    const id = form.dataset.id;
                    const isEditing = id !== '';

                    const data = {
                        nome: form.nome.value,
                        descricao: form.descricao.value,
                        id_area_pesquisa: form.id_area_pesquisa.value
                    };

                    try {
                        const url = isEditing ? `/admin/linhas-pesquisa/${id}` : '/admin/linhas-pesquisa';
                        const method = isEditing ? 'PUT' : 'POST';

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            App.hideGenericModal();
                            await this.loadLinhasPesquisa();
                            this.showSuccess(isEditing ? 'Linha de pesquisa atualizada!' : 'Linha de pesquisa criada!');
                        } else {
                            this.showError(result.error || 'Erro ao salvar');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                async saveProfessor(event) {
                    // ... (código existente - sem alteração)
                    event.preventDefault();
                    const form = event.target;
                    const id = form.dataset.id;
                    const isEditing = id !== '';
                    
                    // Verificar se o botão de submit está desabilitado (e-mail inválido)
                    const submitButton = event.target.querySelector('button[type="submit"]');
                    if (submitButton && submitButton.disabled) {
                        this.showError('Por favor, verifique o e-mail antes de salvar.');
                        return;
                    }

                    const data = {
                        nome: form.nome.value,
                        email: form.email.value,
                        telefone: form.telefone.value,
                        id_curso: form.id_curso.value,
                        departamento: form.departamento.value,
                        areas_interesse_ids: Array.from(form.querySelectorAll('input[name="areas_interesse_ids"]:checked')).map(cb => cb.value),
                        linhas_pesquisa_ids: Array.from(form.querySelectorAll('input[name="linhas_pesquisa_ids"]:checked')).map(cb => cb.value)
                    };

                    try {
                        const url = isEditing ? `/admin/professores/${id}` : '/admin/professores';
                        const method = isEditing ? 'PUT' : 'POST';

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            App.hideGenericModal();
                            await this.loadProfessores();
                            this.showSuccess(isEditing ? 'Professor atualizado!' : 'Professor criado!');
                        } else {
                            this.showError(result.error || 'Erro ao salvar');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                async deleteLinha(id) {
                    // ... (código existente - sem alteração)
                    if (!confirm('Tem certeza de que deseja excluir esta linha de pesquisa?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/linhas-pesquisa/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            await this.loadLinhasPesquisa();
                            this.showSuccess('Linha de pesquisa excluída!');
                        } else {
                            this.showError(result.error || 'Erro ao excluir');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                async deleteProfessor(id) {
                    // ... (código existente - sem alteração)
                    if (!confirm('Tem certeza de que deseja excluir este professor?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/professores/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            await this.loadProfessores();
                            this.showSuccess('Professor excluído!');
                        } else {
                            this.showError(result.error || 'Erro ao excluir');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                // --- NOVAS FUNÇÕES ---

                async desativarUsuario(id) {
                    if (!confirm('Tem certeza de que deseja desativar este usuário?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/usuarios/desativar/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            await this.loadUsuarios(); // Recarrega a tabela de usuários
                            this.showSuccess('Usuário desativado com sucesso!');
                        } else {
                            this.showError(result.error || 'Erro ao desativar usuário');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                async ativarUsuario(id) {
                    if (!confirm('Tem certeza de que deseja ativar este usuário?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/usuarios/ativar/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            await this.loadUsuarios(); // Recarrega a tabela de usuários
                            this.showSuccess('Usuário ativado com sucesso!');
                        } else {
                            this.showError(result.error || 'Erro ao ativar usuário');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },


                showSuccess(message) {
                    // Simple alert for now - you can replace with a toast notification
                    alert(message);
                },

                showError(message) {
                    alert('Erro: ' + message);
                }
            };

            // Make AdminPanel globally available
            window.AdminPanel = AdminPanel;
            AdminPanel.init();

            // Keep App available for modal functions
            if (!window.App) {
                window.App = {
                    showGenericModal(content) {
                        let modal = document.getElementById('generic-modal');
                        let modalContent = document.getElementById('generic-modal-content');
                        if(modal && modalContent) {
                            modalContent.innerHTML = content;
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                        } else {
                            console.error('Modal elements not found');
                        }
                    },

                    hideGenericModal() {
                        let modal = document.getElementById('generic-modal');
                        if(modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    }
                };
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .admin-panel { display: none; }
        .admin-panel.active { display: block; }

        /* Melhoria: Estilo para selects múltiplos */
        select[multiple] {
            background-image: none; /* Remove a seta padrão */
        }
    </style>
@endpush