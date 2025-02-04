<?php
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'config_mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function enviarNotificacionPazYSalvo($empleado_id) {
    $conn = DatabaseConfig::getConnection();
    
    // Verificar conexión
    if (!$conn) {
        error_log("Error de conexión a la base de datos.");
        return false;
    }

    // Obtener información del empleado
    $stmt = $conn->prepare("SELECT nombres, apellidos, cedula, area, cargo FROM empleados WHERE id = ?");
    
    // Verificar preparación de la consulta
    if (!$stmt) {
        error_log("Error al preparar la consulta: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $empleado = $stmt->get_result()->fetch_assoc();

    if (!$empleado) {
        error_log("Empleado no encontrado con ID: $empleado_id");
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración de SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Desactivar depuración en producción
        $mail->isSMTP();
        $mail->Host = SMTP_HOST; // smtp.office365.com
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER; // notificacionesmesadeayuda@poligrow.com
        $mail->Password = SMTP_PASS; // Contraseña de la cuenta de Office 365
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cambiar a STARTTLS
        $mail->Port = 587; // Puerto para Office 365
        
        $mail->setFrom(SMTP_FROM, SMTP_NAME);
        
        // Añadir destinatarios
        foreach (IT_EMAILS as $email) {
            $mail->addAddress($email);
        }
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Nuevo Paz y Salvo por Revisar - ' . $empleado['nombres'] . ' ' . $empleado['apellidos'];
        
        $mensaje = "
        <h2>Nuevo Paz y Salvo Pendiente de Revisión</h2>
        <p>Se ha generado un nuevo paz y salvo para el siguiente empleado:</p>
        <ul>
            <li><strong>Nombre:</strong> {$empleado['nombres']} {$empleado['apellidos']}</li>
            <li><strong>Cédula:</strong> {$empleado['cedula']}</li>
            <li><strong>Área:</strong> {$empleado['area']}</li>
            <li><strong>Cargo:</strong> {$empleado['cargo']}</li>
        </ul>
        <p>Por favor, revise el sistema para aprobar o rechazar este paz y salvo.</p>";
        
        $mail->Body = $mensaje;
        
        // Guardar log de intento de envío
        error_log("Intentando enviar correo a: " . implode(', ', IT_EMAILS) . " - " . date('Y-m-d H:i:s'));
        
        $mail->send();
        
        // Log de éxito
        error_log("Correo enviado exitosamente - " . date('Y-m-d H:i:s'));
        return true;
        
    } catch (Exception $e) {
        // Log de error
        error_log("Error al enviar correo: " . $mail->ErrorInfo . " - " . date('Y-m-d H:i:s'));
        return false;
    }
}
?>
