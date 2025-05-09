<?php
session_start();

// Get JSON data from request body
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
