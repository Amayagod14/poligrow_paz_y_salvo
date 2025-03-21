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

    if (!empty($busqueda)) {
        $sql .= " WHERE 
            e.cedula LIKE ? OR
            e.nombres LIKE ? OR
            e.apellidos LIKE ? OR
            e.cargo LIKE ? OR
            e.area LIKE ?";
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
        if (empty($row['estado'])) {
            $row['estado'] = 'pendiente';
        }
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
