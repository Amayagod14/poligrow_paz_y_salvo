<?php
session_start();
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (register($_POST['nombre'], $_POST['email'], $_POST['password'])) {
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
</head>
<body>
  <div class="container">
    <h1>Registrarse</h1>
    <?php if (isset($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
      </div>
      <button type="submit">Registrarse</button>
    </form>
    <p class="login-link">¿Ya estás registrado? <a href="index.php">Inicia sesión aquí</a></p> </div>
</body>
</html>