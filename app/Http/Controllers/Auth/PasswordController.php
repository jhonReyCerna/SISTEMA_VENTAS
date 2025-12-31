<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validar los campos
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'La contraseña actual es requerida.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.required' => 'La nueva contraseña es requerida.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ]);

        // Actualizar la contraseña
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Obtener la fecha y hora actual
        $fechaActualizacion = now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] h:mm A');

        return back()->with([
            'status' => 'password-updated',
            'password_updated_at' => $fechaActualizacion
        ]);
    }
}
