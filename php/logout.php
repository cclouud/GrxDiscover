<!DOCTYPE html>
<html>
<head>
    <title>Cerrando sesión...</title>
    <!-- Supabase -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="../config/supabase.js"></script>
</head>
<body>
    <script>
        async function handleLogout() {
            try {
                // Initialize Supabase client
                const supabase = initSupabase();
                
                // Sign out from Supabase
                await supabase.auth.signOut();
                
                // Clear PHP session
                const response = await fetch('session_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'logout'
                    })
                });

                if (!response.ok) {
                    throw new Error('Error al cerrar sesión');
                }
                
                // Redirect to home page
                window.location.href = '../index.php';
            } catch (error) {
                console.error('Error during logout:', error);
                // Redirect anyway to ensure user is logged out
                window.location.href = '../index.php';
            }
        }

        // Execute logout when page loads
        handleLogout();
    </script>
</body>
</html>
