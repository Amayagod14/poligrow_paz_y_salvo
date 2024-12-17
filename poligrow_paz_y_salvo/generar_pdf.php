<?php
require_once 'logica_paz_y_salvo.php';

if (isset($_GET['empleado_id'])) {
    $empleado_id = $_GET['empleado_id'];

    // Crear una instancia de la clase PazYSalvo
    $pazYSalvo = new PazYSalvo(); 

    // Llamar al método público que llama al método privado generarPDF()
    $pdfContent = $pazYSalvo->generarPDFPublico($empleado_id); 

    // Obtener el nombre del empleado y su cédula para el nombre del archivo (llamando al método público)
    $empleado = $pazYSalvo->getEmpleadoInfoPublico($empleado_id); 

    // Obtener la cédula y el nombre del empleado usando expresiones regulares
    preg_match('/con cedula de ciudadania N\.\s*(\d+)/', $empleado, $matches);
    $cedula = $matches[1];
    preg_match('/Poligrow\s*(.+?)\s*identificado/', $empleado, $matches);
    $nombre = $matches[1];
    $pdfFileName = $cedula . "_" . $nombre . ".pdf";

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