<?php
$servername = "localhost"; // Cambia si no usas localhost
$username = "root"; // Cambia por tu usuario de MySQL
$password = ""; // Cambia por tu contraseña de MySQL
$dbname = "movistarpuntos"; // Cambia por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

