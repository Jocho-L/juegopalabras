<?php

const SERVER = "localhost";
const DB = "juegopalabras";
const USER = "root";
const PASS = "";
const SGBD = "mysql:host=" . SERVER . ";port=3307;dbname=" . DB . ";charset=UTF8"; // Cambié "portname" a "port"

class Conexion {

  /**
   * Retorna la conexión al servidor y BD utilizando patrón SINGLETON y de acceso restringido.
   */
  protected static $conexion;

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

  public static function getData($storeProcedure): array {
    return [];
  }
}


