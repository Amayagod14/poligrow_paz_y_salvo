<?php
require_once 'logica_paz_y_salvo.php';

if (isset($_GET['empleado_id'])) {
    $empleado_id = $_GET['empleado_id'];

    // Crear una instancia de la clase PazYSalvo
    $pazYSalvo = new PazYSalvo(); 

    // Obtener la información del empleado
    $empleado = $pazYSalvo->getEmpleadoInfoPublico($empleado_id); 

    // Crear el nombre del archivo usando la información del empleado
    $pdfFileName = $empleado['cedula'] . "_" . $empleado['nombres'] . "_" . $empleado['apellidos'] . ".pdf";
    // Limpiar el nombre del archivo
    $pdfFileName = preg_replace('/[^a-zA-Z0-9_\.]/', '', $pdfFileName);

    // Generar el PDF
    $pdfContent = $pazYSalvo->generarPDFPublico($empleado_id); 

    // Verificar que tenemos contenido
    if (empty($pdfContent)) {
        die('Error: No se pudo generar el contenido del PDF');
    }

    // Establecer las cabeceras para la descarga
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($pdfContent));

    // Enviar el contenido del PDF al navegador
    echo $pdfContent;
    exit;
}
?>
