<?php
session_start();
require_once('../includes/db.php');  // Asegúrate de que la ruta sea correcta

if (isset($_POST['goal_id']) && isset($_POST['completed'])) {
    $goal_id = $_POST['goal_id'];
    $completed = $_POST['completed'];

    // Actualizar el estado de completada en la base de datos
    $query = "UPDATE metas SET completada = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $completed, $goal_id);
    $stmt->execute();

    echo "Meta actualizada";
}
?>
