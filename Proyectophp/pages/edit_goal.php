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

// Consultar los datos actuales de la meta
$query = "SELECT * FROM metas WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $goal_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Meta no encontrada o no tienes permiso para editarla.");
}

$meta = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_limite = $_POST['fecha_limite'];

    // Validar campos
    if (empty($descripcion) || empty($fecha_inicio) || empty($fecha_limite)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Actualizar la meta
        $query = "UPDATE metas SET descripcion = ?, fecha_inicio = ?, fecha_limite = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssii", $descripcion, $fecha_inicio, $fecha_limite, $goal_id, $user_id);

        if ($stmt->execute()) {
            header("Location: goals.php");
            exit();
        } else {
            $error = "Error al actualizar la meta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Meta</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h1 {
            color: #4CAF50;
            text-align: center;
            margin-top: 20px;
        }

        /* Contenedor principal */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Estilo del formulario */
        form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="date"],
        form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Estilo para el mensaje de error */
        .error {
            color: red;
            margin-bottom: 15px;
        }

        /* Estilo del enlace */
        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Editar Meta</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" value="<?php echo htmlspecialchars($meta['descripcion']); ?>" required>

            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo $meta['fecha_inicio']; ?>" required>

            <label for="fecha_limite">Fecha Límite:</label>
            <input type="date" name="fecha_limite" id="fecha_limite" value="<?php echo $meta['fecha_limite']; ?>" required>

            <input type="submit" value="Guardar Cambios">
        </form>

        <br>
        <a href="goals.php">Volver a la lista de metas</a>
    </div>

</body>
</html>
