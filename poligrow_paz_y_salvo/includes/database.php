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
            
            self::$instance->set_charset("utf8");
        }
        return self::$instance;
    }
}

// Ejemplo de función para obtener un usuario por email
function getUserByEmail($email) {
    $conn = DatabaseConfig::getConnection();
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// ... otras funciones para interactuar con la base de datos ...

?>