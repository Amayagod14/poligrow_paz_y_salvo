<?php
session_start();
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula']; 
    $password = $_POST['password'];

    $user = getUserByCedula($cedula); 

    if (!$user) {
        $error = "Cédula incorrecta."; 
    } elseif (!password_verify($password, $user['password'])) {
        $error = "Contraseña incorrecta.";
    } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['es_admin'] = $user['es_admin']; 

        // Redirigir al dashboard correspondiente
        if ($_SESSION['es_admin']) {
            header('Location: admin_dashboard.php'); 
        } else {
            header('Location: dashboard.php'); 
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="css/style.css"> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(document).ready(function() {
        <?php if (isset($error)): ?>
            // Mostrar el mensaje de error con SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>'
            });
            $('#login-form')[0].reset(); 
        <?php endif; ?>
    });
  </script>
</head>
<body>
  <div class="container">
    <h1>Iniciar sesión</h1>
    <form method="POST" id="login-form">
      <div class="form-group">
        <label for="cedula">Cédula:</label> 
        <input type="text" id="cedula" name="cedula" placeholder="Cédula" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
      </div>
      <button type="submit">Iniciar sesión</button>
    </form>
    <p class="register-link">¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p> 
  </div>
</body>
</html>