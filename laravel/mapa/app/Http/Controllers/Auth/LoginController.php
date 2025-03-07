<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\Models\User;

class LoginController extends Controller
{
    // Redirige al usuario a Google para la autenticación
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
        
        // Lógica para verificar si el usuario existe en la base de datos o crear uno nuevo
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
                // Agrega más campos según sea necesario
            ]
        );

        // Loguear al usuario
        Auth::login($user);

        // Redirigir a la página principal
        return redirect()->route('home');
    }

    // Redirige al usuario a Facebook para la autenticación
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Maneja la respuesta de Facebook
    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();
        
        // Lógica para verificar si el usuario existe en la base de datos o crear uno nuevo
        $user = User::firstOrCreate(
            ['email' => $facebookUser->getEmail()],
            [
                'name' => $facebookUser->getName(),
                'avatar' => $facebookUser->getAvatar(),
                // Agrega más campos según sea necesario
            ]
        );

        // Loguear al usuario
        Auth::login($user);

        // Redirigir a la página principal
        return redirect()->route('home');
    }
}
