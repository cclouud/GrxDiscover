<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // If not logged in, redirect to login page
        header('Location: login.php');
        exit();
    }

    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "grxdisc";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $userName = '';
    $userEmail = '';
    $error_message = '';
    $success_message = '';
    $profilePicturePath = '../uploads/profile_pictures/chinese.png'; // Default placeholder

    // Fetch user data including profile picture path
    // **ASSUMPTION: 'profile_picture_path' column exists in 'users' table**
    $stmt = $conn->prepare("SELECT name, email, profile_picture_path FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($userName, $userEmail, $dbProfilePicturePath);
    $stmt->fetch();
    $stmt->close();

    if ($dbProfilePicturePath) {
        $profilePicturePath = $dbProfilePicturePath;
    }

    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update_profile'])) {
            // Handle name and email update
            $newName = htmlspecialchars($_POST['name']);
            $newEmail = htmlspecialchars($_POST['email']);
            
            // Preserve existing profile picture path if no new picture is uploaded
            $currentProfilePicturePath = $dbProfilePicturePath;

            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_picture'];
                $uploadDir = '/home/cloud/Documentos/htdocs/Trabajos/GrxDisc/uploads/profile_pictures/';
                $allowedTypes = ['jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error_message = "File upload error: " . $file['error']; // Report specific error code
                } else {
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $fileType = finfo_file($fileInfo, $file['tmp_name']);

                    $fileExtension = array_search($fileType, $allowedTypes, true);

                    if ($fileExtension === false) {
                        $error_message = "Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.";
                    } elseif ($file['size'] > $maxFileSize) {
                        $error_message = "File size exceeds the maximum limit of 5MB.";
                    } else {
                        $fileName = uniqid('pfp_', true) . '.' . $fileExtension;
                        $destination = $uploadDir . $fileName;

                        // Add more detailed error reporting
                        if (!is_dir($uploadDir)) {
                            $error_message = "Upload directory does not exist: " . $uploadDir;
                        }elseif (move_uploaded_file($file['tmp_name'], $destination)) {
                            $webPath = '../uploads/profile_pictures/' . $fileName;
                            $profilePicturePath = $webPath;
                            $currentProfilePicturePath = $webPath; // Update current path
                            $success_message = "Profile picture uploaded successfully.";
                        }
                    }
                }
            }

            // Validate email format
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Formato de correo electrónico inválido.";
            } else {
                // Check if email already exists for another user
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $newEmail, $user_id);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "El correo electrónico ya está registrado por otro usuario.";
                } else {
                    $stmt->close();
                    // Update user data including profile picture path
                    if ($currentProfilePicturePath) {
                        // **ASSUMPTION: 'profile_picture_path' column exists in 'users' table**
                        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_picture_path = ? WHERE id = ?");
                        $stmt->bind_param("sssi", $newName, $newEmail, $currentProfilePicturePath, $user_id);
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                        $stmt->bind_param("ssi", $newName, $newEmail, $user_id);
                    }


                    if ($stmt->execute()) {
                        $_SESSION['user_name'] = $newName;
                        $_SESSION['user_email'] = $newEmail;
                        if ($currentProfilePicturePath) {
                            $_SESSION['profile_picture_path'] = $currentProfilePicturePath;
                            $profilePicturePath = $currentProfilePicturePath; // Update for display
                        }
                        if (empty($success_message)) {
                            $success_message = "Profile updated successfully.";
                        }
                        $userName = $newName; // Update displayed name
                        $userEmail = $newEmail; // Update displayed email
                    } else {
                        $error_message = "Error updating profile: " . $conn->error;
                    }
                }
                $stmt->close();
            }
        } elseif (isset($_POST['update_password'])) { // This part seems to be for a separate password form, but the current HTML has password fields in the main form. I will keep this logic for now but the HTML structure might need adjustment if password update is part of the main form submission.
            // Handle password update
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            // Fetch current hashed password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (!password_verify($currentPassword, $hashed_password)) {
                $error_message = "La contraseña actual es incorrecta.";
            } elseif ($newPassword !== $confirmPassword) {
                $error_message = "Las nuevas contraseñas no coinciden.";
            } else {
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $newHashedPassword, $user_id);

                if ($stmt->execute()) {
                    $success_message = "Contraseña actualizada correctamente.";
                } else {
                    $error_message = "Error al actualizar la contraseña: " . $conn->error;
                }
                $stmt->close();
            }
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrxDisc - Perfil de <?php echo htmlspecialchars($userName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <div class="profile-page-container">
        <div class="profile-header-section">
            <div class="back-link">
                <a href="#" onclick="history.back();" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div class="header-title">
                <h2>Profile</h2>
            </div>
            <div class="header-icons">
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <div class="profile-content-section">

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

            <!-- View Mode -->
            <div id="view-mode">
                <div class="profile-overview">
                    <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" alt="Profile Picture" class="profile-pic">
                    <div class="profile-basic-info">
                        <h3 class="profile-name"><?php echo htmlspecialchars($userName); ?></h3>
                        <p class="profile-email"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($userEmail); ?></p>
                        <p class="profile-phone"><i class="fas fa-phone me-2"></i>N/A</p> <!-- Placeholder Phone -->
                    </div>
                    <div class="profile-buttons-mobile">
                        <button id="edit-profile-btn" class="btn btn-primary">Edit Profile</button>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm mobile-logout-btn">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Edit Mode -->
            <div id="edit-mode" style="display: none;">
                <div class="profile-details-form">
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nick_name" class="form-label">Nick Name</label>
                                    <input type="text" class="form-control" id="nick_name" name="nick_name" value="<?php echo htmlspecialchars($userName); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                        </div>

                        <div class="my-email-section mt-4">
                            <h4>Email Address</h4>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                            </div>
                        </div>

                        <div class="password-change-fields mt-4">
                            <h4>Change Password</h4>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password:</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password:</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" id="cancel-edit-btn" class="btn btn-secondary ms-2">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-handler.js"></script>
    <script>
        const viewMode = document.getElementById('view-mode');
        const editMode = document.getElementById('edit-mode');
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');

        editProfileBtn.addEventListener('click', () => {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
        });

        cancelEditBtn.addEventListener('click', () => {
            editMode.style.display = 'none';
            viewMode.style.display = 'block';
        });
    </script>
</body>
</html>
