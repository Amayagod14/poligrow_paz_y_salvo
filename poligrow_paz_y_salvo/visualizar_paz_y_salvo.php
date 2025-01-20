<?php
require_once 'includes/database.php';
require_once 'logica_paz_y_salvo.php'; 

// Verificar que se recibió el ID del empleado
if (!isset($_GET['empleado_id'])) {
    die('ID de empleado no especificado');
}

// Obtener el ID del empleado de la URL
$empleado_id = $_GET['empleado_id'];

// Establecer los headers para PDF
header('Content-Type: application/pdf');
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

// Generar y mostrar el PDF
$pazYSalvo = new PazYSalvo(); 
$pazYSalvo->visualizarPazYSalvo($empleado_id);
?>
