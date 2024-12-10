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
  <div class="container1"> 
    <div class="register-form"> 
      <h1 class="text-3xl font-bold text-green-800 mb-4">Registrarse</h1> 
      <form method="POST" id="register-form">
        <div class="form-group">
          <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula:</label>
          <input type="text" id="cedula" name="cedula" placeholder="Cédula" required
                 class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <div class="form-group">
          <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo:</label>
          <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" required
                 class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <div class="form-group">
          <label for="cargo" class="block text-sm font-medium text-gray-700">Cargo:</label>
          <input type="text" id="cargo" name="cargo" placeholder="Cargo" required
                 class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <div class="form-group">
          <label for="area" class="block text-sm font-medium text-gray-700">Área:</label>
          <input type="text" id="area" name="area" placeholder="Área" required
                 class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <div class="form-group">
          <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
          <input type="password" id="password" name="password" placeholder="Contraseña" required
                 class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <button type="submit" class="btn btn-primary w-full mt-4">
          Registrarse
        </button>
      </form>
      <p class="mt-4 text-center text-sm text-gray-600">
        ¿Ya tienes una cuenta? <a href="index.php" class="text-green-600 hover:text-green-500">Inicia sesión aquí</a>
      </p>
    </div>
  </div>
</body>
</html>