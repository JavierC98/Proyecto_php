<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$errors = [];
$message = '';

// Obtener la información actual del usuario
$result = $conn->query("SELECT nombre, email, password FROM usuarios WHERE id = '$user_id'");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = ['nombre' => '', 'email' => '', 'password' => ''];
}

// Manejar cambios en el perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validaciones
    if (empty($nombre)) {
        $errors['nombre'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errors['email'] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "El formato del correo electrónico no es válido.";
    }

    // Validar contraseña actual y nueva contraseña
    if (!empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors['current_password'] = "Debes ingresar tu contraseña actual para cambiarla.";
        } else {
            // Comparar directamente con la contraseña almacenada
            if ($current_password !== $user['password']) {
                $errors['current_password'] = "La contraseña actual es incorrecta.";
            }
        }

        if ($new_password !== $confirm_password) {
            $errors['password'] = "Las contraseñas no coinciden.";
        }
    }

    // Si no hay errores, guardar los cambios
    if (empty($errors)) {
        if (!empty($nombre)) {
            $conn->query("UPDATE usuarios SET nombre = '$nombre' WHERE id = '$user_id'");
            $_SESSION['user']['nombre'] = $nombre;
        }

        if (!empty($email)) {
            $conn->query("UPDATE usuarios SET email = '$email' WHERE id = '$user_id'");
        }

        if (!empty($new_password)) {
            $conn->query("UPDATE usuarios SET password = '$new_password' WHERE id = '$user_id'");
        }

        // Mensaje de éxito
        $message = "Perfil editado correctamente.";
        
        // Redirigir automáticamente al home con un pequeño retraso
        echo "<script>
                alert('$message');
                window.location.href = 'home.php';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Editar Perfil</h3>
                    </div>
                    <div class="card-body">
                        <!-- Mensaje de error general -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario para editar el perfil -->
                        <form method="post" class="needs-validation" novalidate>
                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" id="nombre" name="nombre" 
                                       class="form-control <?php echo isset($errors['nombre']) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo htmlspecialchars($nombre ?? $user['nombre']); ?>" required>
                                <?php if (isset($errors['nombre'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['nombre']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email" name="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo htmlspecialchars($email ?? $user['email']); ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Contraseña actual -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                <input type="password" id="current_password" name="current_password" 
                                       class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>">
                                <?php if (isset($errors['current_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['current_password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Nueva Contraseña -->
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Botón de enviar -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>

                        <hr>

                        <!-- Volver al Home -->
                        <div class="text-center">
                            <a href="home.php" class="btn btn-link">Volver al Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
