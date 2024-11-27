<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Convertir fechas al formato yyyy-mm-dd antes de guardarlas
    $fecha_ingreso = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_ingreso'])));
    $fecha_retiro = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_retiro'])));

    // Obtener el ID del usuario de la sesión
    $usuario_id = $_SESSION['user_id'];

    // Obtener los valores de $_POST 
    $valores = [
        $_POST['nombre'],
        $_POST['cedula'],
        $_POST['cargo'],
        $_POST['area'],
        $fecha_ingreso,
        $fecha_retiro,
        $_POST['motivo_retiro']
    ];

    // Construir la cadena de tipos dinámicamente
    $tipos = str_repeat('s', count($valores));

    // Insertar el empleado en la tabla
    $stmt = DatabaseConfig::getConnection()->prepare("
        INSERT INTO empleados (
            nombre, 
            cedula,
            cargo,
            area, 
            fecha_ingreso, 
            fecha_retiro, 
            motivo_retiro
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param($tipos, ...$valores);
    $stmt->execute();
    $empleado_id = $stmt->insert_id;

    if ($empleado_id) {
        echo $empleado_id; // Devolver el ID del nuevo empleado
    } else {
        echo "Error al guardar el empleado.";
    }
    $stmt->close();
}
?>