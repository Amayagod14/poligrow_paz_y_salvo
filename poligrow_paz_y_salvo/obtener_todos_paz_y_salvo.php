<?php
require_once 'includes/database.php';

// Preparar la consulta SQL
$sql = "
    SELECT e.id AS empleado_id, e.nombres, e.apellidos, e.cedula AS documento, e.area, e.cargo, p.estado, p.id AS paz_y_salvo_id
    FROM empleados e
    INNER JOIN paz_y_salvo p ON e.id = p.empleado_id
";

$stmt = DatabaseConfig::getConnection()->prepare($sql);

$stmt->execute();
$result = $stmt->get_result();
$empleados = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Devolver los resultados en formato JSON
echo json_encode($empleados); 
?>