<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'          => ['required', 'confirmed', Rules\Password::min(6)],
            'pais'              => ['nullable', 'string', 'max:100'],
            'telefono'          => ['nullable', 'string', 'max:20'],
            'fecha_nacimiento'  => ['nullable', 'date', 'before:today'],
            'direccion'         => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'rol'               => 'guest',
            'pais'              => $request->pais,
            'telefono'          => $request->telefono,
            'fecha_nacimiento'  => $request->fecha_nacimiento,
            'direccion'         => $request->direccion,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('guest.dashboard');
    }
}
