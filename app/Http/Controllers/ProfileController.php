<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Recargar el usuario desde la base de datos con la relación role
        $user = $request->user()->fresh(['role']);
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Actualizar nombre
        if ($request->has('name')) {
            $user->nombre = $request->name;
        }
        
        // Actualizar email
        if ($request->has('email')) {
            $user->email = $request->email;
            
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();
        
        // Eliminar foto anterior si existe
        if ($user->foto && file_exists(public_path('storage/fotos/' . $user->foto))) {
            unlink(public_path('storage/fotos/' . $user->foto));
        }
        
        // Guardar nueva foto
        $file = $request->file('foto');
        $filename = time() . '_' . $user->id_usuario . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('storage/fotos'), $filename);
        
        $user->foto = $filename;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
