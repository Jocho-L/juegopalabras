<?php

// Incluir el archivo de conexión
require_once '../models/Conexion.php'; // Archivo de conexión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUsuario'])) {
    // Redirigir a la página de login si no hay sesión activa
    header("Location: ../login.php");
    exit();
}
// Conectar a la base de datos para obtener el nombre del usuario
$idUsuario = $_SESSION['idUsuario'];

// Usar la clase Conexion para ejecutar la consulta
$query = "SELECT nombre_usuario FROM Usuarios WHERE idUsuario = ?";
$stmt = Conexion::ejecutarConsulta($query, [$idUsuario]);

// Obtener el resultado
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$nombreUsuario = $user['nombre_usuario'];
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seleccion - <?php echo $nombreUsuario; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="../styles.css">
  </head>
  <body>
    <section class="section">
      <div class="contenedor">
        <h1 class="title">
          ¡Bienvenido, <?php echo $nombreUsuario; ?>!
        </h1>
        <p class="subtitle">
          Apartado de <strong>Selección</strong>!
        </p>
        <div>
          <a class="button is-primary mb-3" href="normal.php">Normal</a>
          <a class="button is-primary mb-3" href="temporizado.php">Temporizado</a>
          <a class="button is-danger mb-3" href="unavida.php">Una vida</a>
        </div>
        <a class="button is-link mb-3" href="create.html">Crear +</a>
      </div>
      <div>
        <a href="../config/logout.php">Salir</a>
      </div>

      <audio autoplay loop>
        <source src="../sounds/index.mp3" type="audio/mp3">
        Tu navegador no soporta el elemento de audio.
      </audio>
    </section>
  </body>
</html>
