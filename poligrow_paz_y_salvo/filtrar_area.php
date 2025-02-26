<?php
require_once 'includes/database.php';

try {
    $conn = DatabaseConfig::getConnection();

    // Obtener el área seleccionada del POST
    $areaSeleccionada = isset($_POST['area']) ? trim($_POST['area']) : '';

    // Consulta SQL para filtrar los paz y salvos por área
    $sql = "SELECT 
                e.id as empleado_id,
                e.cedula as documento,
                e.nombres,
                e.apellidos,
                e.cargo,
                e.area,
                COALESCE(ps.estado, 'pendiente') as estado
            FROM empleados e
            LEFT JOIN (
                SELECT empleado_id, estado
                FROM paz_y_salvo
                WHERE id IN (
                    SELECT MAX(id)
                    FROM paz_y_salvo
                    GROUP BY empleado_id
                )
            ) ps ON e.id = ps.empleado_id";

    // Si se ha seleccionado un área, agregar la condición a la consulta
    if (!empty($areaSeleccionada)) {
        $sql .= " WHERE e.area = ?";
    }

    $sql .= " AND COALESCE(ps.estado, 'pendiente') = 'en_proceso' ORDER BY e.nombres ASC";

    $stmt = $conn->prepare($sql);

    // Si se ha seleccionado un área, vincular el parámetro
    if (!empty($areaSeleccionada)) {
        $stmt->bind_param("s", $areaSeleccionada);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $resultados = [];

    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
    }

    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($resultados);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>
