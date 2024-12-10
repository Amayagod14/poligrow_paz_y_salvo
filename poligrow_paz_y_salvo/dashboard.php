<?php

require_once 'logica_paz_y_salvo.php';
require_once 'includes/auth.php';


if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Obtener el ID del usuario actual
$usuario_id = $_SESSION['user_id'];

// Obtener el Paz y Salvo del usuario actual
$stmt = DatabaseConfig::getConnection()->prepare("
    SELECT e.id AS empleado_id, e.nombre, e.cedula AS documento, e.area, e.cargo, p.estado, p.id AS paz_y_salvo_id
    FROM empleados e
    INNER JOIN paz_y_salvo p ON e.id = p.empleado_id
    WHERE e.id = ? 
"); 
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$paz_y_salvo = $result->fetch_assoc();
$stmt->close();

// Obtener el número de firmas del Paz y Salvo
if ($paz_y_salvo) {
  $stmt = DatabaseConfig::getConnection()->prepare("SELECT COUNT(*) FROM firmas WHERE paz_y_salvo_id = ?");
  $stmt->bind_param("i", $paz_y_salvo['paz_y_salvo_id']);
  $stmt->execute();
  $stmt->bind_result($num_firmas);
  $stmt->fetch();
  $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestión de Paz y Salvo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminar(empleado_id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'eliminar_paz_y_salvo.php',
                        type: 'POST',
                        data: { empleado_id: empleado_id },
                        success: function(response) {
                            Swal.fire(
                                'Eliminado',
                                'El Paz y Salvo ha sido eliminado.',
                                'success'
                            )
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error',
                                'No se pudo eliminar el Paz y Salvo.',
                                'error'
                            )
                        }
                    });
                }
            })
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
    </script>
</head>
<body>
    <div class="container">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-green-800 mb-2">Dashboard de Paz y Salvo</h1>
            <p class="text-gray-600">Gestiona tu documentación de manera eficiente y clara</p>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-green-50 border-b border-green-200">
                <h3 class="text-lg leading-6 font-medium text-green-900">
                    <i class="fas fa-leaf text-green-600 mr-2"></i>Detalles de tu Paz y Salvo
                </h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-id-card mr-2"></i>Cédula
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-briefcase mr-2"></i>Cargo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-building mr-2"></i>Área
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-check-circle mr-2"></i>Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-signature mr-2"></i>Firmas
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-2"></i>Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($paz_y_salvo): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($paz_y_salvo['nombre']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($paz_y_salvo['documento']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($paz_y_salvo['cargo']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($paz_y_salvo['area']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo ($paz_y_salvo['estado'] === 'completado') ? 'status-completed' : 'status-pending'; ?>">
                                <?php echo htmlspecialchars($paz_y_salvo['estado']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $num_firmas; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="visualizar_paz_y_salvo.php?empleado_id=<?php echo $paz_y_salvo['empleado_id']; ?>" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-eye"></i> Visualizar
                                </a>
                                <a href="#" onclick="confirmarEliminar(<?php echo $paz_y_salvo['empleado_id']; ?>)" 
                                   class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                                <span class="text-yellow-800">No tienes un Paz y Salvo generado</span>
                            </div>
                            <a href="generar_paz_y_salvo_empleado.php" class="mt-4 inline-block btn btn-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Crear Paz y Salvo
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between items-center">
            
            <div class="text-sm text-gray-600">
                <i class="fas fa-tree text-green-600 mr-2"></i>
                Gestión de Paz y Salvo - Sector Palmero
            </div>
            <button onclick="confirmarCerrarSesion()" class="btn btn-danger">
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
            </button>

        </div>
    </div>
</body>
</html>