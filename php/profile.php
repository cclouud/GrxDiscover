<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_lifetime' => 30 * 24 * 60 * 60,
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_path' => '/GrxDiscover'
        ]);
    }

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        // If not logged in, redirect to login page
        header('Location: login.php');
        exit();
    }

    // Get user data from session
    $user = $_SESSION['user'];
    $user_id = $user['id'];
    $userName = $user['name'] ?? '';
    $userEmail = $user['email'] ?? '';
    $error_message = '';
    $success_message = '';
    
    // Get profile picture from Google account if available
    $profilePicturePath = $_SESSION['profile_picture'] ?? '../uploads/profile_pictures/chinese.png';

    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update_profile'])) {
            // Handle name and email update
            $newName = htmlspecialchars($_POST['name']);
            $newEmail = htmlspecialchars($_POST['email']);
            
            // Validate email format
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Formato de correo electrónico inválido.";
            } else {
                try {
                    // Update session data only
                    $_SESSION['user_name'] = $newName;
                    $_SESSION['user_email'] = $newEmail;

                    // Update session data
                    $_SESSION['user_name'] = $newName;
                    $_SESSION['user_email'] = $newEmail;
                    
                    $success_message = "Profile updated successfully.";
                    $userName = $newName;
                    $userEmail = $newEmail;
                    
                } catch (Exception $e) {
                    $error_message = "Error updating profile: " . $e->getMessage();
                }
            }
        }
    }
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
                <a href="../index.php" class="text-decoration-none">
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
