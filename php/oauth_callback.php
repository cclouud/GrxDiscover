<?php
// Agregar headers necesarios
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data:;");

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 30 * 24 * 60 * 60,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_path' => '/GrxDiscover'
    ]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando inicio de sesión...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <!-- Configuración -->
    <script src="../config.php"></script>
</head>
<body>
    <div class="container">
        <h2>Procesando inicio de sesión...</h2>
        <div class="spinner"></div>
    </div>

    <script type="module">
        import { initSupabase } from '../config/supabase.js';

        const supabase = initSupabase();

        async function finishOAuth() {
            try {
                // Intentar obtener la sesión
                const { data: { session }, error: sessionError } = await supabase.auth.getSession();
                
                if (sessionError) throw sessionError;
                if (!session) throw new Error('No se encontró sesión activa');

                // Verificar el usuario
                const { data: { user }, error: userError } = await supabase.auth.getUser();
                
                if (userError) throw userError;
                if (!user) throw new Error('No se encontró usuario');

                // Guardar la sesión en PHP
                const response = await fetch('session_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: 'login',
                        user: {
                            id: user.id,
                            email: user.email,
                            name: user.user_metadata.name || user.email
                        },
                        session: session
                    })
                });

                if (!response.ok) {
                    throw new Error('Error al guardar la sesión');
                }

                // Redirigir al perfil o a la URL indicada
                const params = new URLSearchParams(window.location.search);
                const redirectUrl = params.get('redirect');
                if (redirectUrl) {
                    window.location.replace(redirectUrl);
                } else {
                    window.location.replace('profile.php');
                }

            } catch (error) {
                // En caso de error, redirigir a login
                window.location.replace('login.php?error=auth_failed');
            }
        }

        // Iniciar el proceso
        finishOAuth();
    </script>
</body>
</html> 