<?php
session_start();
require_once('../includes/db.php');  // Asegúrate de que la ruta sea correcta

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener el ID del usuario y de la meta
$user_id = $_SESSION['user']['id'];
$goal_id = $_GET['id'] ?? null;

if (!$goal_id) {
    die("ID de meta no especificado.");
}

// Eliminar la meta si pertenece al usuario
$query = "DELETE FROM metas WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $goal_id, $user_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Meta eliminada correctamente.";
} else {
    $_SESSION['message'] = "Error al eliminar la meta.";
}

header("Location: goals.php");
exit();
?>
