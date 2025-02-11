<?php
// config_mail.php
define('SMTP_HOST', 'smtp.office365.com'); // Servidor SMTP de Office 365
define('SMTP_USER', 'notificacionesmesadeayuda@poligrow.com'); // Tu correo de Office 365
define('SMTP_PASS', 'Sec_palmero1'); // Contraseña de la cuenta de Office 365
define('SMTP_PORT', 587); // Puerto para SMTP con TLS
define('SMTP_FROM', 'notificacionesmesadeayuda@poligrow.com');
define('SMTP_NAME', 'Sistema de Paz y Salvos Poligrow');

// Array de destinatarios
define('IT_EMAILS', [
    'sebascata2005@gmail.com',
]);
