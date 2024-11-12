<?php
session_start();
session_unset();    // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión

// Redirige de vuelta al juego
header("Location: seleccion.html");
exit();
