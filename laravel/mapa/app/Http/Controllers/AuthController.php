<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
        
        // Aquí puedes manejar el login del usuario
        // Comprobar si el usuario existe, crear uno nuevo, etc.
        
        // Ejemplo:
        // Auth::login($user);
        // return redirect()->route('home');
        
        dd($user); // Para ver la información del usuario que devuelve el provider
    }
}
