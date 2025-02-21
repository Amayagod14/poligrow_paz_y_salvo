<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';
require_once 'logica_paz_y_salvo.php'; 

// Verificar tipo de usuario
if (!isLoggedIn() || $_SESSION['es_admin'] != 2) {
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
    <title>Dashboard Supervisor - Palmeras</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="css/style.css" rel="stylesheet">
    <style>
        .status-completado {
            background-color: #10B981;
            color: white;
        }
        .status-en_proceso {
            background-color: #F59E0B;
            color: white;
        }
        .status-pendiente {
            background-color: #EF4444;
            color: white;
        }
        .empty-table {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center"> 
        <h1 class="text-4xl font-bold text-green-800 mb-3">
            <i class="fas fa-leaf mr-3" style="color: var(--primary-green);"></i>Dashboard de SUPERVISOR
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

        <!-- Barra de búsqueda -->
        <div class="mb-6">
            <div class="flex items-center">
                <input type="text" id="busqueda" 
                    class="flex-grow py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                    placeholder="Buscar por documento, nombre, área o cargo...">
                <button id="btn-buscar" class="ml-3 btn btn-primary">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
                <div id="loading-indicator" class="ml-3 hidden">
                    <i class="fas fa-spinner fa-spin text-green-500"></i>
                    <span class="ml-2 text-green-500">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <a href="register.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-user-plus mr-2"></i>Registrar Usuario
            </a>
        </div>

        <!-- Sección Completados -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-green-700 mb-4">
                <i class="fas fa-check-circle mr-2"></i>Paz y Salvos Completados
            </h2>
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="tabla-completados">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-green-800 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-green-200"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sección En Proceso -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-yellow-600 mb-4">
                <i class="fas fa-clock mr-2"></i>Paz y Salvos En Proceso
            </h2>
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="tabla-en-proceso">
                        <thead>
                            <tr class="bg-yellow-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-yellow-800 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-yellow-200"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sección Pendientes -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-red-600 mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>Paz y Salvos Pendientes
            </h2>
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="tabla-pendientes">
                        <thead>
                            <tr class="bg-red-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-red-800 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-red-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
        function actualizarTablas(empleados) {
            // Limpiar todas las tablas
            $('#tabla-completados tbody').empty();
            $('#tabla-en-proceso tbody').empty();
            $('#tabla-pendientes tbody').empty();

            if (empleados.length === 0) {
                const mensajeNoResultados = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron resultados
                        </td>
                    </tr>`;
                $('#tabla-completados tbody').html(mensajeNoResultados);
                $('#tabla-en-proceso tbody').html(mensajeNoResultados);
                $('#tabla-pendientes tbody').html(mensajeNoResultados);
                return;
            }

            empleados.forEach(function(empleado) {
    var row = `
        <tr>
            <td class="px-6 py-4 whitespace-normal">
                <div class="text-sm text-gray-900">${empleado.documento}</div>
            </td>
            <td class="px-6 py-4 whitespace-normal">
                <div class="text-sm text-gray-900">${empleado.nombres} ${empleado.apellidos}</div>
            </td>
            <td class="px-6 py-4 whitespace-normal">
                <div class="text-sm text-gray-900">${empleado.area}</div>
            </td>
            <td class="px-6 py-4 whitespace-normal">
                <div class="text-sm text-gray-900">${empleado.cargo}</div>
            </td>
            <td class="px-6 py-4 whitespace-normal">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-${empleado.estado}">
                    ${empleado.estado}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-normal text-right">
                <div class="flex justify-end space-x-2">
                    <a href="visualizar_paz_y_salvo.php?empleado_id=${empleado.empleado_id}" 
                       class="btn btn-secondary" target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="generar_pdf.php?empleado_id=${empleado.empleado_id}" 
                       class="btn btn-success" target="_blank">
                        <i class="fas fa-file-pdf"></i>
                    </a>`;

    // Agregar botón de crear solo si el estado es pendiente
    if (empleado.estado === 'pendiente') {
        row += `
            <a href="generar_paz_y_salvo_empleado.php?documento=${empleado.documento}" 
               class="btn btn-primary"
               title="Crear Paz y Salvo">
                <i class="fas fa-plus-circle"></i>
            </a>`;
    }

    // Botón de eliminar
    row += `
            <button onclick="eliminarPazYSalvo(${empleado.empleado_id})" class="btn btn-danger" title="Eliminar Paz y Salvo">
                <i class="fas fa-trash-alt"></i>
            </button>`;

    row += `
                </div>
            </td>
        </tr>`;
    
    // Agregar la fila a la tabla correspondiente según el estado
    switch(empleado.estado) {
        case 'completado':
            $('#tabla-completados tbody').append(row);
            break;
        case 'en_proceso':
            $('#tabla-en-proceso tbody').append(row);
            break;
        case 'pendiente':
        default:
            $('#tabla-pendientes tbody').append(row);
            break;
    }
});


        }

        function cargarDatos(busqueda = '') {
            $('#loading-indicator').removeClass('hidden');
            
            $.ajax({
                url: 'buscar_empleados.php',
                type: 'POST',
                data: { busqueda: busqueda },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error
                        });
                        return;
                    }
                    actualizarTablas(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al cargar los datos'
                    });
                },
                complete: function() {
                    $('#loading-indicator').addClass('hidden');
                }
            });
        }

        // Cargar datos iniciales
        cargarDatos();

        // Búsqueda con botón
        $('#btn-buscar').on('click', function() {
            cargarDatos($('#busqueda').val());
        });

        // Búsqueda con Enter
        $('#busqueda').on('keypress', function(e) {
            if (e.which === 13) {
                cargarDatos($(this).val());
            }
        });

        // Búsqueda en tiempo real
        let timeoutId;
        $('#busqueda').on('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                cargarDatos($(this).val());
            }, 500);
        });
    });
    function eliminarPazYSalvo(empleado_id) {
    Swal.fire({
        title: '¿Estás seguro de eliminar este Paz y Salvo?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#10b981',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'eliminar_paz_y_salvo.php',
                type: 'POST',
                data: { empleado_id: empleado_id },
                success: function(response) {
                    Swal.fire('Eliminado!', response, 'success');
                    cargarDatos(); // Actualizar la tabla después de eliminar
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'No se pudo eliminar el Paz y Salvo.', 'error');
                }
            });
        }
    });
}

    </script>
</body>
</html>