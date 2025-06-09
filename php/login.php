<?php
// Agregar headers necesarios
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data:;");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrxDisc - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/estilosinicio.css">
    <link rel="stylesheet" href="../css/login.css">
    <!-- Supabase -->
    <script type="module">
        import { initSupabase } from '../config/supabase.js';
        
        // Make supabase instance available globally
        const supabase = initSupabase();
        window.supabase = supabase;
        
        // Make login function available globally
        window.signInWithGoogle = async () => {
            try {
                const { data, error } = await supabase.auth.signInWithOAuth({
                    provider: 'google',
                    options: {
                        redirectTo: window.location.origin + '/GrxDiscover/php/oauth_callback.php' + window.location.search,
                        queryParams: {
                            access_type: 'offline',
                            prompt: 'consent'
                        }
                    }
                });
                
                if (error) {
                    document.getElementById('error-message').textContent = 'Error al iniciar sesión con Google: ' + error.message;
                    document.getElementById('error-message').classList.remove('d-none');
                    console.error('Error:', error);
                }
                
                console.log('Auth response:', data);
                
            } catch (error) {
                document.getElementById('error-message').textContent = 'Error al iniciar sesión con Google: ' + error.message;
                document.getElementById('error-message').classList.remove('d-none');
                console.error('Error:', error);
            }
        };
    </script>
</head>
<body>
    <div class="login-container">
        <div class="back-link mb-3">
            <a href="#" onclick="history.back();" class="text-decoration-none">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <h2>Iniciar Sesión</h2>
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
            </button>
        </form>

        <div class="or-separator">O</div>

        <div class="social-login mb-3">
            <button onclick="signInWithGoogle()" class="btn btn-outline-dark w-100">
                <i class="fab fa-google me-2"></i>Continuar con Google
            </button>
        </div>

        <p class="text-center">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>

        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
    </div>

    <script>
        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault()
            
            const email = document.getElementById('email').value
            const password = document.getElementById('password').value
            const errorDiv = document.getElementById('error-message')
            
            try {
                const { data, error } = await supabase.auth.signInWithPassword({
                    email: email,
                    password: password
                })

                if (error) throw error

                // Get user details after successful login
                const { data: { user } } = await supabase.auth.getUser()
                
                // Comprobar si el correo está verificado
                if (!user.email_confirmed_at) {
                    errorDiv.textContent = 'Debes verificar tu correo electrónico antes de iniciar sesión.';
                    errorDiv.classList.remove('d-none');
                    await supabase.auth.signOut();
                    return;
                }
                
                // Start PHP session and store user info
                const response = await fetch('session_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'login',
                        user: {
                            id: user.id,
                            email: user.email,
                            name: user.user_metadata.name || user.email
                        },
                        session: data.session
                    })
                })

                if (!response.ok) throw new Error('Error al iniciar sesión')

                // Redirect after successful login
                const urlParams = new URLSearchParams(window.location.search)
                let redirectUrl = urlParams.get('redirect')
                // Si no hay redirect, ve al index
                if (!redirectUrl) {
                    window.location.href = '../index.php'
                } else {
                    // Si la ruta ya empieza por '/', quítalo para evitar errores
                    redirectUrl = redirectUrl.replace(/^\/+/, '')
                    // Si la ruta no contiene '.php', añade index.php como fallback
                    if (!redirectUrl.endsWith('.php')) {
                        redirectUrl = 'index.php'
                    }
                    // Redirige de forma segura
                    window.location.href = '../' + redirectUrl
                }

            } catch (error) {
                errorDiv.textContent = 'Error al iniciar sesión: ' + error.message
                errorDiv.classList.remove('d-none')
            }
        })


    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-handler.js"></script>
</body>
</html>
