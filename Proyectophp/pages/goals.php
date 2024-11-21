<?php
session_start();
require_once('../includes/db.php');  // Asegúrate de que la ruta sea correcta

// Obtener el mes y el año actuales
$mes_actual = date('m');
$anio_actual = date('Y');

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user']['id']; // Asegúrate de tener 'id' en la sesión

// Consultar las metas para el usuario en el mes actual
$query = "SELECT * FROM metas WHERE usuario_id = ? AND mes = ? AND anio = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $mes_actual, $anio_actual);
$stmt->execute();
$metas = $stmt->get_result();

// Contar el número total de metas y las completadas
$query_completadas = "SELECT COUNT(*) FROM metas WHERE usuario_id = ? AND mes = ? AND anio = ? AND completada = 1";
$stmt_completadas = $conn->prepare($query_completadas);
$stmt_completadas->bind_param("iii", $user_id, $mes_actual, $anio_actual);
$stmt_completadas->execute();
$result_completadas = $stmt_completadas->get_result();
$row_completadas = $result_completadas->fetch_row();
$completadas = $row_completadas[0];

// Total de metas
$total_metas = $metas->num_rows;

// Calcular el porcentaje de metas completadas
$porcentaje = $total_metas > 0 ? ($completadas / $total_metas) * 100 : 0;

// Acción para agregar una nueva meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'])) {
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_limite = $_POST['fecha_limite'];

    // Inserta la nueva meta en la base de datos
    $query = "INSERT INTO metas (usuario_id, descripcion, mes, anio, fecha_inicio, fecha_limite, completada) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $completada = 0;  // Al principio, la meta no está completada
    $stmt->bind_param("issssss", $user_id, $descripcion, $mes_actual, $anio_actual, $fecha_inicio, $fecha_limite, $completada);
    $stmt->execute();

    // Mensaje de éxito después de agregar la meta
    $_SESSION['meta_success'] = "Meta agregada exitosamente!";

    // Redirigir para evitar resubir el formulario al recargar
    header("Location: goals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Metas del Mes</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Estilo de la barra de progreso */
        .progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 10px;
            margin: 20px 0;
            height: 20px;
        }
        .progress-bar {
            height: 100%;
            background-color: #4CAF50;
            border-radius: 10px;
        }

        /* Estilo de la tabla de metas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table td input[type="checkbox"] {
            margin-left: 10px;
        }

        /* Estilo del formulario */
        form {
            margin-top: 30px;
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
        <h1>Mis metas de este mes</h1>

        <!-- Barra de progreso -->
        <div class="progress-container">
            <div class="progress-bar" style="width: <?php echo $porcentaje; ?>%;"></div>
        </div>
        <p><?php echo round($porcentaje); ?>% Completado</p>

        <!-- Mostrar las metas del mes actual -->
        <?php
        if ($metas->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Meta</th><th>Fecha de Inicio</th><th>Fecha Límite</th><th>Completada</th><th>Acciones</th></tr>";
            while ($row = $metas->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_inicio']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_limite']) . "</td>";
                echo "<td><input type='checkbox' class='check-meta' data-id='" . $row['id'] . "' " . ($row['completada'] ? 'checked' : '') . "></td>";
                echo "<td>";
                echo "<a href='edit_goal.php?id=" . $row['id'] . "'>Editar</a> | ";
                echo "<a href='delete_goal.php?id=" . $row['id'] . "'>Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No tienes metas para este mes.</p>";
        }
        ?>

        <!-- Acción para agregar una nueva meta -->
        <form method="post" action="goals.php">
            <label for="descripcion">Nueva Meta:</label>
            <input type="text" name="descripcion" id="descripcion" required>
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" required>
            <label for="fecha_limite">Fecha Límite:</label>
            <input type="date" name="fecha_limite" required>
            <input type="submit" value="Agregar Meta">
        </form>
    </div>

    <!-- Mostrar alerta de éxito si existe un mensaje en la sesión -->
    <script>
        // Mostrar alerta de éxito si existe un mensaje en la sesión
        <?php if (isset($_SESSION['meta_success'])): ?>
            alert('<?php echo $_SESSION['meta_success']; ?>');
            <?php unset($_SESSION['meta_success']); ?>  // Limpiar el mensaje después de mostrarlo
        <?php endif; ?>

        // Script para actualizar la barra de progreso cuando se marca un checkbox
        document.querySelectorAll('.check-meta').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const goalId = this.getAttribute('data-id');
                const completed = this.checked ? 1 : 0;

                // Enviar solicitud AJAX para actualizar la meta
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "update_goal.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Recargar la página después de la actualización
                        location.reload();
                    }
                };
                xhr.send("goal_id=" + goalId + "&completed=" + completed);
            });
        });
    </script>

    <br>
    <a href="home.php">Volver al Home</a>
</body>
</html>
