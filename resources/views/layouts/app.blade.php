<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    {{-- Laddar Tailwind via Vite --}}
    @vite(['resources/css/app.css','resources/js/app.js'])


    <script>
        (function () {
            try {
                const ls = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const shouldDark = ls ? (ls === 'dark') : prefersDark;
                if (shouldDark) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            } catch (_) {
            }
        })();
    </script>
    {{-- Vite efter snippeten --}}
    @vite(['resources/css/app.css','resources/js/app.js'])


</head>
<body class="bg-white text-black dark:bg-gray-800 dark:text-white">
<header class="bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-4">
    <h1 class="text-xl font-bold">Välkommen till Landlord Panelen!</h1>
</header>
<nav class="bg-white dark:bg-gray-500 border-b shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="font-semibold">🏠 {{ config('app.name') }}</a>
        <div class="space-x-3">
            <a class="text-sm text-gray-700 dark:text-gray-100 hover:underline"
               href="{{ url('/landlord') }}">Landlord</a>
            <a class="text-sm text-gray-700 dark:text-gray-100 hover:underline"
               href="{{ url('/landlord/tenants/create') }}">Ny tenant</a>
            <button id="toggleDarkMode"
                    class="fixed top-4 right-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 dark:bg-yellow-500 dark:hover:bg-yellow-700">
                Toggle Dark Mode
            </button>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const htmlElement = document.documentElement;
        const button = document.getElementById('toggleDarkMode');
        const currentTheme = localStorage.getItem('theme') || 'light';

        // Sätt initialt tema
        if (currentTheme === 'dark') {
            console.log("du har mörkt tema");
            htmlElement.classList.add('dark');
        } else {
            console.log("du har ljust tema");
            htmlElement.classList.remove('dark');
        }

        button.addEventListener('click', () => {
            const isDarkMode = htmlElement.classList.contains('dark');
            if (isDarkMode) {
                console.log("byter till ljust tema");
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                console.log("byter till mörkt tema");
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    });
</script>

</body>
</html>
