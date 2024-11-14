<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");  // Si no está logueado, redirigir al login
    exit();
}

$user = $_SESSION['usuario'];  // Obtener los datos del usuario desde la sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Juego de Palabras</title>
</head>
<body>
    <h1>Bienvenido, <?php echo $user['nombre_usuario']; ?>!</h1>
    <p>Rol: <?php echo $user['nombre_rol']; ?></p>

    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
