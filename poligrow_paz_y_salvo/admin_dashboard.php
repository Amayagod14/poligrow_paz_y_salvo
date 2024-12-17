<?php
require_once 'includes/auth.php';
require_once 'logica_paz_y_salvo.php'; 

if (!isLoggedIn() || !$_SESSION['es_admin']) {
    header('Location: index.php');
    exit;
}

$pazYSalvo = new PazYSalvo();

// Obtener todos los departamentos para el filtro
$departments = $pazYSalvo->getDepartments();

?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Administrador - Palmeras</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="css/style.css" rel="stylesheet">
  <script>
  function confirmarCerrarSesion() {
             Swal.fire({
                 title: '¿Cerrar Sesión?',
                 icon: 'question',
                 showCancelButton: true,
                 confirmButtonColor: '#10b981',
                 cancelButtonColor: '#ef4444',
                 confirmButtonText: 'Sí, cerrar sesión',
                 cancelButtonText: 'Cancelar'
             }).then((result) => {
                 if (result.isConfirmed) {
                     $.ajax({
                         url: 'logout.php',
                         type: 'POST',
                         success: function(response) {
                             window.location.href = "index.php"; 
                         },
                         error: function(xhr, status, error) {
                             Swal.fire(
                                 'Error',
                                 'No se pudo cerrar la sesión.',
                                 'error'
                             )
                         }
                     });
                 }
             })
         }

    $(document).ready(function() {
      // Función para actualizar la tabla con los resultados del filtro
      function actualizarTabla(empleados) {
        // Limpiar la tabla
        $('#tabla-empleados tbody').empty();

        // Agregar las nuevas filas a la tabla
        $.each(empleados, function(index, empleado) {
          var row = '<tr>' +
                      '<td class="px-6 py-4 whitespace-nowrap">' +
                        '<div class="text-sm text-gray-900">' + empleado.documento + '</div>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-nowrap">' +
                        '<div class="text-sm text-gray-900">' + empleado.nombres + ' ' + empleado.apellidos + '</div>' + // <-- Mostrar nombres y apellidos
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-nowrap">' +
                        '<div class="text-sm text-gray-900">' + empleado.area + '</div>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-nowrap">' +
                        '<div class="text-sm text-gray-900">' + empleado.cargo + '</div>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-nowrap">' +
                        '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' + 
                        (empleado.estado === 'completado' ? 'status-completed' : 'status-pending') + '">' +
                        empleado.estado + '</span>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-nowrap text-right">' +
                        '<div class="flex justify-end space-x-3">' +
                          '<a href="generar_paz_y_salvo_admin.php?empleado_id=' + empleado.empleado_id + '&paz_y_salvo_id=' + empleado.paz_y_salvo_id + '" class="btn btn-primary rounded-lg px-3 py-2 flex items-center"><i class="fas fa-signature mr-2"></i> Firmar</a>' +
                          '<a href="visualizar_paz_y_salvo.php?empleado_id=' + empleado.empleado_id + '" class="btn btn-secondary rounded-lg px-3 py-2 flex items-center"><i class="fas fa-eye mr-2"></i> Visualizar</a>' +
                        '</div>' +
                      '</td>' +
                    '</tr>';
          $('#tabla-empleados tbody').append(row);
        });
      }

      // Manejar el evento click del botón de búsqueda
      $('#btn-buscar').on('click', function() {
        var area = $('#area').val();
        $.ajax({
          url: 'filtrar_paz_y_salvo.php',
          type: 'POST',
          data: { area: area },
          dataType: 'json',
          success: function(response) {
            actualizarTabla(response);
          }
        });
      });
    });
  </script>
</head>
<body>
  <div class="container mx-auto px-4 py-8"> 
    <div class="mb-8"> 
      <h1 class="text-4xl font-bold text-green-800 mb-3">
        <i class="fas fa-leaf mr-3" style="color: var(--primary-green);"></i>Dashboard de Administración
      </h1>
      <p class="text-gray-600 text-lg">Gestión de Paz y Salvo - Palmeras de Aceite</p>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border-2 border-green-100">
      <div class="px-6 py-5 bg-green-50 border-b border-green-200 flex items-center">
        <i class="fas fa-tree text-green-700 mr-3 text-2xl"></i>
        <h3 class="text-xl font-semibold text-green-900">
          Estado de Paz y Salvo de Empleados
        </h3>
      </div>

      <div class="px-6 py-4 flex items-center"> 
        <label for="area" class="block text-sm font-medium text-gray-700 mr-2">Filtrar por área:</label>
        <select id="area" name="area" class="mr-2 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <option value="">Todas las áreas</option>
          <?php foreach ($departments as $dept): ?>
            <option value="<?php echo $dept; ?>"><?php echo $dept; ?></option>
          <?php endforeach; ?>
        </select>
        <button id="btn-buscar" class="btn btn-primary rounded-lg px-3 py-2 flex items-center">
          <i class="fas fa-search mr-2"></i> Buscar
        </button>
      </div>

      <table class="min-w-full" id="tabla-empleados"> 
        </head>
        <tbody class="divide-y divide-green-200">
          </tbody>
      </table>
    </div>

    <div class="mt-8 flex justify-end"> 
      <button onclick="confirmarCerrarSesion()" class="btn btn-danger">
        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
      </button>
    </div>
  </div>
</body>
</html>