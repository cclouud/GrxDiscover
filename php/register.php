<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrxDisc - Registrarse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/estilosinicio.css"> <!-- Assuming estilosinicio.css has relevant styles -->
    <link rel="stylesheet" href="../css/login.css"> <!-- Using login.css for shared form styles -->
</head>
<body>
    <div class="login-container"> <!-- Using the same container class for consistent styling -->
        <h2>Registrarse</h2>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Registrarse</button>
        </form>
        <p class="text-center">¿Ya tienes cuenta? <a href="login.php">Inicia Sesión aquí</a></p>
        <?php
            session_start();

            // Database connection details
            $servername = "localhost"; // Replace with your database server name
            $username = "root"; // Replace with your database username
            $password = ""; // Replace with your database password
            $dbname = "grxdisc";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $error_message = "";
            $success_message = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $password = htmlspecialchars($_POST['password']);
                $confirm_password = htmlspecialchars($_POST['confirm_password']);

                // Basic validation
                if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                    $error_message = "Todos los campos son obligatorios.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error_message = "Formato de correo electrónico inválido.";
                } elseif ($password !== $confirm_password) {
                    $error_message = "Las contraseñas no coinciden.";
                } else {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        $error_message = "El correo electrónico ya está registrado.";
                    } else {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Insert new user into the database
                        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $name, $email, $hashed_password);

                        if ($stmt->execute()) {
                            $success_message = "Registro exitoso. Ahora puedes iniciar sesión.";
                            // Optionally redirect to login page after a delay
                            header("Location: login.php");
                            exit();
                            // header("refresh:3;url=login.php");
                        } else {
                            $error_message = "Error al registrar usuario: " . $conn->error;
                        }
                    }
                    $stmt->close();
                }
            }

            $conn->close();
        ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-handler.js"></script>
</body>
</html>
