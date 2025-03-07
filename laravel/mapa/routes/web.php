<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});
 
Route::get('/google-auth/callback', function () {
    // Obtén la información del usuario desde Google
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Usar updateOrCreate para almacenar o actualizar el usuario
    $user = User::updateOrCreate([ 
        'google_id' => $googleUser->id,  // Usamos $googleUser para obtener el google_id
    ],[
        'name' => $googleUser->name,      // Usamos $googleUser para obtener el nombre
        'email' => $googleUser->email,    // Usamos $googleUser para obtener el email
    ]);

    // Iniciar sesión con el usuario autenticado
    Auth::login($user);

    return redirect('/dashboard');
});







Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
