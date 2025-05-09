<?php
session_start();

// Check if this is an OAuth callback
if (isset($_GET['action']) && $_GET['action'] === 'oauth_callback') {
    // For OAuth callbacks, ensure session is started and set login status
    $_SESSION['oauth_pending'] = true;
    $_SESSION['loggedin'] = true;

    // Get user data from Supabase
    $supabase = json_decode(file_get_contents('php://input'), true);
    if ($supabase && isset($supabase['event']) && $supabase['event'] === 'SIGNED_IN') {
        $user = $supabase['user'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['user_metadata']['full_name'] ?? $user['email'];
        $_SESSION['profile_picture'] = $user['user_metadata']['avatar_url'] ?? null;
    }
    
    // Redirect to profile page with full path
    header('Location: profile.php');
    exit;
}

// For regular AJAX requests
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

switch ($data['action']) {
    case 'login':
        // Validate required data
        if (!isset($data['user']) || !isset($data['session'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing user or session data']);
            exit;
        }

        // Store user data in PHP session
        $_SESSION['user_id'] = $data['user']['id'];
        $_SESSION['user_email'] = $data['user']['email'];
        $_SESSION['user_name'] = $data['user']['name'];
        $_SESSION['loggedin'] = true;
        $_SESSION['supabase_session'] = $data['session'];
        
        // Store profile picture if available from Google
        if (isset($data['user']['user_metadata']['avatar_url'])) {
            $_SESSION['profile_picture'] = $data['user']['user_metadata']['avatar_url'];
        }

        // Store additional user data in Supabase if needed
        // You can add code here to store extra user information in your Supabase tables

        echo json_encode(['success' => true]);
        break;

    case 'logout':
        // Clear all session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();

        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
