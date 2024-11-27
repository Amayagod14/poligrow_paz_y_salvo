<?php
session_start();
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (register(
        $_POST['cedula'], 
        $_POST['nombre'], 
        $_POST['cargo'], 
        $_POST['area'], 
        $_POST['password']
    )) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Error al registrarse.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registrarse</title>
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
      <?php endif; ?>
    });
  </script>
</head>
<body>
  <div class="container">
    <h1>Registrarse</h1>
    <?php if (isset($error)): ?>
      <p class="error"><?php echo $error; ?></p> 
    <?php endif; ?>
    <form method="POST" id="register-form"> 
      <div class="form-group">
        <label for="cedula">Cédula:</label>
        <input type="text" id="cedula" name="cedula" placeholder="Cédula" required>
      </div>
      <div class="form-group">
        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" required>
      </div>
      <div class="form-group">
        <label for="cargo">Cargo:</label>
        <input type="text" id="cargo" name="cargo" placeholder="Cargo" required>
      </div>
      <div class="form-group">
        <label for="area">Área:</label>
        <input type="text" id="area" name="area" placeholder="Área" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
      </div>
      <button type="submit">Registrarse</button>
    </form>
    <p class="login-link">¿Ya estás registrado? <a href="index.php">Inicia sesión aquí</a></p>
  </div>
</body>
</html>