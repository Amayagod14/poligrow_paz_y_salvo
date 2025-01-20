<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';
require_once 'logica_paz_y_salvo.php';

// Verificar tipo de usuario
if (!isLoggedIn() || $_SESSION['es_admin'] != 3) {
    header('Location: index.php');
    exit;
}

$pazYSalvo = new PazYSalvo();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Contabilidad - Palmeras</title>
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
            <h1 class="text-4xl font-bold text-blue-800 mb-3">
                <i class="fas fa-calculator mr-3" style="color: var(--primary-blue);"></i>Dashboard de Contabilidad
            </h1>
            <button onclick="confirmarCerrarSesion()" class="btn btn-danger">
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
            </button>
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
                    <i class="fas fa-spinner fa-spin text-blue-500"></i>
                    <span class="ml-2 text-blue-500">Cargando...</span>
                </div>
            </div>
        </div>

        <!-- Sección Paz y Salvos Completados -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-blue-700 mb-4">
                <i class="fas fa-check-circle mr-2"></i>Paz y Salvos Completados
            </h2>
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="tabla-completados">
                        <thead>
                            <tr class="bg-blue-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Fecha Completado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-blue-800 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-blue-200"></tbody>
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
        function actualizarTabla(empleados) {
            const tabla = $('#tabla-completados tbody');
            tabla.empty();

            if (empleados.length === 0) {
                tabla.html(`
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron resultados
                        </td>
                    </tr>`);
                return;
            }

            empleados.forEach(function(empleado) {
                tabla.append(`
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
                            <div class="text-sm text-gray-900">${empleado.created_at}</div>
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
                                </a>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }

        function cargarDatos(busqueda = '') {
            $('#loading-indicator').removeClass('hidden');
            
            $.ajax({
                url: 'buscar_paz_salvos_completados.php',
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
                    actualizarTabla(response);
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
    </script>
</body>
</html>
