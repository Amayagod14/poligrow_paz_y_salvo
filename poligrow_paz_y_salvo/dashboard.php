<?php
session_start();
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Obtener TODOS los Paz y Salvo (incluyendo los completados)
$stmt = DatabaseConfig::getConnection()->prepare("
    SELECT e.id AS empleado_id, e.nombre, e.documento, e.area, e.cargo, p.estado 
    FROM empleados e
    INNER JOIN paz_y_salvo p ON e.id = p.empleado_id
"); 
$stmt->execute();
$result = $stmt->get_result();
$empleados = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function confirmarEliminar(button) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar!'
      }).then((result) => {
        if (result.isConfirmed) {
          var empleado_id = button.dataset.id;
          // Enviar solicitud AJAX para eliminar el registro
          $.ajax({
            url: 'eliminar_paz_y_salvo.php',
            type: 'POST',
            data: { empleado_id: empleado_id },
            success: function(response) {
              // Manejar la respuesta del servidor (redirigir, mostrar un mensaje, etc.)
              Swal.fire(
                'Eliminado!',
                'El Paz y Salvo ha sido eliminado.',
                'success'
              )
              location.reload(); // Recargar la página para actualizar la lista
            },
            error: function(xhr, status, error) {
              console.error(error);
              Swal.fire(
                'Error!',
                'Hubo un error al eliminar el Paz y Salvo.',
                'error'
              )
            }
          });
        }
      })
    }

    function confirmarCerrarSesion() {
        Swal.fire({
            title: '¿Estás seguro de que quieres cerrar sesión?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', 
            cancelButtonColor: '#3085d6', 
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar una solicitud AJAX a un archivo PHP para cerrar la sesión
                $.ajax({
                    url: 'logout.php', // Crea este archivo
                    type: 'POST',
                    success: function(response) {
                        // Redirigir al usuario a index.php
                        window.location.href = "index.php"; 
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        Swal.fire(
                            'Error!',
                            'Hubo un error al cerrar la sesión.',
                            'error'
                        )
                    }
                });
            }
        })
    }

    function confirmarEditar(empleado_id) {
        Swal.fire({
            title: '¿Estás seguro de que quieres editar este Paz y Salvo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, editar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "generar_paz_y_salvo.php?empleado_id=" + empleado_id; 
            }
        })
    }
  </script>
</head>
<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-center text-gray-800">
          Bienvenido al Dashboard
        </h1>
        <button onclick="confirmarCerrarSesion()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
          Cerrar sesión
        </button>
      </div>

      <h2 class="text-xl font-bold mb-4">Paz y Salvo </h2> 

      <a href="generar_paz_y_salvo.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
        Generar Nuevo Paz y Salvo
      </a>

      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Cédula
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nombre
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Área
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Cargo
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Estado
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Acciones</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($empleados as $empleado): ?> 
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900"><?php echo $empleado['documento']; ?></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900"><?php echo $empleado['nombre']; ?></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900"><?php echo $empleado['area']; ?></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900"><?php echo $empleado['cargo']; ?></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
              <?php echo ($empleado['estado'] === 'completado') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                <?php echo $empleado['estado']; ?>
              </span>
            </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="confirmarEditar(<?php echo $empleado['empleado_id']; ?>)" class="text-blue-600 hover:text-blue-900">Editar</button>
                <button class="ml-2 text-red-600 hover:text-red-900" data-id="<?php echo $empleado['empleado_id']; ?>" onclick="confirmarEliminar(this)">Eliminar</button>
                <a href="visualizar_paz_y_salvo.php?empleado_id=<?php echo $empleado['empleado_id']; ?>" class="ml-2 text-green-600 hover:text-green-900">Visualizar</a>
              </td>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>