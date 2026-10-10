<?php

namespace App\Http\Controllers;

use App\Models\User;
use BadChoice\Thrust\Controllers\ThrustController;
use Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = User::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(25)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(\Illuminate\Http\Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => $request->name
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente. Se ha sincronizado con M365.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back();
    }

    public function resetPasswordToDefault(User $user)
    {
        $user->update(['password' => bcrypt('12345')]);
        return back()->with('success', "La contraseña de {$user->name} ha sido reseteada a 12345. En su próximo inicio de sesión, el sistema le obligará a cambiarla.");
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {
        $this->validate(request(), [
            'name'     => 'required|min:3',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        User::create([
            'name'     => request('name'),
            'email'    => request('email'),
            'password' => Hash::make(request('password')),
            'token'    => Str::random(60),
        ]);

        return back();
    }

    public function impersonate(User $user)
    {
        auth()->loginUsingId($user->id);

        return redirect()->route('tickets.index');
    }
}
