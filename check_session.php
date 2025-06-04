<?php
session_start();
header('Content-Type: application/json');

// Verificar si la sesión está activa y tiene los datos necesarios
$response = [
    'loggedin' => isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id']),
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null
];

echo json_encode($response); 