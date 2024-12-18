<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['departamento'])) { 
    $departamento = $_POST['departamento']; 

    // Preparar la consulta SQL con el filtro
    $sql = "
        SELECT e.id AS empleado_id, e.nombres, e.apellidos, e.cedula AS documento, e.area, e.cargo, p.estado, p.id AS paz_y_salvo_id
        FROM empleados e
        INNER JOIN paz_y_salvo p ON e.id = p.empleado_id
        WHERE e.es_admin = 0  -- Solo empleados regulares
        AND NOT EXISTS (
            SELECT 1
            FROM firmas f
            WHERE f.paz_y_salvo_id = p.id
            AND f.departamento = ?
        )
    ";

    $stmt = DatabaseConfig::getConnection()->prepare($sql);
    $stmt->bind_param("s", $departamento);

    $stmt->execute();
    $result = $stmt->get_result();
    $empleados = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Devolver los resultados en formato JSON
    echo json_encode($empleados); 
}
?>