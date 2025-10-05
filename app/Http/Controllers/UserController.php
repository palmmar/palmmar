<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PollenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private PollenService $pollenService
    ) {}

    public function index()
    {
        $per_page = (int)request('per_page', 25);
        $per_page = in_array($per_page, [10, 25, 50, 100]) ? $per_page : 25;

        $query = User::query();

        if ($q = request('q')) {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (request()->filled('verified')) {
            request('verified') === '1'
                ? $query->whereNotNull('email_verified_at')
                : $query->whereNull('email_verified_at');
        }

        $sort = in_array(request('sort'), ['name', 'email', 'email_verified_at', 'created_at'])
            ? request('sort')
            : 'name';
        $dir = request('dir') === 'desc' ? 'desc' : 'asc';

        $users = $query->orderBy($sort, $dir)->paginate($per_page);

        return view('users.index', ['users' => $users]);
    }

    public function create(): \Illuminate\View\View
    {
        return view('users.create');
    }

    public function store(): \Illuminate\Http\RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'zipcode' => ['required', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'address' => $validated['address'],
            'city' => $validated['city'],
            'zipcode' => $validated['zipcode'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $pollenData = $this->pollenService->getPollenData($user->city);

        return view('users.show', [
            'user' => $user,
            'pollenData' => $pollenData,
        ]);
    }
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(User $user): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:5', 'confirmed'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'zipcode' => ['required', 'string', 'max:20'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->address = $validated['address'];
        $user->city = $validated['city'];
        $user->zipcode = $validated['zipcode'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

}
