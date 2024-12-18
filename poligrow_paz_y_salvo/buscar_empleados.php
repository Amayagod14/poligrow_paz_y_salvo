<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['busqueda'])) { 
    $busqueda = $_POST['busqueda']; 

    // Preparar la consulta SQL con la búsqueda
    $sql = "
        SELECT e.id AS empleado_id, e.nombres, e.apellidos, e.cedula AS documento, e.area, e.cargo, p.estado, p.id AS paz_y_salvo_id
        FROM empleados e
        INNER JOIN paz_y_salvo p ON e.id = p.empleado_id
        WHERE e.nombres LIKE ? OR e.apellidos LIKE ? OR e.cedula LIKE ?
    ";

    $param = "%" . $busqueda . "%";
    $stmt = DatabaseConfig::getConnection()->prepare($sql);
    $stmt->bind_param("sss", $param, $param, $param);

    $stmt->execute();
    $result = $stmt->get_result();
    $empleados = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Devolver los resultados en formato JSON
    echo json_encode($empleados); 
}
?>