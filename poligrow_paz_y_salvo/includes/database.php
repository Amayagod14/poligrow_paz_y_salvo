<?php

// Configuración de la base de datos
class DatabaseConfig {
    private const DB_HOST = '127.0.0.1';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_NAME = 'poligrow_paz_y_salvo';
    
    private static $instance = null;
    
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new mysqli(
                self::DB_HOST, 
                self::DB_USER, 
                self::DB_PASS, 
                self::DB_NAME
            );
            
            if (self::$instance->connect_error) {
                die("Error de conexión: " . self::$instance->connect_error);
            }
            
            self::$instance->set_charset("utf8mb4");

        }
        return self::$instance;
    }
}

// Obtener un usuario por cédula
function getUserByCedula($cedula) {
    $conn = DatabaseConfig::getConnection();
    $stmt = $conn->prepare("SELECT id, cedula, nombres, apellidos, cargo, area, password, es_admin FROM empleados WHERE cedula = ?");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Obtener un usuario por ID
function getUserById($id) {
    $conn = DatabaseConfig::getConnection();
    // Cambiar la consulta para usar la tabla 'empleados'
    $stmt = $conn->prepare("SELECT * FROM empleados WHERE id = ?"); 
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc(); 
}

// ... otras funciones para interactuar con la base de datos ...

?>