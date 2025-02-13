<?php
// permitir peticiones al front
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

//conexion base de datos
$conexion = new mysqli("localhost", "root", "", "grx");
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error en la conexión"]));
}



$categoria = isset($_GET["categoria"]) ? intval($_GET["categoria"]) : 0;  //Se obtiene el valor del parámetro categoria / evitar inyecciones sql

// Preparar y ejecutar la consulta SQL
$sql = "SELECT nombre, latitud, longitud FROM lugares WHERE categoria_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $categoria);
$stmt->execute();

//Obtener los resultados en un array asociativo
$resultado = $stmt->get_result();
$lugares = $resultado->fetch_all(MYSQLI_ASSOC);

//Enviar los datos en formato JSON y cerrar la conexión
echo json_encode($lugares);
$stmt->close();
$conexion->close();
?>
