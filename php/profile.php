<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrxDisc - Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <div class="profile-container">
        <?php
            // Start the session
            session_start();

            // Check if the user is logged in
            if (!isset($_SESSION['user_email'])) {
                // If not logged in, redirect to login page
                header('Location: ../login.php');
                exit();
            }

            // Assuming user's name is stored in session
            $userName = $_SESSION['user_name'] ?? 'Usuario'; // Default to 'Usuario' if name not in session
        ?>
        <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=<?php echo urlencode($userName); ?>&backgroundColor=b6e3f4" alt="User Profile Picture" class="profile-pic">
        <h2 class="profile-name"><?php echo htmlspecialchars($userName); ?></h2>
        <p>This is a basic profile preview.</p>
        <a href="logout.php" class="btn btn-danger mt-3">Cerrar Sesión</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-handler.js"></script>
</body>
</html>
