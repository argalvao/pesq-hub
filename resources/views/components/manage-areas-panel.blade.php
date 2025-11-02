@props([
    'basePath' => '/admin' // Define '/admin' como padrão
])

<div class="admin-panel-component bg-white p-6 rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Gerenciar Áreas de Pesquisa</h2>
        <button class="add-area-btn bg-green-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-green-600 text-sm">
            Adicionar Nova
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="areas-table w-full text-left text-sm">
            <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-3 font-semibold">Nome</th>
                <th class="p-3 font-semibold">Descrição</th>
                <th class="p-3 font-semibold">Nº de Linhas</th>
                <th class="p-3 font-semibold">Nº de Professores</th>
                <th class="p-3 font-semibold">Ações</th>
            </tr>
            </thead>
            <tbody class="areas-tbody">
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

{{-- Este script é isolado e só funciona para este componente --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Usamos uma classe única para o painel para evitar conflitos
            const panelElement = document.querySelector('.admin-panel-component');

            // Se o painel não estiver na página, o script não faz nada
            if (!panelElement) return;

            const AreaPanel = {
                basePath: @json($basePath), // Pega o basePath do Blade
                data: {
                    areasPesquisa: []
                },

                // Referências aos elementos da DOM dentro deste painel
                elements: {
                    tbody: panelElement.querySelector('.areas-tbody'),
                    addBtn: panelElement.querySelector('.add-area-btn')
                },

                async init() {
                    this.setupEventListeners();
                    await this.loadAreasPesquisa();
                },

                setupEventListeners() {
                    this.elements.addBtn.addEventListener('click', () => this.showAreaModal());

                    this.elements.tbody.addEventListener('click', (e) => {
                        if (e.target.classList.contains('edit-area-btn')) {
                            this.showAreaModal(e.target.dataset.id);
                        } else if (e.target.classList.contains('delete-area-btn')) {
                            this.deleteArea(e.target.dataset.id);
                        }
                    });
                },

                async loadAreasPesquisa() {
                    try {
                        // USA O BASEPATH DINÂMICO!
                        const response = await fetch(`${this.basePath}/areas-pesquisa`);
                        const result = await response.json();
                        if (result.success) {
                            this.data.areasPesquisa = result.data;
                            this.renderAreasTable();
                        } else {
                            this.showError('Erro ao carregar áreas de pesquisa');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão ao carregar áreas de pesquisa');
                    }
                },

                renderAreasTable() {
                    if (this.data.areasPesquisa.length === 0) {
                        this.elements.tbody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-gray-500">Nenhuma área de pesquisa cadastrada</td></tr>';
                        return;
                    }

                    this.elements.tbody.innerHTML = this.data.areasPesquisa.map(area => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium">${area.nome}</td>
                        <td class="p-3 text-gray-600">${area.descricao || ''}</td>
                        <td class="p-3 text-gray-600">${area.linhas_pesquisa_count}</td>
                        <td class="p-3 text-gray-600">${area.professores_count}</td>
                        <td class="p-3 space-x-2">
                            <button data-id="${area.id}" class="edit-area-btn text-blue-600 hover:underline">Editar</button>
                            <button data-id="${area.id}" class="delete-area-btn text-red-600 hover:underline">Excluir</button>
                        </td>
                    </tr>
                `).join('');
                },

                showAreaModal(id = null) {
                    const isEditing = id !== null;
                    const area = isEditing
                        ? this.data.areasPesquisa.find(a => a.id === id)
                        : { nome: '', descricao: '' };

                    const content = `
                    <button onclick="App.hideGenericModal()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
                    <form onsubmit="AreaPanel.saveArea(event)" class="p-8" data-id="${isEditing ? area.id : ''}">
                        <h2 class="text-2xl font-bold mb-4">${isEditing ? 'Editar' : 'Adicionar'} Área de Pesquisa</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium block mb-1">Nome</label>
                                <input type="text" name="nome" class="w-full px-3 py-2 border rounded-md" value="${area.nome}" required>
                            </div>
                            <div>
                                <label class="text-sm font-medium block mb-1">Descrição</label>
                                <textarea name="descricao" rows="4" class="w-full px-3 py-2 border rounded-md">${area.descricao || ''}</textarea>
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
                    // Assumindo que App.showGenericModal() está disponível globalmente
                    App.showGenericModal(content);
                },

                async saveArea(event) {
                    event.preventDefault();
                    const form = event.target;
                    const id = form.dataset.id;
                    const isEditing = id !== '';

                    const data = {
                        nome: form.nome.value,
                        descricao: form.descricao.value
                    };

                    try {
                        const url = isEditing ? `${this.basePath}/areas-pesquisa/${id}` : `${this.basePath}/areas-pesquisa`;
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
                            await this.loadAreasPesquisa();
                            this.showSuccess(isEditing ? 'Área atualizada!' : 'Área criada!');

                            // Opcional: Dispara um evento para que outros painéis (como o de Linhas) possam recarregar
                            document.dispatchEvent(new CustomEvent('areas-updated'));
                        } else {
                            this.showError(result.error || 'Erro ao salvar');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                async deleteArea(id) {
                    if (!confirm('Tem certeza de que deseja excluir esta área de pesquisa?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`${this.basePath}/areas-pesquisa/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            await this.loadAreasPesquisa();
                            this.showSuccess('Área de pesquisa excluída!');
                            // Opcional: Dispara evento
                            document.dispatchEvent(new CustomEvent('areas-updated'));
                        } else {
                            this.showError(result.error || 'Erro ao excluir');
                        }
                    } catch (error) {
                        this.showError('Erro de conexão');
                    }
                },

                // Funções de helper (elas podem ser movidas para um App.js global)
                showSuccess(message) { alert(message); },
                showError(message) { alert('Erro: ' + message); }
            };

            // Disponibiliza o AreaPanel globalmente para o onsubmit do formulário
            window.AreaPanel = AreaPanel;
            AreaPanel.init();

            // Opcional: Se o painel de Linhas de Pesquisa (AdminPanel) existir,
            // faça com que ele ouça o evento de atualização das áreas.
            if (window.AdminPanel) {
                document.addEventListener('areas-updated', () => {
                    // Recarrega as áreas no painel principal, se ele existir
                    if (typeof window.AdminPanel.loadAreasPesquisa === 'function') {
                        window.AdminPanel.loadAreasPesquisa();
                    }
                });
            }
        });
    </script>
@endpush
