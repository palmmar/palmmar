<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - User Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
                <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Users
                </a>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- User Info Column -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Personal Information</h2>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">Address</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->address }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">City</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->city }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">Zipcode</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->zipcode }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">Email Verified</label>
                        <p class="mt-1">
                            @if($user->email_verified_at)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                    </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Not Verified
                                    </span>
                            @endif
                        </p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-500">Created At</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->created_at->format('F d, Y g:i A') }}</p>
                    </div>

                    <div class="pb-4">
                        <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->updated_at->format('F d, Y g:i A') }}</p>
                    </div>
                </div>

                <!-- Pollen Data Column -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Local Pollen Levels</h2>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                        @if(isset($pollenData['error']))
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Pollen data temporarily unavailable</p>
                            </div>
                        @else
                            <!-- Overall Level -->
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-md mb-3">
                                    <span class="text-3xl">🌸</span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $pollenData['overall_level'] }}</h3>
                                <p class="text-sm text-gray-600">Overall Pollen Level</p>
                            </div>

                            <!-- Pollen Types -->
                            @if(!empty($pollenData['types']))
                                <div class="space-y-3 mb-4">
                                    @foreach($pollenData['types'] as $type)
                                        <div class="bg-white rounded-lg p-4 shadow-sm">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-semibold text-gray-700">{{ $type['name'] }}</span>
                                                <span class="font-bold {{ $type['color'] }}">{{ $type['level'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-blue-400 to-indigo-500 h-2 rounded-full"
                                                     style="width: {{ ($type['value'] / 5) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Last Updated -->
                            @if(isset($pollenData['last_updated']))
                                <div class="text-center pt-4 border-t border-blue-200">
                                    <p class="text-xs text-gray-500">
                                        Last updated: {{ \Carbon\Carbon::parse($pollenData['last_updated'])->diffForHumans() }}
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Location Info -->
                    <div class="mt-4 bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Showing data for {{ $user->city }}, {{ $user->zipcode }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('users.edit', $user) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    Edit User
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
