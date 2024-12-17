<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area'])) { 
    $area = $_POST['area']; 

    // Preparar la consulta SQL con el filtro
    $sql = "
        SELECT e.id AS empleado_id, e.nombres, e.apellidos, e.cedula AS documento, e.area, e.cargo, p.estado, p.id AS paz_y_salvo_id
        FROM empleados e
        INNER JOIN paz_y_salvo p ON e.id = p.empleado_id
        WHERE 1=1 "; // Siempre verdadero para poder agregar condiciones con AND

    if (!empty($area)) {
        $sql .= " AND e.area = ? ";
    }

    $sql .= " AND NOT EXISTS (
            SELECT 1
            FROM firmas f
            WHERE f.paz_y_salvo_id = p.id
            AND f.departamento = ?
        )";

    $stmt = DatabaseConfig::getConnection()->prepare($sql);

    if (!empty($area)) {
        $stmt->bind_param("ss", $area, $area); // Vincular el parámetro $area dos veces si es necesario
    } else {
        $stmt->bind_param("s", $area); // Vincular solo una vez si $area está vacío
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $empleados = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Devolver los resultados en formato JSON
    echo json_encode($empleados); 
}
?>