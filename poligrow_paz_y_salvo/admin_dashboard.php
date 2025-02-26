<?php
require_once 'includes/auth.php';
require_once 'logica_paz_y_salvo.php'; 

// Verificar si el usuario está logueado y es admin
if (!isLoggedIn() || !$_SESSION['es_admin']) {
    header('Location: index.php');
    exit;
}

// Obtener la información del usuario actual
$usuario = getUserById($_SESSION['user_id']);
if ($usuario) {
    $_SESSION['cargo'] = $usuario['cargo'];
} else {
    $_SESSION['cargo'] = 'Administrador'; // Valor por defecto
}

$pazYSalvo = new PazYSalvo();
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
      // Función para actualizar la tabla con los resultados 
      function actualizarTabla(empleados, tablaId) {
        // Limpiar la tabla
        $(tablaId + ' tbody').empty();

        // Agregar las nuevas filas a la tabla
        $.each(empleados, function(index, empleado) {
          var row = '<tr>' +
                      '<td class="px-6 py-4 whitespace-normal">' +
                        '<div class="text-sm text-gray-900">' + empleado.documento + '</div>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-normal">' +
                        '<div class="text-sm text-gray-900">' + empleado.nombres + ' ' + empleado.apellidos + '</div>' + 
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-normal">' +
                        '<div class="text-sm text-gray-900">' + empleado.area + '</div>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-normal">' +
                        '<div class="text-sm text-gray-900">' + empleado.cargo + '</div>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-normal">' +
                        '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' + 
                        (empleado.estado === 'completado' ? 'status-completed' : 'status-pending') + '">' +
                        empleado.estado + '</span>' +
                      '</td>' +
                      '<td class="px-6 py-4 whitespace-normal text-right">' +
                        '<div class="flex flex-col items-end">' + 
                          '<a href="generar_paz_y_salvo_admin.php?empleado_id=' + empleado.empleado_id + '&paz_y_salvo_id=' + empleado.paz_y_salvo_id + '" class="btn btn-primary mb-2 w-full">' + 
                            '<i class="fas fa-signature mr-2"></i> Firmar' + 
                          '</a>' +
                          '<a href="visualizar_paz_y_salvo.php?empleado_id=' + empleado.empleado_id + '" class="btn btn-secondary w-full" target="_blank">' + 
                            '<i class="fas fa-eye mr-2"></i> Visualizar' +
                          '</a>' +
                        '</div>' +
                      '</td>' +
                    '</tr>';
          $(tablaId + ' tbody').append(row);
        });
      }

      // Cargar automáticamente los paz y salvos en proceso al cargar la página
      $(function() {
        var departamento = '<?php echo htmlspecialchars($_SESSION['area']); ?>';
        $.ajax({
          url: 'buscar_empleados_por_departamento.php',
          type: 'POST',
          data: { departamento: departamento },
          dataType: 'json',
          success: function(response) {
            actualizarTabla(response, '#tabla-pendientes-empleados');
          }
        });
      });

      // Manejar el evento click del botón "Buscar"
      $('#btn-buscar-pendientes').on('click', function() {
        var busqueda = $('#busqueda-pendientes').val();
        $.ajax({
          url: 'buscar_empleados_proceso.php',
          type: 'POST',
          data: { busqueda: busqueda, estado: 'pendiente' }, // Solo busca los que están pendientes
          dataType: 'json',
          success: function(response) {
            actualizarTabla(response, '#tabla-pendientes-empleados');
          }
        });
      });

      // Manejar el evento del filtro por área
      $('#filtro-area').on('change', function() {
        var areaSeleccionada = $(this).val();
        $.ajax({
          url: 'filtrar_area.php',
          type: 'POST',
          data: { area: areaSeleccionada, estado: 'pendiente' }, // Solo filtra los pendientes
          dataType: 'json',
          success: function(response) {
            actualizarTabla(response, '#tabla-pendientes-empleados');
          }
        });
      });
    });
  </script>
</head>
<body>
<div class="container">
    <div class="mb-8 flex justify-between items-center"> 
        <h1 class="text-4xl font-bold text-green-800 mb-3">
            <i class="fas fa-leaf mr-3" style="color: var(--primary-green);"></i>Dashboard de Administración
        </h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-600 font-semibold">
                <i class="fas fa-user-tie mr-2"></i><?php echo $_SESSION['cargo']; ?>
            </span>
            <button onclick="confirmarCerrarSesion()" class="btn btn-danger"> 
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
            </button>
        </div>
    </div>

    <p class="text-gray-600 text-lg mb-4">Gestión de Paz y Salvo - Palmeras de Aceite</p> 

    <div class="w-full pr-4 mb-4 lg:mb-0"> 
        <ul class="menu flex space-x-4"> 
            <li id="menu-pendientes" class="cursor-pointer py-2 px-4 hover:bg-gray-100 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i> Pendientes por firmar
            </li>
        </ul>
    </div>

    <div class="w-full"> 
        <div class="bg-white shadow-lg rounded-xl border-2 border-green-100 max-w-7xl"> 
            <div class="px-6 py-5 bg-green-50 border-b border-green-200 flex items-center">
                <i class="fas fa-tree text-green-700 mr-3 text-2xl"></i>
                <h3 class="text-xl font-semibold text-green-900">
                    Estado de Paz y Salvo Pendientes
                </h3>
            </div>

            <div id="seccion-pendientes" class="table-responsive"> 
                <div class="px-6 py-4">
                    <div class="flex"> 
                        <input type="text" id="busqueda-pendientes" name="busqueda-pendientes" class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm w-full lg:w-1/2" placeholder="Buscar...">
                        <button id="btn-buscar-pendientes" class="btn btn-primary rounded-lg px-3 py-2 flex items-center ml-2"> 
                            <i class="fas fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <?php if ($_SESSION['user_id'] == 5): ?>
                <div class="px-6 py-4">
                    <label for="filtro-area" class="block text-gray-700 font-bold mb-2">Filtrar por Área:</label>
                    <select id="filtro-area" class="border border-gray-300 rounded px-4 py-2 w-full">
                        <option value="">Seleccionar área</option>
                        <option value="GESTION HUMANA">GESTION HUMANA</option>
                        <option value="DIRECCIÓN AGRONOMICO">DIRECCIÓN AGRONOMICO</option>
                        <option value="DIRECCIÓN INDUSTRIAL">DIRECCIÓN INDUSTRIAL</option>
                        <option value="AMBIENTAL">AMBIENTAL</option>
                        <option value="LOGISTICA Y COMPRAS">LOGISTICA Y COMPRAS</option>
                        <option value="DIRECCIÓN FINANCIERA">DIRECCIÓN FINANCIERA</option>
                    </select>
                </div>
                <?php endif; ?>

                <table class="min-w-full w-full" id="tabla-pendientes-empleados"> 
                    <thead>
                        <tr>
                            <th class="px-6 py-3">Documento</th>
                            <th class="px-6 py-3">Nombres</th>
                            <th class="px-6 py-3">Área</th>
                            <th class="px-6 py-3">Cargo</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-green-200">
                        <!-- Aquí va el contenido de la tabla -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
