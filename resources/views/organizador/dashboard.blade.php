@extends('layouts.app')

@section('title', 'Dashboard Organizador - PesqHub')

@section('content')
<div class="container mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-3xl font-bold mb-6">Painel do Organizador</h1>
    
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
            </nav>
        </aside>

        <div class="w-full md:w-3/4 lg:w-4/5">
            <div id="organizador-content">
                <!-- Linhas de Pesquisa Panel -->
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
                                        <th class="p-3 font-semibold">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="linhas-tbody">
                                    <tr>
                                        <td colspan="3" class="p-3 text-center">
                                            <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                                            Carregando...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Professores Panel -->
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
            currentPanel: 'linhas'
        },

        async init() {
            this.setupEventListeners();
            await this.loadLinhasPesquisa();
            await this.loadProfessores();
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

            // Table actions
            document.getElementById('linhas-tbody').addEventListener('click', (e) => {
                if (e.target.classList.contains('edit-linha-btn')) {
                    this.showLinhaModal(parseInt(e.target.dataset.id));
                } else if (e.target.classList.contains('delete-linha-btn')) {
                    this.deleteLinha(parseInt(e.target.dataset.id));
                }
            });

            document.getElementById('professores-tbody').addEventListener('click', (e) => {
                if (e.target.classList.contains('edit-professor-btn')) {
                    this.showProfessorModal(parseInt(e.target.dataset.id));
                } else if (e.target.classList.contains('delete-professor-btn')) {
                    this.deleteProfessor(parseInt(e.target.dataset.id));
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
                this.showError('Erro de conexão');
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
                this.showError('Erro de conexão');
            }
        },

        renderLinhasTable() {
            const tbody = document.getElementById('linhas-tbody');
            
            if (this.data.linhasPesquisa.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="p-3 text-center text-gray-500">Nenhuma linha de pesquisa cadastrada</td></tr>';
                return;
            }

            tbody.innerHTML = this.data.linhasPesquisa.map(linha => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">${linha.nome}</td>
                    <td class="p-3 text-gray-600">${linha.descricao}</td>
                    <td class="p-3 space-x-2">
                        <button data-id="${linha.id}" class="edit-linha-btn text-blue-600 hover:underline">Editar</button>
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
                    <td class="p-3 text-gray-600">${professor.curso}</td>
                    <td class="p-3 text-gray-600">${professor.email}</td>
                    <td class="p-3 space-x-2">
                        <button data-id="${professor.id}" class="edit-professor-btn text-blue-600 hover:underline">Editar</button>
                        <button data-id="${professor.id}" class="delete-professor-btn text-red-600 hover:underline">Excluir</button>
                    </td>
                </tr>
            `).join('');
        },

        showLinhaModal(id = null) {
            const isEditing = id !== null;
            const linha = isEditing ? this.data.linhasPesquisa.find(l => l.id === id) : { nome: '', descricao: '' };

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
                            <textarea name="descricao" rows="4" class="w-full px-3 py-2 border rounded-md" required>${linha.descricao}</textarea>
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
            const professor = isEditing ? this.data.professores.find(p => p.id === id) : 
                { nome: '', email: '', telefone: '', curso: '', areas_interesse: [], linhas_pesquisa_ids: [] };

            const linhasOptions = this.data.linhasPesquisa.map(linha => 
                `<option value="${linha.id}" ${professor.linhas_pesquisa_ids.includes(linha.id) ? 'selected' : ''}>${linha.nome}</option>`
            ).join('');

            const content = `
                <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                <form onsubmit="organizadorPanel.saveProfessor(event)" class="p-8" data-id="${isEditing ? professor.id : ''}">
                    <h2 class="text-2xl font-bold mb-6">${isEditing ? 'Editar' : 'Adicionar'} Professor</h2>
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                        <input type="text" name="nome" placeholder="Nome Completo" class="w-full px-3 py-2 border rounded-md" value="${professor.nome}" required>
                        <input type="email" name="email" placeholder="E-mail" class="w-full px-3 py-2 border rounded-md" value="${professor.email}" required>
                        <input type="tel" name="telefone" placeholder="Telefone" class="w-full px-3 py-2 border rounded-md" value="${professor.telefone}" required>
                        <input type="text" name="curso" placeholder="Curso" class="w-full px-3 py-2 border rounded-md" value="${professor.curso}" required>
                        <div>
                            <label class="text-sm font-medium block mb-1">Áreas de Interesse (separadas por vírgula)</label>
                            <input type="text" name="areas_interesse" class="w-full px-3 py-2 border rounded-md" value="${(professor.areas_interesse || []).join(', ')}" placeholder="IA, Machine Learning, etc.">
                        </div>
                        <div>
                            <label class="text-sm font-medium block mb-1">Linhas de Pesquisa</label>
                            <select name="linhas_pesquisa_ids" multiple class="w-full h-32 px-3 py-2 border rounded-md">
                                ${linhasOptions}
                            </select>
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
                descricao: form.descricao.value
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
                curso: form.curso.value,
                areas_interesse: form.areas_interesse.value.split(',').map(s => s.trim()).filter(s => s),
                linhas_pesquisa_ids: Array.from(form.linhas_pesquisa_ids.selectedOptions).map(opt => parseInt(opt.value))
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
            if (!confirm('Tem certeza de que deseja excluir esta linha de pesquisa?')) {
                return;
            }

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
                    this.showSuccess('Linha de pesquisa excluída!');
                } else {
                    this.showError(result.error || 'Erro ao excluir');
                }
            } catch (error) {
                this.showError('Erro de conexão');
            }
        },

        async deleteProfessor(id) {
            if (!confirm('Tem certeza de que deseja excluir este professor?')) {
                return;
            }

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
                    this.showSuccess('Professor excluído!');
                } else {
                    this.showError(result.error || 'Erro ao excluir');
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

    // Make organizadorPanel globally available
    window.organizadorPanel = organizadorPanel;
    organizadorPanel.init();

    // Keep App available for modal functions
    if (!window.App) {
        window.App = {
            showGenericModal(content) {
                document.getElementById('generic-modal-content').innerHTML = content;
                document.getElementById('generic-modal').classList.remove('hidden');
                document.getElementById('generic-modal').classList.add('flex');
            },

            hideGenericModal() {
                document.getElementById('generic-modal').classList.add('hidden');
                document.getElementById('generic-modal').classList.remove('flex');
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
</style>
@endpush
