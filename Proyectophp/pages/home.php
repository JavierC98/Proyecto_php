<?php session_start(); ?>
<?php if (!isset($_SESSION['user'])) header("Location: ../login.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Vincular Bootstrap para un mejor diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .nav-link {
            font-weight: 500;
            font-size: 1.1rem;
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

        /* Barra de navegación mejorada */
        .navbar {
            background-color: #004085; /* Fondo más oscuro */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra para dar profundidad */
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff !important;
        }
        .navbar-nav .nav-link {
            color: #ffffff !important; /* Cambiar color de los enlaces */
            margin-right: 15px;
        }
        .navbar-nav .nav-link:hover {
            color: #ffcc00 !important; /* Cambiar color al pasar el mouse */
        }

        /* Mejorar el espaciado de los botones */
        .d-grid .btn {
            font-size: 1.1rem;
            padding: 10px;
            margin-bottom: 10px;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .navbar-collapse {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Editar Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="goals.php">Metas</a>
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
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Bienvenido a tu Panel de Usuario</h3>
                    </div>
                    <div class="card-body">
                        <p>Desde aquí puedes acceder a tu perfil, gestionar tu calendario y establecer tus metas.</p>
                        <div class="d-grid gap-2">
                            <a href="profile.php" class="btn btn-primary">Ir a Editar Perfil</a>
                            <a href="calendar.php" class="btn btn-secondary">Ver Calendario</a>
                            <a href="goals.php" class="btn btn-success">Establecer Metas</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© 2024 Javier Aguilar. Todos los derechos reservados.</span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
