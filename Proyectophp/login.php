<?php
include 'includes/db.php'; // Conexión a la base de datos
session_start();

// Si el usuario ya está logueado, redirigir a la página principal
if (isset($_SESSION['user'])) {
    header("Location: pages/home.php"); // Redirigir si ya está logueado
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Evitar SQL Injection usando Prepared Statements
    $query = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc(); // Guardamos la información del usuario en la sesión
        header("Location: pages/home.php"); // Redirigir a la página de inicio o la página principal
        exit();
    } else {
        $error_message = "Credenciales incorrectas."; // Mostrar error si no hay coincidencia
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .register-link {
            text-align: center;
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <?php
        // Mostrar mensaje de error si las credenciales son incorrectas
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>

        <form method="post">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required placeholder="Ingresa tu correo electrónico">

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required placeholder="Ingresa tu contraseña">

            <button type="submit">Iniciar Sesión</button>
        </form>

        <div class="register-link">
            <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>

</body>
</html>
