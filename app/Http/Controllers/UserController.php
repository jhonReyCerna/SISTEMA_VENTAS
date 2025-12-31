<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::with('role');

        if (request()->filled('nombre')) {
            $query->where('nombre', 'like', '%' . request('nombre') . '%');
        }
        if (request()->filled('email')) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }
        if (request()->filled('rol')) {
            $query->where('id_rol', request('rol'));
        }
        if (request()->filled('estado')) {
            $query->where('estado', request('estado'));
        }

        $usuarios = $query->paginate(10)->appends(request()->query());
        $roles = \App\Models\Role::all();
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        User::create($validated);

        // Obtener roles para la vista después de crear
        $query = User::with('role');
        $usuarios = $query->paginate(10);
        $roles = Role::all();
        return view('usuarios.index', compact('usuarios', 'roles'))->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $usuario->update($validated);

        // Obtener roles para la vista después de actualizar
        $query = User::with('role');
        $usuarios = $query->paginate(10);
        $roles = Role::all();
        return view('usuarios.index', compact('usuarios', 'roles'))->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();

        // Obtener roles para la vista después de eliminar
        $query = User::with('role');
        $usuarios = $query->paginate(10);
        $roles = Role::all();
        return view('usuarios.index', compact('usuarios', 'roles'))->with('success', 'Usuario eliminado exitosamente');
    }
}
