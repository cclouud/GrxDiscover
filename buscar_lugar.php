<?php
// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grx";

// Crear conexión
//$conn = new mysqli($servername, $username, $password, $dbname);
try{
    $conn = new mysqli($servername, $username, $password, $dbname);
}
catch(Exception $e){
    echo $e;
}
//$conn = mysqli_connect($servername,$username,$password,$dbname);

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';

$sql = "SELECT * FROM lugares WHERE nombre ='Alhambra'";
$res = $conn->query($sql);
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("s", $nombre);
// $stmt->execute();
// $result = $stmt->get_result();
// print_r($result);


// Verificar si se encontró el lugar
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    // Retornar los datos como JSON
    echo json_encode(['latitud' => $row['latitud'], 'longitud' => $row['longitud']]);
} else {
    echo json_encode(['error' => 'Lugar no encontrado']);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
