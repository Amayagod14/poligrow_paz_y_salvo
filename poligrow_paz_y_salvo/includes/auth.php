<?php

require_once 'database.php';

function login($cedula, $password) {
    $user = getUserByCedula($cedula);
    
    // Verificación detallada del usuario
    if (!$user) {
        return ['success' => false, 'message' => 'Usuario no encontrado'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Contraseña incorrecta'];
    }
    
    // Si llegamos aquí, las credenciales son correctas
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['cedula'] = $user['cedula'];
    $_SESSION['nombres'] = $user['nombres'];
    $_SESSION['apellidos'] = $user['apellidos'];
    $_SESSION['es_admin'] = $user['es_admin'];
    $_SESSION['area'] = $user['area'];
    $_SESSION['cargo'] = $user['cargo'];
    
    return [
        'success' => true,
        'es_admin' => $user['es_admin'],
        'user' => [
            'id' => $user['id'],
            'cedula' => $user['cedula'],
            'nombres' => $user['nombres'],
            'apellidos' => $user['apellidos'],
            'area' => $user['area'],
            'cargo' => $user['cargo']
        ]
    ];
}

function register($cedula, $nombres, $apellidos, $cargo, $area, $password) {
    $conn = DatabaseConfig::getConnection();
    
    // Verificar si el usuario ya existe
    if (getUserByCedula($cedula)) {
        return ['success' => false, 'message' => 'La cédula ya está registrada'];
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $fecha_ingreso = date('Y-m-d');
    
    $stmt = $conn->prepare("INSERT INTO empleados (cedula, nombres, apellidos, cargo, area, password, fecha_ingreso, es_admin) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sssssss", $cedula, $nombres, $apellidos, $cargo, $area, $hashed_password, $fecha_ingreso);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Usuario registrado correctamente'];
    } else {
        return ['success' => false, 'message' => 'Error al registrar usuario'];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isSuperAdmin() {
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 4;
}

function isAdmin() {
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] >= 1;
}

function logout() {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return getUserById($_SESSION['user_id']);
}

// Función para verificar permisos específicos
function checkPermission($requiredLevel) {
    if (!isLoggedIn()) {
        return false;
    }
    return $_SESSION['es_admin'] >= $requiredLevel;
}
?>
