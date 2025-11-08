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
                                        <th class="p-3 font-semibold">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody id="usuarios-tbody">
                                    <tr>
                                        <td colspan="5" class="p-3 text-center">
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
                        tbody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-gray-500">Nenhum usuário cadastrado</td></tr>';
                        return;
                    }

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
                    <td class="p-3 space-x-2">
                        ${usuario.ativo
                        ? `<button data-id="${usuario.id}" class="deactivate-user-btn text-red-600 hover:underline">Desativar</button>`
                        : `<button data-id="${usuario.id}" class="activate-user-btn text-green-600 hover:underline">Ativar</button>`
                    }
                    </td>
                </tr>
            `).join('');
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
                        <input type="email" name="email" placeholder="E-mail" class="w-full px-3 py-2 border rounded-md" value="${professor.email}" required>
                        <input type="tel" name="telefone" placeholder="Telefone" class="w-full px-3 py-2 border rounded-md" value="${professor.telefone || ''}">

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
