<?php

const SERVER = "localhost";
const DB = "juegopalabras";
const USER = "root";
const PASS = "";
const SGBD = "mysql:host=" . SERVER . ";port=3306;dbname=" . DB . ";charset=UTF8"; // Cambié "portname" a "port"

class Conexion {

    // Variable estática para la conexión (Patrón Singleton)
    protected static $conexion;

    /**
     * Retorna la conexión al servidor y BD utilizando patrón SINGLETON y de acceso restringido.
     */
    public static function getConexion() {
        if (!isset(self::$conexion)) {
            try {
                self::$conexion = new PDO(SGBD, USER, PASS);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                die("Error en la conexión: " . $e->getMessage());
            }
        }
        return self::$conexion;
    }

    /**
     * Ejecuta una consulta y devuelve el resultado
     * @param string $query La consulta SQL a ejecutar
     * @param array $params Los parámetros de la consulta (opcional)
     * @return PDOStatement
     */
    public static function ejecutarConsulta($query, $params = []) {
        try {
            $conn = self::getConexion();
            $stmt = $conn->prepare($query);
    
            // Asegurarse de que los parámetros comiencen desde el índice 1
            foreach ($params as $index => $value) {
                // El índice de PDO debe comenzar desde 1
                $stmt->bindValue($index + 1, $value);
            }
    
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
    
}
?>
