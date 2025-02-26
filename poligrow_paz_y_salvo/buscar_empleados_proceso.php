<?php
require_once 'includes/database.php';

try {
    $conn = DatabaseConfig::getConnection();
    $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';
    
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

    // Modificar la consulta para incluir solo los que están en proceso
    if (!empty($busqueda)) {
        $sql .= " WHERE 
            (e.cedula LIKE ? OR
            e.nombres LIKE ? OR
            e.apellidos LIKE ? OR
            e.cargo LIKE ? OR
            e.area LIKE ?)
            AND COALESCE(ps.estado, 'pendiente') = 'en_proceso'";
    } else {
        // Si no hay búsqueda, solo mostrar los que están en proceso
        $sql .= " WHERE COALESCE(ps.estado, 'pendiente') = 'en_proceso'";
    }

    $sql .= " ORDER BY e.nombres ASC";

    $stmt = $conn->prepare($sql);

    if (!empty($busqueda)) {
        $busquedaParam = "%{$busqueda}%";
        $params = array_fill(0, 5, $busquedaParam);
        $stmt->bind_param("sssss", ...$params);
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
