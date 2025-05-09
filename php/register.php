<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrxDisc - Registro</title>
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
        <h2>Crear Cuenta</h2>
        <form id="registerForm">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
            </div>
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
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
            </button>
        </form>

        <div class="or-separator">O</div>

        <div class="social-login mb-3">
            <button onclick="signInWithGoogle()" class="btn btn-outline-dark w-100">
                <i class="fab fa-google me-2"></i>Continuar con Google
            </button>
        </div>

        <p class="text-center">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>

        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
    </div>

    <script>
        // Handle form submission
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault()
            
            const name = document.getElementById('name').value
            const email = document.getElementById('email').value
            const password = document.getElementById('password').value
            const confirmPassword = document.getElementById('confirmPassword').value
            const errorDiv = document.getElementById('error-message')
            
            // Validate passwords match
            if (password !== confirmPassword) {
                errorDiv.textContent = 'Las contraseñas no coinciden'
                errorDiv.classList.remove('d-none')
                return
            }

            try {
                // Sign up with Supabase
                const { data, error } = await supabase.auth.signUp({
                    email: email,
                    password: password,
                    options: {
                        data: {
                            name: name
                        }
                    }
                })

                if (error) throw error

                // Get user details after successful registration
                const { data: { user } } = await supabase.auth.getUser()
                
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
                            name: name
                        },
                        session: data.session
                    })
                })

                if (!response.ok) throw new Error('Error al registrar usuario')

                // Redirect after successful registration
                window.location.href = '../index.php'

            } catch (error) {
                errorDiv.textContent = 'Error al registrar: ' + error.message
                errorDiv.classList.remove('d-none')
            }
        })

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-handler.js"></script>
</body>
</html>
