<?php
session_start();
header('Content-Type: application/json');

// Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Recoger datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || empty($data['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Falta user_id']);
    exit;
}

// Guardar en la sesión PHP
$_SESSION['loggedin'] = true;
$_SESSION['user_id'] = $data['user_id'];
if (isset($data['username'])) {
    $_SESSION['username'] = $data['username'];
}

// Puedes añadir más campos si lo necesitas

echo json_encode(['success' => true, 'message' => 'Sesión sincronizada']); 