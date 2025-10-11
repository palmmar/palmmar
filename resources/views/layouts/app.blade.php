<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', config('app.name'))</title>

{{-- Laddar Tailwind via Vite --}}
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-dvh">
<nav class="bg-white border-b shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="font-semibold">🏠 {{ config('app.name') }}</a>
        <div class="space-x-3">
            <a class="text-sm text-gray-700 hover:underline" href="{{ url('/landlord') }}">Landlord</a>
            <a class="text-sm text-gray-700 hover:underline" href="{{ url('/landlord/tenants/create') }}">Ny tenant</a>
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
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
