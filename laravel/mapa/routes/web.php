<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;



// Ruta para la vista de inicio (donde está el botón de Google)
Route::get('/', function () {
    return view('inicio'); // Asegúrate de que tu archivo 'inicio.blade.php' esté en resources/views
})->name('inicio');

// Ruta para redirigir a Google para la autenticación
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider'])->name('google.login');

// Ruta de callback después de la autenticación de Google
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('google.callback');

// Ruta para el mapa (después de la autenticación)
Route::get('/mapa', function () {
    return view('mapa'); // Asegúrate de que tu archivo 'mapa.blade.php' esté en resources/views
})->name('mapa');

// Ruta para mostrar el formulario de registro
Route::get('/registro', [AuthController::class, 'showRegisterForm'])->name('registro');

// Ruta para manejar el registro
Route::post('/registro', [AuthController::class, 'registro'])->name('registro.store');

// Ruta para el dashboard (solo para usuarios autenticados)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Requiere el archivo de autenticación
require __DIR__.'/auth.php';
