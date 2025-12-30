<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:150',
        ]);

        Role::create($validated);
        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:150',
        ]);

        $role->update($validated);
        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente');
    }
}
