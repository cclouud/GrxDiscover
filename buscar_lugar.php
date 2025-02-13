<?php
// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grx";

// Crear conexión
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
} catch (Exception $e) {
    // Enviar un mensaje de error si la conexión falla
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// Obtener el nombre del lugar desde el parámetro GET
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';

// Consulta a la base de datos para buscar el lugar por nombre
$sql = "SELECT * FROM lugares WHERE nombre LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre);  // Vincula el parámetro de búsqueda
$stmt->execute();
$res = $stmt->get_result();

// Verificar si se encontró el lugar
if ($res->num_rows > 0) {
    // Crear el array de resultados
    $lugares = [];
    while ($row = $res->fetch_assoc()) {
        $lugares[] = [
            'nombre' => $row['nombre'],
            'latitud' => $row['latitud'],
            'longitud' => $row['longitud']
        ];
    }
    // Retornar los resultados como JSON
    echo json_encode(['lugares' => $lugares]);
} else {
    echo json_encode(['error' => 'Lugar no encontrado']);
}

// Cerrar la conexión y la declaración
$stmt->close();
$conn->close();
?>
