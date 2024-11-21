<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$errors = [];

// Obtener eventos del usuario
$eventos = [];
$result = $conn->query("SELECT * FROM eventos WHERE usuario_id = '$user_id'");
if ($result) {
    $eventos = $result->fetch_all(MYSQLI_ASSOC);
}

// Añadir o editar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $titulo = trim($_POST['titulo']);
    $fecha = trim($_POST['fecha']);
    $descripcion = trim($_POST['descripcion']);
    $evento_id = $_POST['evento_id'] ?? null;

    if (empty($titulo)) {
        $errors['titulo'] = "El título del evento es obligatorio.";
    }
    if (empty($fecha)) {
        $errors['fecha'] = "La fecha del evento es obligatoria.";
    }

    if (empty($errors)) {
        if ($accion === 'add') {
            // Insertar un nuevo evento
            $stmt = $conn->prepare("INSERT INTO eventos (usuario_id, titulo, fecha, descripcion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $titulo, $fecha, $descripcion);
            $stmt->execute();

            // Mensaje de éxito para agregar evento
            $_SESSION['evento_success'] = "Evento agregado exitosamente!";
        } elseif ($accion === 'edit' && $evento_id) {
            // Editar un evento existente
            $stmt = $conn->prepare("UPDATE eventos SET titulo = ?, fecha = ?, descripcion = ? WHERE id = ? AND usuario_id = ?");
            $stmt->bind_param("sssii", $titulo, $fecha, $descripcion, $evento_id, $user_id);
            $stmt->execute();

            // Mensaje de éxito para editar evento
            $_SESSION['evento_success'] = "Evento editado exitosamente!";
        }
        header("Location: calendar.php");
        exit();
    }
}

// Eliminar evento
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM eventos WHERE id = '$delete_id' AND usuario_id = '$user_id'");
    header("Location: calendar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <!-- Vincular Bootstrap para un mejor diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .card {
            margin-top: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .card-body {
            background-color: #ffffff;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Calendario de Eventos</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Editar Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Card para Agregar/Editar evento -->
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Agregar / Editar Evento</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="accion" value="add">
                            <input type="hidden" name="evento_id" id="evento_id">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título del Evento:</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                                <?php if (isset($errors['titulo'])): ?>
                                    <p class="text-danger"><?php echo $errors['titulo']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha del Evento:</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                                <?php if (isset($errors['fecha'])): ?>
                                    <p class="text-danger"><?php echo $errors['fecha']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Evento</button>
                        </form>
                    </div>
                </div>

                <!-- Lista de eventos -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Eventos Programados</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eventos as $evento): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['descripcion']); ?></td>
                                        <td>
                                            <button onclick="editarEvento(<?php echo $evento['id']; ?>, '<?php echo addslashes($evento['titulo']); ?>', '<?php echo $evento['fecha']; ?>', '<?php echo addslashes($evento['descripcion']); ?>')" class="btn btn-warning btn-sm">Editar</button>
                                            <a href="calendar.php?delete_id=<?php echo $evento['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este evento?')">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© 2024 Tu Empresa. Todos los derechos reservados.</span>
        </div>
    </footer>

    <!-- Script de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para mostrar alertas -->
    <script>
        // Mostrar alerta de éxito si existe un mensaje en la sesión
        <?php if (isset($_SESSION['evento_success'])): ?>
            alert('<?php echo $_SESSION['evento_success']; ?>');
            <?php unset($_SESSION['evento_success']); ?>  // Limpiar el mensaje después de mostrarlo
        <?php endif; ?>

        function editarEvento(id, titulo, fecha, descripcion) {
            document.querySelector("input[name='accion']").value = "edit";
            document.getElementById("evento_id").value = id;
            document.getElementById("titulo").value = titulo;
            document.getElementById("fecha").value = fecha;
            document.getElementById("descripcion").value = descripcion;
        }
    </script>

</body>
</html>
