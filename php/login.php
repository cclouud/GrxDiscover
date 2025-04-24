<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrxDisc - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/estilosinicio.css"> <!-- Assuming estilosinicio.css has relevant styles -->
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>



    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
        </form>
        <p class="text-center">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
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

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $email = $_POST['email'];
                $password = $_POST['password'];

                // Prepare and execute the query
                $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $name, $hashed_password);
                    $stmt->fetch();

                    // Verify the password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_name'] = $name; // Store user's name in session
                        $_SESSION['loggedin'] = true;

                        // Redirect to the map page
                        header("Location: ../mapa.php");
                        exit();
                    } else {
                        $error_message = "Correo electrónico o contraseña incorrectos.";
                    }
                } else {
                    $error_message = "Correo electrónico o contraseña incorrectos.";
                }

                $stmt->close();
            }

            $conn->close();
        ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-handler.js"></script>
</body>
</html>
