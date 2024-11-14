<?php
session_start();
require_once 'includes/auth.php';

logout(); // Llamar a la función logout() de auth.php
echo "Sesión cerrada correctamente."; 
?>