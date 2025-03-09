<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('inicio'); // Asegúrate de que tienes una vista llamada 'inicio.blade.php'
    }
}