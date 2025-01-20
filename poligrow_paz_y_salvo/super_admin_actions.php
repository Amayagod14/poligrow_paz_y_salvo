<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Verificar que sea super admin
if (!isLoggedIn() || !isSuperAdmin()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener la conexión
try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

// Procesar la acción solicitada
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'obtener_admin':
        $id = intval($_POST['id']);
        $query = "SELECT id, cedula, nombres, apellidos, cargo, area, es_admin 
                  FROM empleados 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            
            if ($admin) {
                echo json_encode([
                    'success' => true,
                    'data' => $admin
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos'
            ]);
        }
        break;

    case 'actualizar_admin':
        $id = intval($_POST['id']);
        $cedula = $_POST['cedula'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $cargo = $_POST['cargo'];
        $area = $_POST['area'];
        $es_admin = intval($_POST['es_admin']);

        $query = "UPDATE empleados 
                  SET cedula = ?, 
                      nombres = ?, 
                      apellidos = ?, 
                      cargo = ?, 
                      area = ?, 
                      es_admin = ? 
                  WHERE id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssii", 
            $cedula, 
            $nombres, 
            $apellidos, 
            $cargo, 
            $area, 
            $es_admin, 
            $id
        );

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Datos actualizados correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar datos'
            ]);
        }
        break;

    case 'reset_password':
        $id = intval($_POST['id']);
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $query = "UPDATE empleados SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_password, $id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la contraseña'
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
        break;
}

$conn->close();
?>
