<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 30 * 24 * 60 * 60,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_path' => '/'
    ]);
}

// Limpiar todas las variables de sesión
$_SESSION = array();

// Eliminar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => '/GrxDiscover',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Destruir la sesión
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cerrando sesión...</title>
    <!-- Supabase -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="../config/supabase.js"></script>
    <script type="module">
        import { initSupabase } from '../config/supabase.js';
        const supabase = initSupabase();
        supabase.auth.signOut().finally(() => {
            window.location.replace('../index.php');
        });
    </script>
</head>
<body>
    <p>Cerrando sesión...</p>
</body>
</html>
