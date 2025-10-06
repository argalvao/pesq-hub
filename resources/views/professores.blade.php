<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Professores</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-3xl font-bold mb-6">Nossos Professores</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($professores as $professor)
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-indigo-700">{{ $professor['nome'] }}</h2>
                <p class="text-gray-600">{{ $professor['curso'] }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $professor['email'] }}</p>
            </div>
        @endforeach
        </div>

</body>
</html>