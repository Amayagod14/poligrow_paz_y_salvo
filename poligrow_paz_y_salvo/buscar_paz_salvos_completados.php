<?php
session_start();
require_once 'includes/database.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['es_admin'] != 3) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

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
                ps.created_at
            FROM empleados e
            INNER JOIN (
                SELECT empleado_id, estado, created_at
                FROM paz_y_salvo
                WHERE id IN (
                    SELECT MAX(id)
                    FROM paz_y_salvo
                    GROUP BY empleado_id
                )
                AND estado = 'completado'
            ) ps ON e.id = ps.empleado_id
            WHERE 1=1";

    if (!empty($busqueda)) {
        $busqueda = "%{$conn->real_escape_string($busqueda)}%";
        $sql .= " AND (
            e.cedula LIKE ? OR
            e.nombres LIKE ? OR
            e.apellidos LIKE ? OR
            e.cargo LIKE ? OR
            e.area LIKE ?
        )";
    }

    $sql .= " ORDER BY ps.created_at DESC";

    $stmt = $conn->prepare($sql);
    
    if (!empty($busqueda)) {
        $stmt->bind_param("sssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $resultados = [];
    
    while ($row = $result->fetch_assoc()) {
        array_walk($row, function(&$item) {
            $item = $item === null ? '' : $item;
        });
        $resultados[] = $row;
    }

    $stmt->close();
    
    header('Content-Type: application/json');
    echo json_encode($resultados);

} catch (Exception $e) {
    header('Content-Type: application/json');
    error_log("Error en la base de datos: " . $e->getMessage());
    echo json_encode([
        'error' => 'Error en la base de datos',
        'details' => $e->getMessage()
    ]);
}
?>
