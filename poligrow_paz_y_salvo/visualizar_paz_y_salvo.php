<?php
require_once 'includes/database.php';
require_once 'logica_paz_y_salvo.php'; 

// Obtener el ID del empleado de la URL
$empleado_id = $_GET['empleado_id'];

// Generar el PDF en modo solo lectura
$pazYSalvo = new PazYSalvo(); 
$pazYSalvo->visualizarPazYSalvo($empleado_id);
?>