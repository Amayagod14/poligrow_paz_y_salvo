<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/database.php';

// Verificación para super admin
if (!isLoggedIn() || !isSuperAdmin()) {
    header('Location: index.php');
    exit;
}

try {
    $conn = DatabaseConfig::getConnection();
    
    // Consulta para obtener solo 13 registros
    $query = "SELECT id, cedula, nombres, apellidos, cargo, area, es_admin 
              FROM empleados 
              ORDER BY id ASC 
              LIMIT 13";
    
    $result = $conn->query($query);
    $administradores = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - Palmeras</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <!-- Barra de navegación -->
    <nav class="bg-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold">Panel Super Administrador</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-4"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span>
                    <button onclick="confirmarCerrarSesion()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Gestión de Usuarios</h2>
            
            <!-- Tabla de administradores -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-purple-100">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Cédula</th>
                            <th class="px-4 py-2">Nombres</th>
                            <th class="px-4 py-2">Apellidos</th>
                            <th class="px-4 py-2">Cargo</th>
                            <th class="px-4 py-2">Área</th>
                            <th class="px-4 py-2">Tipo Admin</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($administradores as $admin): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($admin['id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($admin['cedula']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($admin['nombres']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($admin['apellidos']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($admin['cargo']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($admin['area']); ?></td>
                            <td class="px-4 py-2 text-center">
                                <?php 
                                $tipo_admin = match(intval($admin['es_admin'])) {
                                    1 => 'Admin',
                                    2 => 'Supervisor',
                                    3 => 'Contabilidad',
                                    4 => 'Super Admin',
                                    default => 'Usuario'
                                };
                                echo $tipo_admin;
                                ?>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button onclick="editarAdmin(<?php echo $admin['id']; ?>)" 
                                        class="text-blue-600 hover:text-blue-800 mx-1" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="resetPassword(<?php echo $admin['id']; ?>)" 
                                        class="text-yellow-600 hover:text-yellow-800 mx-1" 
                                        title="Restablecer Contraseña">
                                    <i class="fas fa-key"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div id="modalEdicion" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Editar Usuario</h3>
                <div class="mt-2 px-7 py-3">
                    <input type="hidden" id="admin-id">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cédula</label>
                        <input type="text" id="admin-cedula" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nombres</label>
                        <input type="text" id="admin-nombres" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Apellidos</label>
                        <input type="text" id="admin-apellidos" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cargo</label>
                        <input type="text" id="admin-cargo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Área</label>
                        <input type="text" id="admin-area" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Usuario</label>
                        <select id="admin-tipo" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="0">Usuario</option>
                            <option value="1">Admin</option>
                            <option value="2">Supervisor</option>
                            <option value="3">Contabilidad</option>
                            <option value="4">Super Admin</option>
                        </select>
                    </div>
                </div>
                <div class="items-center px-4 py-3 text-center">
                    <button id="guardarCambios" class="px-4 py-2 bg-purple-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-300 mr-2">
                        Guardar
                    </button>
                    <button onclick="cerrarModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function resetPassword(id) {
        Swal.fire({
            title: 'Cambiar Contraseña',
            html: `
                <input type="password" id="new-password" class="swal2-input" placeholder="Nueva contraseña">
                <input type="password" id="confirm-password" class="swal2-input" placeholder="Confirmar contraseña">
            `,
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const newPassword = document.getElementById('new-password').value;
                const confirmPassword = document.getElementById('confirm-password').value;
                
                if (!newPassword || !confirmPassword) {
                    Swal.showValidationMessage('Complete ambos campos');
                    return false;
                }
                if (newPassword !== confirmPassword) {
                    Swal.showValidationMessage('Las contraseñas no coinciden');
                    return false;
                }
                if (newPassword.length < 6) {
                    Swal.showValidationMessage('Mínimo 6 caracteres');
                    return false;
                }
                return newPassword;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'super_admin_actions.php',
                    type: 'POST',
                    data: { 
                        action: 'reset_password',
                        id: id,
                        new_password: result.value
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire('¡Éxito!', 'Contraseña actualizada', 'success');
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    }
                });
            }
        });
    }

    function editarAdmin(id) {
        $.ajax({
            url: 'super_admin_actions.php',
            type: 'POST',
            data: { 
                action: 'obtener_admin',
                id: id 
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        const admin = result.data;
                        $('#admin-id').val(admin.id);
                        $('#admin-cedula').val(admin.cedula);
                        $('#admin-nombres').val(admin.nombres);
                        $('#admin-apellidos').val(admin.apellidos);
                        $('#admin-cargo').val(admin.cargo);
                        $('#admin-area').val(admin.area);
                        $('#admin-tipo').val(admin.es_admin);
                        $('#modalEdicion').removeClass('hidden');
                    } else {
                        mostrarError(result.message);
                    }
                } catch (e) {
                    mostrarError('Error al procesar la respuesta');
                }
            },
            error: function() {
                mostrarError('Error de conexión');
            }
        });
    }

    function cerrarModal() {
        $('#modalEdicion').addClass('hidden');
        limpiarFormulario();
    }

    function limpiarFormulario() {
        $('#admin-id').val('');
        $('#admin-cedula').val('');
        $('#admin-nombres').val('');
        $('#admin-apellidos').val('');
        $('#admin-cargo').val('');
        $('#admin-area').val('');
        $('#admin-tipo').val('1');
    }

    $('#guardarCambios').click(function() {
        const datos = {
            action: 'actualizar_admin',
            id: $('#admin-id').val(),
            cedula: $('#admin-cedula').val().trim(),
            nombres: $('#admin-nombres').val().trim(),
            apellidos: $('#admin-apellidos').val().trim(),
            cargo: $('#admin-cargo').val().trim(),
            area: $('#admin-area').val().trim(),
            es_admin: $('#admin-tipo').val()
        };

        if (!datos.cedula || !datos.nombres || !datos.apellidos) {
            mostrarError('Complete los campos obligatorios');
            return;
        }

        $.ajax({
            url: 'super_admin_actions.php',
            type: 'POST',
            data: datos,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    cerrarModal();
                    if (result.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Datos actualizados correctamente',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        mostrarError(result.message);
                    }
                } catch (e) {
                    mostrarError('Error al procesar la respuesta');
                }
            },
            error: function() {
                mostrarError('Error de conexión');
            }
        });
    });

    function mostrarError(mensaje) {
        Swal.fire({
            title: 'Error',
            text: mensaje,
            icon: 'error'
        });
    }

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
        $(document).keydown(function(e) {
            if (e.keyCode === 27) cerrarModal();
        });

        $('#modalEdicion').click(function(e) {
            if (e.target === this) cerrarModal();
        });

        $('#admin-cedula').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#admin-nombres, #admin-apellidos').on('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    });
    </script>
</body>
</html>
