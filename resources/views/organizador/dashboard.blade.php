@extends('layouts.app')

@section('title', 'Dashboard organizador - PesqHub')

@section('content')
    <div class="container mx-auto px-4 lg:px-6 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Painel do organizador</h1>
            <a href="{{ route('organizador.profile') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Editar Perfil
            </a>
        </div>

        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $error }}
            </div>
        @endif

        <div class="flex flex-col md:flex-row gap-8">
            <aside class="w-full md:w-1/4 lg:w-1/5">
                <nav class="bg-white p-4 rounded-lg shadow-sm space-y-2 sticky top-24">
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md organizador-panel-trigger active" data-panel="linhas">
                        Gerenciar Linhas de Pesquisa
                    </a>
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md organizador-panel-trigger" data-panel="professores">
                        Gerenciar Professores
                    </a>
                    <a href="#" class="sidebar-link block px-3 py-2 rounded-md organizador-panel-trigger" data-panel="areas">
                        Gerenciar Áreas de Pesquisa
                    </a>
                </nav>
            </aside>

            <div class="w-full md:w-3/4 lg:w-4/5">
                <div id="organizador-content">
                    <div id="panel-linhas" class="organizador-panel active">
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

                    <div id="panel-professores" class="organizador-panel hidden">
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

                    <div id="panel-usuarios" class="organizador-panel hidden">
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

                    <div id="panel-areas" class="organizador-panel hidden">
                        <x-manage-areas-panel base-path="/organizador" />
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const organizadorPanel = {
                data: {
                    linhasPesquisa: [],
                    professores: [],
                    cursos: [],
                    areasPesquisa: [],
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
                    ]);
                },

                setupEventListeners() {
                    // Panel navigation
                    document.querySelectorAll('.organizador-panel-trigger').forEach(trigger => {
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
                    document.querySelectorAll('.organizador-panel').forEach(panelEl => {
                        panelEl.classList.toggle('active', panelEl.id === `panel-${panel}`);
                        panelEl.classList.toggle('hidden', panelEl.id !== `panel-${panel}`);
                    });
                },

                // =============== FUNÇÕES DE CARREGAMENTO DE DADOS ===============

                async loadLinhasPesquisa() {
                    try {
                        const response = await fetch('/organizador/linhas-pesquisa');
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
                    try {
                        const response = await fetch('/organizador/professores');
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
                    try {
                        const response = await fetch('/organizador/cursos');
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
                        const response = await fetch('/organizador/areas-pesquisa');
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

                // =============== FUNÇÕES DE RENDERIZAÇÃO ===============

                renderLinhasTable() {
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
                        <button data-id="${linha.id}" class="edit-linha-btn text-indigo-600 hover:underline">Editar</button>
                        <button data-id="${linha.id}" class="delete-linha-btn text-red-600 hover:underline">Excluir</button>
                    </td>
                </tr>
            `).join('');
                },

                renderProfessoresTable() {
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
                        <button data-id="${professor.id}" class="edit-professor-btn text-indigo-600 hover:underline">Editar</button>
                        <button data-id="${professor.id}" class="delete-professor-btn text-red-600 hover:underline">Excluir</button>
                    </td>
                </tr>
            `).join('');
                },

                // =============== MODAIS E SALVAMENTO ===============

                showLinhaModal(id = null) {
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
                <form onsubmit="organizadorPanel.saveLinha(event)" class="p-8" data-id="${isEditing ? linha.id : ''}">
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
                <form onsubmit="organizadorPanel.saveProfessor(event)" class="p-8" data-id="${isEditing ? professor.id : ''}">
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
                        const url = isEditing ? `/organizador/linhas-pesquisa/${id}` : '/organizador/linhas-pesquisa';
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
                        const url = isEditing ? `/organizador/professores/${id}` : '/organizador/professores';
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
                    this.showConfirmDialog(
                        'Tem certeza de que deseja excluir esta linha de pesquisa? Esta ação não pode ser desfeita.',
                        async () => {
                            try {
                                const response = await fetch(`/organizador/linhas-pesquisa/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });

                                const result = await response.json();

                                if (result.success) {
                                    await this.loadLinhasPesquisa();
                                    this.showSuccess('Linha de pesquisa excluída com sucesso!');
                                } else {
                                    this.showError(result.error || 'Erro ao excluir');
                                }
                            } catch (error) {
                                this.showError('Erro de conexão');
                            }
                        }
                    );
                },

                async deleteProfessor(id) {
                    this.showConfirmDialog(
                        'Tem certeza de que deseja excluir este professor? Esta ação não pode ser desfeita.',
                        async () => {
                            try {
                                const response = await fetch(`/organizador/professores/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });

                                const result = await response.json();

                                if (result.success) {
                                    await this.loadProfessores();
                                    this.showSuccess('Professor excluído com sucesso!');
                                } else {
                                    this.showError(result.error || 'Erro ao excluir');
                                }
                            } catch (error) {
                                this.showError('Erro de conexão');
                            }
                        }
                    );
                },

                showSuccess(message) {
                    this.showAlert(message, 'success');
                },

                showError(message) {
                    this.showAlert('Erro: ' + message, 'error');
                },

                showAlert(message, type = 'info') {
                    const iconMap = {
                        'success': '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
                        'error': '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>',
                        'warning': '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                        'info': '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
                    };
                    
                    const colorMap = {
                        'success': 'from-emerald-500 to-emerald-600 text-white',
                        'error': 'from-red-500 to-red-600 text-white',
                        'warning': 'from-amber-500 to-amber-600 text-white',
                        'info': 'from-indigo-500 to-indigo-600 text-white'
                    };

                    const bgColorMap = {
                        'success': 'from-emerald-50 to-emerald-50 border-emerald-200 text-emerald-800',
                        'error': 'from-red-50 to-red-50 border-red-200 text-red-800',
                        'warning': 'from-amber-50 to-amber-50 border-amber-200 text-amber-800',
                        'info': 'from-indigo-50 to-indigo-50 border-indigo-200 text-indigo-800'
                    };

                    const buttonColorMap = {
                        'success': 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700',
                        'error': 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700',
                        'warning': 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700',
                        'info': 'bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700'
                    };

                    const content = `
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-gradient-to-br ${colorMap[type]} shadow-lg">
                                ${iconMap[type]}
                            </div>
                            <div class="bg-gradient-to-br ${bgColorMap[type]} rounded-2xl p-6 mb-6 shadow-sm border border-opacity-20">
                                <p class="font-medium text-lg leading-relaxed">${message}</p>
                            </div>
                            <button onclick="App.hideConfirmationModal()" class="${buttonColorMap[type]} text-white font-semibold px-10 py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-opacity-50">
                                Entendi
                            </button>
                        </div>
                    `;
                    App.showConfirmationModal(content);
                },

                showConfirmDialog(message, onConfirm, onCancel = null) {
                    // Store the callback functions globally for access from modal
                    window._tempModalConfirm = onConfirm;
                    window._tempModalCancel = onCancel;
                    
                    const content = `
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 shadow-lg border border-indigo-300">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-indigo-800 mb-2">
                                Confirmação Necessária
                            </h3>
                            <p class="text-indigo-500 text-sm mb-6 opacity-80">Esta ação requer sua confirmação</p>
                            <div class="bg-gradient-to-br from-indigo-50 to-indigo-50 rounded-2xl p-6 mb-8 border border-indigo-200 shadow-sm">
                                <p class="text-indigo-700 text-lg leading-relaxed font-medium">${message}</p>
                            </div>
                            <div class="flex justify-center gap-4">
                                <button onclick="App.hideConfirmationModal(); if(window._tempModalCancel) window._tempModalCancel();" 
                                        class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-semibold px-8 py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
                                    Cancelar
                                </button>
                                <button onclick="App.hideConfirmationModal(); if(window._tempModalConfirm) window._tempModalConfirm();" 
                                        class="bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    `;
                    App.showConfirmationModal(content);
                },
            };

            // Make organizadorPanel globally available
            window.organizadorPanel = organizadorPanel;
            organizadorPanel.init();

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
                            
                            setTimeout(() => {
                                modalContent.classList.add('scale-100', 'opacity-100');
                                modalContent.classList.remove('scale-95', 'opacity-0');
                            }, 10);
                        } else {
                            console.error('Modal elements not found');
                        }
                    },

                    hideGenericModal() {
                        let modal = document.getElementById('generic-modal');
                        let modalContent = document.getElementById('generic-modal-content');
                        if(modal && modalContent) {
                            modalContent.classList.add('scale-95', 'opacity-0');
                            modalContent.classList.remove('scale-100', 'opacity-100');
                            
                            setTimeout(() => {
                                modal.classList.add('hidden');
                                modal.classList.remove('flex');
                            }, 300);
                        }
                    },

                    showConfirmationModal(content) {
                        let modal = document.getElementById('confirmation-modal');
                        let modalContent = document.getElementById('confirmation-modal-content');
                        if(modal && modalContent) {
                            modalContent.innerHTML = content;
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                            
                            setTimeout(() => {
                                modalContent.classList.add('scale-100', 'opacity-100');
                                modalContent.classList.remove('scale-95', 'opacity-0');
                            }, 10);
                        } else {
                            console.error('Confirmation modal elements not found');
                        }
                    },

                    hideConfirmationModal() {
                        let modal = document.getElementById('confirmation-modal');
                        let modalContent = document.getElementById('confirmation-modal-content');
                        if(modal && modalContent) {
                            modalContent.classList.add('scale-95', 'opacity-0');
                            modalContent.classList.remove('scale-100', 'opacity-100');
                            
                            setTimeout(() => {
                                modal.classList.add('hidden');
                                modal.classList.remove('flex');
                            }, 300);
                        }
                    }
                };
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .organizador-panel { display: none; }
        .organizador-panel.active { display: block; }

        /* Melhoria: Estilo para selects múltiplos */
        select[multiple] {
            background-image: none; /* Remove a seta padrão */
        }

        /* Animações dos modais */
        .modal-enter {
            animation: modalEnter 0.3s ease-out forwards;
        }

        @keyframes modalEnter {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Gradientes personalizados */
        .bg-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Efeito hover para botões */
        .btn-elegant {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-elegant:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }
    </style>
@endpush

<!-- Modal de Confirmação -->
<div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
    <div id="confirmation-modal-content" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative transform transition-all duration-300 scale-95 opacity-0 modal-enter">
        <!-- Conteúdo será inserido dinamicamente -->
    </div>
</div>

<!-- Modal Genérico -->
<div id="generic-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
    <div id="generic-modal-content" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-y-auto relative transform transition-all duration-300 scale-95 opacity-0 modal-enter">
        <!-- Conteúdo será inserido dinamicamente -->
    </div>
</div>

