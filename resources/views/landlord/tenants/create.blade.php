@extends('layouts.app')
@section('title','Ny tenant')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Ny tenant</h1>

    <form method="post" action="{{ route('landlord.tenants.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium">Tenant ID (subdomän)</label>
            <input name="id" required class="mt-1 w-full border rounded px-3 py-2" placeholder="ex: acme" />
        </div>

        <div>
            <label class="block text-sm font-medium">Namn</label>
            <input name="name" required class="mt-1 w-full border rounded px-3 py-2" />
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">DB name</label>
                <input name="db_name" required class="mt-1 w-full border rounded px-3 py-2" placeholder="ex: saas_acme" />
            </div>
            <div>
                <label class="block text-sm font-medium">Aktiv</label>
                <select name="is_active" class="mt-1 w-full border rounded px-3 py-2">
                    <option value="1" selected>Ja</option>
                    <option value="0">Nej</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">DB user (valfritt)</label>
                <input name="db_user" class="mt-1 w-full border rounded px-3 py-2" placeholder="{{ env('TENANT_DB_USERNAME') }}" />
            </div>
            <div>
                <label class="block text-sm font-medium">DB pass (valfritt)</label>
                <input name="db_pass" class="mt-1 w-full border rounded px-3 py-2" placeholder="(lämna tomt för blankt)" />
            </div>
        </div>

        <div class="border-t pt-4">
            <h2 class="font-semibold mb-2">Admin-användare (valfritt)</h2>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">Admin email</label>
                    <input name="admin_email" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Admin lösenord</label>
                    <input name="admin_password" class="mt-1 w-full border rounded px-3 py-2" placeholder="password" />
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button class="px-4 py-2 rounded bg-blue-600 text-white">Spara & provisionera</button>
            <a href="{{ route('landlord.tenants.index') }}" class="ml-2 text-gray-600">Avbryt</a>
        </div>
    </form>
@endsection
