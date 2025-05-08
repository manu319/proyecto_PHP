<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";  // Ajusta según tus credenciales
$dbname = "gamecore";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión es exitosa
if ($conn->connect_error) {
    // Si la conexión falla, mostrar un mensaje de error y detener la ejecución
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8 para evitar problemas con caracteres especiales
$conn->set_charset("utf8");
?>
