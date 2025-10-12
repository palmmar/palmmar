@extends('layouts.app')
@section('title','Tenants')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Tenants</h1>
        <a href="{{ route('landlord.tenants.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">Ny tenant</a>
    </div>

    <div class="bg-white dark:bg-gray-700 shadow rounded divide-y divide-gray-200 dark:divide-gray-800 transition-colors">
        @forelse($tenants as $t)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="font-mono text-sm text-gray-600 dark:text-gray-300">{{ $t->id }}</div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $t->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $t->db_name }} • {{ $t->db_user }}</div>
                </div>
                <div class="space-x-2">
                    <a href="{{ route('landlord.tenants.provision', $t) }}" class="px-3 py-1 rounded bg-indigo-600 text-white text-sm">Provisionera</a>
                </div>
            </div>
        @empty
            <div class="p-4 text-gray-500 dark:text-gray-400">Inga tenants ännu.</div>
        @endforelse
    </div>
@endsection
