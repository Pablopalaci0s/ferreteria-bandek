<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContrasenaTemporal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->string('buscar')->toString();

                $q->where(function ($sub) use ($buscar) {
                    $sub->where('name', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('usuarios', 'email')],
            'password' => ['required', Password::defaults()],
            'rol' => ['required', Rule::in(['admin', 'vendedor'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'],
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'password' => ['nullable', Password::defaults()],
            'rol' => ['required', Rule::in(['admin', 'vendedor'])],
        ]);

        if ($validated['rol'] === 'vendedor' && $usuario->rol === 'admin') {
            $totalAdmins = User::where('rol', 'admin')->count();

            if ($totalAdmins <= 1) {
                return back()->with('error', 'No podés quitarle el rol de admin al último administrador.')->withInput();
            }
        }

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->rol = $validated['rol'];

        if (! empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function resetPassword(User $usuario)
    {
        $temporal = Str::password(12);

        $usuario->update([
            'password' => Hash::make($temporal),
            'must_change_password' => true,
        ]);

        Mail::to($usuario->email)->send(new ContrasenaTemporal($usuario, $temporal));

        return redirect()->route('admin.usuarios.index')
            ->with('status', "Se generó una contraseña temporal y se envió por correo a {$usuario->email}.");
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No podés eliminar tu propio usuario.');
        }

        if ($usuario->rol === 'admin') {
            $totalAdmins = User::where('rol', 'admin')->count();

            if ($totalAdmins <= 1) {
                return back()->with('error', 'No podés eliminar al último administrador.');
            }
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('status', 'Usuario eliminado correctamente.');
    }
}