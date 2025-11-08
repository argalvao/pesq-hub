@extends('layouts.app')

@section('title', 'Sobre - PesqHub')

@section('content')
<div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="container mx-auto px-4 lg:px-6 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
                    PesqHub
                </h1>
                <p class="text-xl md:text-2xl mb-4 text-blue-100">
                    Conectando Estudantes e Professores da UEFS
                </p>
                <p class="text-lg md:text-xl text-blue-200">
                    A ponte entre quem busca oportunidades de pesquisa e quem oferece
                </p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">O Que é o PesqHub?</h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-purple-600 mx-auto"></div>
                </div>
                
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    <p class="text-xl text-center mb-8">
                        O <strong>PesqHub</strong> é a plataforma que conecta <strong>estudantes</strong> 
                        em busca de oportunidades de pesquisa com <strong>professores</strong> que orientam 
                        projetos acadêmicos na <strong>UEFS</strong>.
                    </p>
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-l-4 border-blue-600 p-6 my-8 rounded-r-lg">
                        <p class="text-lg font-semibold text-blue-900 mb-2">
                            🎯 Nossa Missão
                        </p>
                        <p class="text-gray-700">
                            Facilitar o encontro entre estudantes interessados em pesquisa e professores 
                            orientadores, tornando o processo de descoberta de oportunidades acadêmicas 
                            simples, rápido e acessível.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gradient-to-br from-blue-600 to-purple-600">
        <div class="container mx-auto px-4 lg:px-6">
            <h2 class="text-3xl font-bold text-white text-center mb-12">A Comunidade PesqHub</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-white">
                <div class="text-center transform hover:scale-110 transition-transform">
                    <div class="text-5xl font-bold mb-2">{{ $stats['professores'] }}+</div>
                    <div class="text-xl text-blue-100">Professores</div>
                    <div class="text-sm text-blue-200 mt-1">Orientadores Ativos</div>
                </div>
                <div class="text-center transform hover:scale-110 transition-transform">
                    <div class="text-5xl font-bold mb-2">{{ $stats['linhas_pesquisa'] }}+</div>
                    <div class="text-xl text-blue-100">Linhas de Pesquisa</div>
                    <div class="text-sm text-blue-200 mt-1">Oportunidades</div>
                </div>
                <div class="text-center transform hover:scale-110 transition-transform">
                    <div class="text-5xl font-bold mb-2">{{ $stats['areas_pesquisa'] }}+</div>
                    <div class="text-xl text-blue-100">Áreas de Pesquisa</div>
                    <div class="text-sm text-blue-200 mt-1">Cobertas</div>
                </div>
                <div class="text-center transform hover:scale-110 transition-transform">
                    <div class="text-5xl font-bold mb-2">{{ $stats['usuarios_ativos'] }}+</div>
                    <div class="text-xl text-blue-100">Usuários</div>
                    <div class="text-sm text-blue-200 mt-1">Estudantes Cadastrados</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience Section - 2 PÚBLICOS -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Para Quem é o PesqHub?</h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-purple-600 mx-auto mb-4"></div>
                    <p class="text-xl text-gray-600">Conectando os dois lados da pesquisa acadêmica</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-12">
                    <!-- Estudantes -->
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-300 border-t-4 border-blue-600">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-8 text-white">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg class="w-14 h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-center mb-2">👨‍🎓 Estudantes</h3>
                            <p class="text-center text-blue-100 text-lg">Você que busca oportunidades</p>
                        </div>
                        <div class="p-8">
                            <p class="text-gray-700 mb-6 text-center text-lg font-medium">
                                Encontre o professor ideal para orientar sua pesquisa
                            </p>
                            
                            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                                <p class="text-blue-900 font-semibold mb-2">🎯 O que você pode fazer:</p>
                            </div>
                            
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Buscar por área de interesse</span>
                                        <p class="text-sm text-gray-600">Filtros inteligentes por palavra-chave, curso e área</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Conhecer professores orientadores</span>
                                        <p class="text-sm text-gray-600">Perfis completos com áreas de atuação</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Explorar linhas de pesquisa</span>
                                        <p class="text-sm text-gray-600">Descubra projetos alinhados ao seu interesse</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Entrar em contato direto</span>
                                        <p class="text-sm text-gray-600">Email e telefone para comunicação rápida</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Acesso 24/7</span>
                                        <p class="text-sm text-gray-600">Pesquise quando e onde quiser</p>
                                    </div>
                                </li>
                            </ul>

                            <div class="mt-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg text-center">
                                <p class="font-semibold">💡 Dica:</p>
                                <p class="text-sm">Use os filtros para encontrar o orientador perfeito para seu TCC ou projeto de pesquisa!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Professores -->
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-300 border-t-4 border-purple-600">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-8 text-white">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg class="w-14 h-14 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-center mb-2">👨‍🏫 Professores</h3>
                            <p class="text-center text-purple-100 text-lg">Você que orienta pesquisas</p>
                        </div>
                        <div class="p-8">
                            <p class="text-gray-700 mb-6 text-center text-lg font-medium">
                                Divulgue suas pesquisas e encontre estudantes motivados
                            </p>
                            
                            <div class="bg-purple-50 rounded-lg p-4 mb-6">
                                <p class="text-purple-900 font-semibold mb-2">🎯 O que você pode fazer:</p>
                            </div>
                            
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Gerenciar seu perfil acadêmico</span>
                                        <p class="text-sm text-gray-600">Mantenha suas informações sempre atualizadas</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Cadastrar linhas de pesquisa</span>
                                        <p class="text-sm text-gray-600">Divulgue seus projetos e áreas de interesse</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Atrair estudantes qualificados</span>
                                        <p class="text-sm text-gray-600">Estudantes interessados encontram você facilmente</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Facilitar o primeiro contato</span>
                                        <p class="text-sm text-gray-600">Suas informações de contato ficam visíveis</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-3 text-2xl flex-shrink-0">✓</span>
                                    <div>
                                        <span class="font-semibold text-gray-800">Aumentar visibilidade</span>
                                        <p class="text-sm text-gray-600">Seus projetos acessíveis 24/7 online</p>
                                    </div>
                                </li>
                            </ul>

                            <div class="mt-6 bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-lg text-center">
                                <p class="font-semibold">💡 Dica:</p>
                                <p class="text-sm">Quanto mais completo seu perfil, mais estudantes interessados você atrai!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Como funciona a conexão -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
                    <h3 class="text-3xl font-bold text-center mb-6">🤝 Como Acontece a Conexão?</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold text-blue-600">
                                1
                            </div>
                            <h4 class="text-xl font-bold mb-2">Estudante Busca</h4>
                            <p class="text-blue-100">O estudante pesquisa por área de interesse ou professor específico</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold text-purple-600">
                                2
                            </div>
                            <h4 class="text-xl font-bold mb-2">Encontra Professor</h4>
                            <p class="text-purple-100">Visualiza perfil completo com linhas de pesquisa e contato</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold text-green-600">
                                3
                            </div>
                            <h4 class="text-xl font-bold mb-2">Faz Contato</h4>
                            <p class="text-blue-100">Entra em contato direto e inicia a parceria acadêmica</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Por Que Usar o PesqHub?</h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-purple-600 mx-auto"></div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:shadow-xl transition-shadow">
                        <div class="text-5xl mb-4">⚡</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Rápido e Fácil</h3>
                        <p class="text-gray-700">Encontre oportunidades de pesquisa em minutos, não em semanas</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:shadow-xl transition-shadow">
                        <div class="text-5xl mb-4">🌐</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Acesso Universal</h3>
                        <p class="text-gray-700">Qualquer dispositivo, qualquer hora, qualquer lugar</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl hover:shadow-xl transition-shadow">
                        <div class="text-5xl mb-4">🎯</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Busca Inteligente</h3>
                        <p class="text-gray-700">Filtros avançados para encontrar exatamente o que procura</p>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl hover:shadow-xl transition-shadow">
                        <div class="text-5xl mb-4">🤝</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Conexão Direta</h3>
                        <p class="text-gray-700">Contato direto entre estudantes e professores</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Comece Agora!</h2>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    @auth
                        Explore as oportunidades disponíveis
                    @else
                        Faça login e comece a explorar
                    @endauth
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    @auth
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-xl transition-all transform hover:scale-105">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Buscar Professores
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-xl transition-all transform hover:scale-105">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Fazer Login
                        </a>
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-blue-600 transition-all">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Professores
                        </a>
                    @endauth
                </div>
                <p class="mt-8 text-blue-100 text-lg">
                    Gratuito • Rápido • Fácil • Sem Complicação
                </p>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1s ease-out;
    }

    html {
        scroll-behavior: smooth;
    }
</style>
@endpush
