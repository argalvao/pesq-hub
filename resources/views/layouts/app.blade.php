@php use App\Services\DatabaseService; @endphp
    <!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PesqHub: Hub de Pesquisa')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .view {
            display: none;
        }

        .view.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .sidebar-link.active {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 600;
        }

        .tab-button.active {
            border-color: #4f46e5;
            color: #4f46e5;
            background-color: #eef2ff;
        }
    </style>

    @stack('styles')
</head>
<body class="text-gray-900">
<div id="app-container">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <nav class="container mx-auto px-4 lg:px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="bg-indigo-700 text-white font-bold text-xl rounded-md p-2">P</span>
                    <span class="text-2xl font-bold text-indigo-800">PesqHub</span>
                </a>
                <a href="{{ route('sobre') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">
                    Sobre
                </a>
            </div>
            <div id="user-actions">
                @if(Session::has('user'))
                    @php $user = Session::get('user'); @endphp
                    <div class="flex items-center space-x-4">
                        <span class="font-semibold">{{ $user['nome'] }}</span>
                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">
                                {{ app(App\Services\UsuarioService::class)->getNivelPermissaoTexto($user['tipo_permissao']) }}
                            </span>
                        @if($user['tipo_permissao'] == DatabaseService::NIVEL_ADMIN)
                            <a href="{{ route('admin.dashboard') }}"
                               class="font-semibold text-indigo-600 hover:underline">Dashboard</a>
                        @elseif($user['tipo_permissao'] == DatabaseService::NIVEL_ORGANIZADOR)
                            <a href="{{ route('organizador.dashboard') }}"
                               class="font-semibold text-indigo-600 hover:underline">Painel Organizador</a>
                        @elseif($user['tipo_permissao'] == DatabaseService::NIVEL_BASICO)
                            <a href="{{ route('basico.dashboard') }}"
                               class="font-semibold text-indigo-600 hover:underline">Minha Área</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:underline">Sair</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Cadastrar</a>
                        <button id="login-trigger-btn" class="bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">Login</button>
                        </div>
                @endif
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
</div>

<!-- Modals Globais -->
<div id="generic-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
    <div id="generic-modal-content" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg relative transform transition-all duration-300 scale-95 opacity-0">
        <!-- Content is injected dynamically -->
    </div>
</div>

<div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
    <div id="confirmation-modal-content" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative transform transition-all duration-300 scale-95 opacity-0">
        <!-- Content is injected dynamically -->
    </div>
</div>

<script>
// Global Modal Functions
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
</script>

@stack('scripts')
</body>
</html>
