<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'grx';
$username = 'root';
$password = 'tu_contraseña';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si se recibieron las coordenadas y el nombre del lugar
    if (isset($_POST['nombre']) && isset($_POST['lat']) && isset($_POST['lon'])) {
        $nombre = $_POST['nombre'];
        $lat = $_POST['lat'];
        $lon = $_POST['lon'];

        // Insertar los datos en la tabla lugares
        $sql = "INSERT INTO lugares (nombre, latitud, longitud) VALUES (:nombre, :lat, :lon)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lon', $lon);
        $stmt->execute();

        // Responder con éxito
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos no recibidos correctamente.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
