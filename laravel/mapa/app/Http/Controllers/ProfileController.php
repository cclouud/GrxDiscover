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
        // Muestra el formulario de edición del perfil, pasando el usuario autenticado
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Llenar el usuario con los datos validados
        $request->user()->fill($request->validated());

        // Si el correo electrónico ha cambiado, se establece a null la fecha de verificación
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Guardar los cambios
        $request->user()->save();

        // Redirigir con mensaje de éxito
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validación de contraseña actual antes de eliminar la cuenta
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Obtener el usuario autenticado
        $user = $request->user();

        // Cerrar sesión
        Auth::logout();

        // Eliminar el usuario de la base de datos
        $user->delete();

        // Invalida la sesión y regenera el token de CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir al inicio
        return Redirect::to('/');
    }
}
