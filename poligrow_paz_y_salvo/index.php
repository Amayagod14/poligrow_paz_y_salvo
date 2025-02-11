<?php
session_start();
require_once 'includes/auth.php';

// Si ya está logueado, redirigir según su rol
if (isLoggedIn()) {
    switch($_SESSION['es_admin']) {
        case 4:
            header('Location: dashboard_super_admin.php');
            break;
        case 3:
            header('Location: dashboard_contabilidad.php');
            break;
        case 2:
            header('Location: supervisor_dashboard.php');
            break;
        case 1:
            header('Location: admin_dashboard.php');
            break;
        default:
            header('Location: dashboard.php');
            break;
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula']; 
    $password = $_POST['password'];

    $loginResult = login($cedula, $password);

    if ($loginResult['success']) {
        // Redirigir según el tipo de usuario
        switch($loginResult['es_admin']) {
            case 4:
                header('Location: dashboard_super_admin.php');
                break;
            case 3:
                header('Location: dashboard_contabilidad.php');
                break;
            case 2:
                header('Location: supervisor_dashboard.php');
                break;
            case 1:
                header('Location: admin_dashboard.php');
                break;
            default:
                header('Location: dashboard.php');
                break;
        }
        exit;
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-image: url('img/fondo.jpeg') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            min-height: 100vh !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
    </style>
</head>
<body>
    <div class="container1"> 
        <div class="login-form"> 
            <h1>Iniciar sesión</h1> 
            <form method="POST" id="login-form">
                <div class="form-group">
                    <label for="cedula">ID:</label>
                    <input type="text" id="cedula" name="cedula" 
                           placeholder="Ingrese su cédula" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Ingrese su contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    Iniciar sesión
                </button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            <?php if (isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>'
            });
            $('#login-form')[0].reset();
            <?php endif; ?>
        });
    </script>
</body>
</html>

