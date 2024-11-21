<?php
$host = 'localhost';
$usuario = 'root';  // Usualmente "root" en XAMPP
$contraseña = '';   // sin contraseña por ahora
$db = 'mi_sitio';

// Crear conexión
$conn = new mysqli($host, $usuario, $contraseña, $db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

