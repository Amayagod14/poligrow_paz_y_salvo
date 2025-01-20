<?php
session_start();
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar transacción
        DatabaseConfig::getConnection()->begin_transaction();

        // Convertir fechas al formato yyyy-mm-dd
        $fecha_ingreso = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
        $fecha_retiro = date('Y-m-d', strtotime($_POST['fecha_retiro']));
        
        // Obtener el ID del empleado
        $empleado_id = $_POST['empleado_id'];

        // Actualizar la información del empleado
        $stmt = DatabaseConfig::getConnection()->prepare("
            UPDATE empleados SET 
                nombres = ?,
                apellidos = ?, 
                cedula = ?,
                cargo = ?,
                area = ?, 
                fecha_ingreso = ?, 
                fecha_retiro = ?, 
                motivo_retiro = ?
            WHERE id = ?
        ");

        // Vincular parámetros
        $stmt->bind_param("ssssssssi",
            $_POST['nombres'],
            $_POST['apellidos'],
            $_POST['cedula'],
            $_POST['cargo'],
            $_POST['area'],
            $fecha_ingreso,
            $fecha_retiro,
            $_POST['motivo_retiro'],
            $empleado_id
        );

        // Ejecutar la actualización del empleado
        if ($stmt->execute()) {
            // Actualizar el estado del paz y salvo a 'en_proceso'
            $stmt_paz = DatabaseConfig::getConnection()->prepare("
                UPDATE paz_y_salvo 
                SET estado = 'en_proceso'
                WHERE empleado_id = ?
            ");
            $stmt_paz->bind_param("i", $empleado_id);
            
            if ($stmt_paz->execute()) {
                // Obtener el ID del paz y salvo
                $stmt_get_paz = DatabaseConfig::getConnection()->prepare("
                    SELECT id FROM paz_y_salvo WHERE empleado_id = ?
                ");
                $stmt_get_paz->bind_param("i", $empleado_id);
                $stmt_get_paz->execute();
                $result = $stmt_get_paz->get_result();
                $paz_y_salvo = $result->fetch_assoc();
                $paz_y_salvo_id = $paz_y_salvo['id'];

                // Confirmar la transacción
                DatabaseConfig::getConnection()->commit();

                // Devolver respuesta JSON
                echo json_encode([
                    'success' => true,
                    'empleado_id' => $empleado_id,
                    'paz_y_salvo_id' => $paz_y_salvo_id,
                    'message' => 'Información actualizada correctamente'
                ]);
            } else {
                throw new Exception("Error al actualizar el estado del paz y salvo");
            }
        } else {
            throw new Exception("Error al actualizar la información del empleado");
        }

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        DatabaseConfig::getConnection()->rollback();

        // Devolver error en formato JSON
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } finally {
        // Cerrar las conexiones
        if (isset($stmt)) $stmt->close();
        if (isset($stmt_paz)) $stmt_paz->close();
        if (isset($stmt_get_paz)) $stmt_get_paz->close();
        DatabaseConfig::getConnection()->close();
    }
}
