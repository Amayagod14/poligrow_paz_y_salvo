<?php
session_start();
require_once 'includes/database.php';

// Verificar tipo de usuario
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 2) {
    header('Location: index.php');
    exit;
}

// Verificar si se recibió el documento
if (!isset($_GET['documento'])) {
    header('Location: supervisor_dashboard.php');
    exit;
}

$documento = $_GET['documento'];
$conn = DatabaseConfig::getConnection();

// Obtener información del empleado
$stmt = $conn->prepare("SELECT * FROM empleados WHERE cedula = ?");
$stmt->bind_param("s", $documento);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();
$stmt->close();

if (!$empleado) {
    header('Location: supervisor_dashboard.php');
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $fecha_retiro = $_POST['fecha_retiro'];
    $motivo_retiro = $_POST['motivo_retiro'];
    
    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Actualizar la información en la tabla empleados
        $stmt = $conn->prepare("UPDATE empleados SET 
                              fecha_ingreso = ?,
                              fecha_retiro = ?,
                              motivo_retiro = ?
                              WHERE id = ?");
        
        $stmt->bind_param("sssi", $fecha_ingreso, $fecha_retiro, $motivo_retiro, $empleado['id']);
        $stmt->execute();
        $stmt->close();

        // Verificar si ya existe un registro en paz_y_salvo
        $stmt = $conn->prepare("SELECT id FROM paz_y_salvo WHERE empleado_id = ?");
        $stmt->bind_param("i", $empleado['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $paz_y_salvo_existente = $result->fetch_assoc();
        $stmt->close();

        if ($paz_y_salvo_existente) {
            // Actualizar el estado en paz_y_salvo
            $stmt = $conn->prepare("UPDATE paz_y_salvo SET 
                                  estado = 'en_proceso'
                                  WHERE empleado_id = ?");
            
            $stmt->bind_param("i", $empleado['id']);
        } else {
            // Crear nuevo registro en paz_y_salvo
            $stmt = $conn->prepare("INSERT INTO paz_y_salvo 
                                  (empleado_id, estado) 
                                  VALUES (?, 'en_proceso')");
            
            $stmt->bind_param("i", $empleado['id']);
        }
        if($stmt->execute()) {
          $conn->commit();
          
          // Enviar notificación por correo
          require_once 'includes/mail_functions.php';
          $correo_enviado = enviarNotificacionPazYSalvo($empleado['id']);
          
          if($correo_enviado) {
              $_SESSION['mensaje'] = "Paz y salvo generado y notificación enviada correctamente.";
          } else {
              $_SESSION['mensaje'] = "Paz y salvo generado pero hubo un error al enviar la notificación.";
          }
          
          header('Location: supervisor_dashboard.php?success=1');
          exit;
      }
      


    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error al guardar los datos: " . $e->getMessage();
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Generar Paz y Salvo - Palmeras</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-green-800 mb-3">
                    <i class="fas fa-file-alt mr-3"></i>Generar Paz y Salvo
                </h1>
                <a href="supervisor_dashboard.php" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="bg-white shadow-lg rounded-lg p-6">
                <!-- Información del empleado (solo lectura) -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Documento</label>
                        <input type="text" value="<?php echo htmlspecialchars($empleado['cedula']); ?>" 
                               class="mt-1 block w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombres</label>
                        <input type="text" value="<?php echo htmlspecialchars($empleado['nombres']); ?>" 
                               class="mt-1 block w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                        <input type="text" value="<?php echo htmlspecialchars($empleado['apellidos']); ?>" 
                               class="mt-1 block w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Área</label>
                        <input type="text" value="<?php echo htmlspecialchars($empleado['area']); ?>" 
                               class="mt-1 block w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cargo</label>
                        <input type="text" value="<?php echo htmlspecialchars($empleado['cargo']); ?>" 
                               class="mt-1 block w-full bg-gray-50" readonly>
                    </div>
                </div>

                <!-- Campos editables -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Ingreso</label>
                        <input type="date" name="fecha_ingreso" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Retiro</label>
                        <input type="date" name="fecha_retiro" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>

                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Motivo de Retiro</label>
                    <select name="motivo_retiro" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Seleccione un motivo</option>
                        <option value="Terminacion de contrato por justa causa">Terminación de contrato por justa causa</option>
                        <option value="Terminacion de contrato por mutuo acuerdo">Terminación de contrato por mutuo acuerdo</option>
                        <option value="Renuncia voluntaria">Renuncia voluntaria</option>
                        <option value="Terminacion por no prorroga de contrato">Terminación por no prórroga de contrato</option>
                    </select>
                </div>


                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                    <a href="supervisor_dashboard.php" 
                       class="btn btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar Información
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Validación de fechas
    document.querySelector('form').addEventListener('submit', function(e) {
        const fechaIngreso = new Date(document.querySelector('input[name="fecha_ingreso"]').value);
        const fechaRetiro = new Date(document.querySelector('input[name="fecha_retiro"]').value);

        if (fechaRetiro < fechaIngreso) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La fecha de retiro no puede ser anterior a la fecha de ingreso'
            });
        }
    });
    </script>
</body>
</html>
