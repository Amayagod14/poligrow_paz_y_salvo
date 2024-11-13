<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = DatabaseConfig::getConnection()->prepare("
        INSERT INTO empleados (
            nombre, 
            documento, 
            cargo,
            area, 
            fecha_ingreso, 
            fecha_retiro, 
            motivo_retiro,
            estado 
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $fecha_ingreso = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
    $fecha_retiro = date('Y-m-d', strtotime($_POST['fecha_retiro']));
    $estado = 'pendiente'; // Estado inicial del Paz y Salvo
    
    $stmt->bind_param("sssssssss", 
        $_POST['nombre'],
        $_POST['documento'],
        $_POST['cargo'],
        $_POST['area'],
        $fecha_ingreso,
        $fecha_retiro,
        $_POST['motivo_retiro'],
        $estado
    );
    
    if ($stmt->execute()) {
        echo $stmt->insert_id; // Devolver el ID del nuevo empleado
    } else {
        echo "Error al guardar el empleado.";
    }
    $stmt->close();
}
?>