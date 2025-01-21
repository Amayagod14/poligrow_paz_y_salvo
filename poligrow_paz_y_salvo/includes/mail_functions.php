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
    
    // Obtener información del empleado
    $stmt = $conn->prepare("SELECT nombres, apellidos, cedula, area, cargo FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $empleado = $stmt->get_result()->fetch_assoc();
    
    $mail = new PHPMailer(true);

    try {
        // Agregar debug temporal
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        
        // Si hay problemas con SSL, agrega esto:
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->setFrom(SMTP_FROM, SMTP_NAME);
        $mail->addAddress(IT_EMAIL);
        
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
        error_log("Intentando enviar correo a: " . IT_EMAIL . " - " . date('Y-m-d H:i:s'));
        
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

