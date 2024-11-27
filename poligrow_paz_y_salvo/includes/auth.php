<?php
require_once 'database.php';

function login($cedula, $password) {
    $user = getUserByCedula($cedula);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['es_admin'] = $user['es_admin'];
        return true;
    }
    return false;
}

function register($cedula, $nombre, $cargo, $area, $password) {
    $conn = DatabaseConfig::getConnection();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cambiar la consulta para usar la tabla 'empleados'
    $stmt = $conn->prepare("INSERT INTO empleados (cedula, nombre, cargo, area, password) VALUES (?, ?, ?, ?, ?)"); 
    $stmt->bind_param("sssss", $cedula, $nombre, $cargo, $area, $hashed_password);
    return $stmt->execute();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logout() {
    session_destroy();
}

// ... otras funciones de autenticación ...
?>