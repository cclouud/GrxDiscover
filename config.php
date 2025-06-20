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

// Obtener variables de entorno
$supabaseUrl = $_ENV['SUPABASE_URL'] ?? $_SERVER['SUPABASE_URL'] ?? null;
$supabaseKey = $_ENV['SUPABASE_KEY'] ?? $_SERVER['SUPABASE_KEY'] ?? null;

// Validar que las variables críticas para Supabase están presentes.
if (!$supabaseUrl || !$supabaseKey) {
    http_response_code(500);
    // Detener la ejecución y mostrar un error claro en la consola del navegador.
    // Esto previene que la aplicación intente conectar con credenciales inválidas.
    echo "console.error('CRITICAL ERROR: Supabase environment variables (SUPABASE_URL, SUPABASE_KEY) are not configured on the server. The application cannot start.');";
    error_log('Configuration Error: SUPABASE_URL or SUPABASE_KEY are not defined in the environment.');
    exit;
}

$unsplashKey = $_ENV['UNSPLASH_ACCESS_KEY'] ?? $_SERVER['UNSPLASH_ACCESS_KEY'] ?? '';
$pixabayKey = $_ENV['PIXABAY_API_KEY'] ?? $_SERVER['PIXABAY_API_KEY'] ?? '';

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