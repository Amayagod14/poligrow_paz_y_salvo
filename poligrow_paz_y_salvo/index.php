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
<html>
<head>
    <title>Iniciar sesión - Sistema de Paz y Salvo</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>',
                confirmButtonColor: '#3085d6'
            });
            $('#login-form')[0].reset();
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <div class="container1"> 
        <div class="login-form"> 
            <h1 class="text-3xl font-bold text-green-800 mb-4">Iniciar sesión</h1> 
            <form method="POST" id="login-form">
                <div class="form-group">
                    <label for="cedula" class="block text-sm font-medium text-gray-700">Usuario:</label>
                    <input type="text" 
                           id="cedula" 
                           name="cedula" 
                           placeholder="Ingrese su ID" 
                           required
                           class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="form-group">
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Ingrese su contraseña" 
                           required
                           class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <button type="submit" class="btn btn-primary w-full mt-4">
                    Iniciar sesión
                </button>
            </form>

        </div>
    </div>
</body>
</html>
