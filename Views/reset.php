<?php
session_start();

// Solo reiniciar las variables del juego sin destruir la sesión
unset($_SESSION['palabraSecreta']);
unset($_SESSION['idPalabra']);
unset($_SESSION['letrasAdivinadas']);
unset($_SESSION['intentosFallidos']);
unset($_SESSION['letrasIntentadas']);
unset($_SESSION['imagenes']);
unset($_SESSION['racha']);

// Redirigir de vuelta a la página del juego
header("Location: seleccion.php");
exit();
