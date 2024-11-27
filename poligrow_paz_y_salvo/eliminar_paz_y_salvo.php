<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empleado_id'])) {
  $empleado_id = $_POST['empleado_id'];

  // Obtener el ID del paz y salvo asociado al empleado
  $stmt = DatabaseConfig::getConnection()->prepare("SELECT id FROM paz_y_salvo WHERE empleado_id = ?");
  $stmt->bind_param("i", $empleado_id);
  $stmt->execute();
  $stmt->bind_result($paz_y_salvo_id);
  $stmt->fetch();
  $stmt->close();

  // Eliminar las firmas relacionadas (opcional, si quieres eliminar las firmas también)
  $stmt = DatabaseConfig::getConnection()->prepare("DELETE FROM firmas WHERE paz_y_salvo_id = ?");
  $stmt->bind_param("i", $paz_y_salvo_id);
  $stmt->execute();

  // Eliminar el paz y salvo
  $stmt = DatabaseConfig::getConnection()->prepare("DELETE FROM paz_y_salvo WHERE id = ?");
  $stmt->bind_param("i", $paz_y_salvo_id);
  if ($stmt->execute()) {
    echo "Paz y Salvo eliminado correctamente.";
  } else {
    echo "Error al eliminar el Paz y Salvo.";
  }
  $stmt->close();
}
?>