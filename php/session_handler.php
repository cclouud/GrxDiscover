<?php
// Configurar la duración de la sesión (30 días)
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 30 * 24 * 60 * 60, // 30 días
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_path' => '/'
    ]);
}

// Habilitar reporte de errores para debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Función para logging
function logDebug($message, $data = null) {
    $logFile = __DIR__ . '/oauth_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    if ($data) {
        $logMessage .= "\n" . print_r($data, true);
    }
    $logMessage .= "\n\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Log de la petición
logDebug('Request received', [
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => getallheaders(),
    'get' => $_GET,
    'post' => $_POST,
    'input' => file_get_contents('php://input'),
    'session_id' => session_id(),
    'session_data' => $_SESSION
]);

try {
    // Obtener datos JSON del input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    logDebug('Decoded JSON data', $data);
    
    // Verificar acción
    if (!isset($data['action'])) {
        throw new Exception('No se especificó la acción');
    }
    
    switch ($data['action']) {
        case 'login':
            if (!isset($data['user']) || !isset($data['session'])) {
                throw new Exception('Datos de usuario o sesión incompletos');
            }
            
            // Guardar datos en la sesión
            $_SESSION['user'] = $data['user'];
            $_SESSION['session'] = $data['session'];
            $_SESSION['last_activity'] = time();
            $_SESSION['loggedin'] = true;
            
            // Regenerar ID de sesión para mayor seguridad
            session_regenerate_id(true);
            
            // Establecer cookie de sesión
            setcookie(session_name(), session_id(), [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/GrxDiscover',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            logDebug('Session data stored', [
                'user' => $data['user'],
                'session_id' => session_id()
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Sesión iniciada correctamente',
                'session_id' => session_id()
            ]);
            break;
            
        case 'logout':
            // Limpiar la sesión
            $_SESSION = array();
            
            // Destruir la cookie de sesión
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', [
                    'expires' => time() - 3600,
                    'path' => '/GrxDiscover',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            
            session_destroy();
            
            logDebug('Session destroyed');
            
            echo json_encode([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);
            break;
            
        case 'check':
            // Verificar si hay una sesión activa y si no ha expirado
            $is_logged_in = isset($_SESSION['user']) && 
                           isset($_SESSION['last_activity']) && 
                           isset($_SESSION['loggedin']) &&
                           $_SESSION['loggedin'] === true &&
                           (time() - $_SESSION['last_activity'] < 30 * 24 * 60 * 60);
            
            if ($is_logged_in) {
                // Actualizar timestamp de última actividad
                $_SESSION['last_activity'] = time();
                
                // Renovar la cookie de sesión
                setcookie(session_name(), session_id(), [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/GrxDiscover',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            
            $response = [
                'success' => true,
                'is_logged_in' => $is_logged_in,
                'session_id' => session_id()
            ];
            
            if ($is_logged_in) {
                $response['user'] = $_SESSION['user'];
            }
            
            logDebug('Session check', $response);
            
            echo json_encode($response);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    logDebug('Error occurred', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
