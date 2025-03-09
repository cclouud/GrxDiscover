<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    // Redirige al proveedor de OAuth (Google, por ejemplo)
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('inicio')->with('error', 'No se pudo obtener la información del usuario');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            Auth::login($user);
        } else {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(uniqid()), // Usamos un password aleatorio
            ]);

            Auth::login($user);
        }

        // Redirige con un mensaje de éxito
        return redirect()->route('mapa')->with('success', '¡Te has registrado con éxito!');
    }

    // Método para mostrar el formulario de registro
    public function showRegisterForm()
    {
        return view('registro');  // Aquí se muestra la vista de registro
    }

    // Método para manejar el registro de nuevos usuarios con correo y contraseña
    public function registro(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Iniciar sesión con el nuevo usuario
        Auth::login($user);

        // Redirigir a la página principal con un mensaje de éxito
        return redirect()->route('mapa')->with('success', '¡Te has registrado con éxito!');
    }

    // Método para mostrar el formulario de inicio de sesión
    public function showLoginForm()
    {
        // Si el usuario ya está autenticado, redirigirlo al mapa
        if (Auth::check()) {
            return redirect()->route('mapa');
        }
        return view('auth.login');
    }

    // Método para manejar el inicio de sesión con correo y contraseña
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Intentar iniciar sesión con las credenciales
        if (Auth::attempt($credentials)) {
            // Si la autenticación es exitosa, redirigir al usuario
            return redirect()->route('mapa');
        }

        // Si la autenticación falla, volver al formulario de login con un mensaje de error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    // Método para cerrar sesión
    public function logout()
    {
        Auth::logout();
        return redirect()->route('inicio'); // Redirige a la página de inicio después de cerrar sesión
    }
}
