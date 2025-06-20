<?php
// Archivo de configuración PHP que genera variables JavaScript
// Este archivo lee las variables de entorno desde .env y las hace disponibles para JavaScript

header('Content-Type: application/javascript');

// Cargar variables desde .env si existe
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file_get_contents(__DIR__ . '/.env');
    $envVars = [];
    
    foreach (explode("\n", $envFile) as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value, '"\'');
        }
    }
    
    // Hacer las variables disponibles como $_ENV
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Obtener variables de entorno o usar valores por defecto
$supabaseUrl = $_ENV['SUPABASE_URL'] ?? $_SERVER['SUPABASE_URL'] ?? 'https://tu-proyecto.supabase.co';
$supabaseKey = $_ENV['SUPABASE_KEY'] ?? $_SERVER['SUPABASE_KEY'] ?? 'tu-supabase-anon-key-aqui';
$unsplashKey = $_ENV['UNSPLASH_ACCESS_KEY'] ?? $_SERVER['UNSPLASH_ACCESS_KEY'] ?? 'tu-unsplash-access-key-aqui';
$pixabayKey = $_ENV['PIXABAY_API_KEY'] ?? $_SERVER['PIXABAY_API_KEY'] ?? 'tu-pixabay-api-key-aqui';

// Generar JavaScript con las variables
echo "// Configuración generada automáticamente desde variables de entorno\n";
echo "window.SUPABASE_CONFIG = {\n";
echo "    url: '$supabaseUrl',\n";
echo "    key: '$supabaseKey'\n";
echo "};\n";
echo "window.API_KEYS = {\n";
echo "    unsplash: '$unsplashKey',\n";
echo "    pixabay: '$pixabayKey'\n";
echo "};\n";
?> 