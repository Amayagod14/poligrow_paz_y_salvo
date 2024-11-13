<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empleado_id'])) {
  $empleado_id = $_POST['empleado_id'];

  // Eliminar las firmas relacionadas
  $stmt = DatabaseConfig::getConnection()->prepare("DELETE FROM firmas WHERE paz_y_salvo_id = ?");
  $stmt->bind_param("i", $empleado_id);
  $stmt->execute();

  // Eliminar el empleado
  $stmt = DatabaseConfig::getConnection()->prepare("DELETE FROM empleados WHERE id = ?");
  $stmt->bind_param("i", $empleado_id);
  if ($stmt->execute()) {
    echo "Paz y Salvo eliminado correctamente.";
  } else {
    echo "Error al eliminar el Paz y Salvo.";
  }
  $stmt->close();
}
?>