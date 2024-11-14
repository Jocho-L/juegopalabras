<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    // Si no está autenticado, redirigir al login
    header("Location: login.php?error=Por favor, inicia sesión primero.");
    exit;
}

// Mostrar contenido protegido
echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['nombre_usuario']) . "!</h1>";
echo "<p>Esta es una página protegida solo para usuarios registrados.</p>";

// Si es un administrador, puedes mostrar contenido diferente
if ($_SESSION['idRol'] == 1) {
    echo "<p>Acceso de administrador habilitado.</p>";
}

echo "<p><a href='logout.php'>Cerrar sesión</a></p>";
?>
