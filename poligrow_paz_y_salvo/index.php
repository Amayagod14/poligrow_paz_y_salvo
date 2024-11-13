<?php
session_start();
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login($_POST['email'], $_POST['password'])) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
  <div class="container">
    <h1>Iniciar sesión</h1>
    <?php if (isset($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
      </div>
      <button type="submit">Iniciar sesión</button>
    </form>
    <p class="register-link">¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p> </div>
</body>
</html>