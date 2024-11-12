<?php
session_start();

// Inicializar el juego o restablecer si es necesario
if (!isset($_SESSION['palabraSecreta']) || isset($_POST['nueva_palabra'])) {
    $palabras = ["ahorcado", "programacion", "desarrollo", "servidor", "navegador"];
    $_SESSION['palabraSecreta'] = $palabras[array_rand($palabras)]; // Palabra aleatoria
    $_SESSION['letrasAdivinadas'] = str_repeat("_", strlen($_SESSION['palabraSecreta']));
    $_SESSION['intentosFallidos'] = 0;
    $_SESSION['intentosMaximos'] = 6;
    $_SESSION['letrasIntentadas'] = []; // Letras intentadas por el usuario

    // Inicializar o mantener el contador de racha
    if (!isset($_SESSION['racha'])) {
        $_SESSION['racha'] = 0;
    }
}

// Procesar la letra enviada por el usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['letra'])) {
    $letra = strtolower($_POST['letra']);

    // Verificar si la letra ya fue intentada
    if (!in_array($letra, $_SESSION['letrasIntentadas'])) {
        $_SESSION['letrasIntentadas'][] = $letra;

        if (strpos($_SESSION['palabraSecreta'], $letra) !== false) {
            // Actualizar progreso si la letra está en la palabra
            for ($i = 0; $i < strlen($_SESSION['palabraSecreta']); $i++) {
                if ($_SESSION['palabraSecreta'][$i] === $letra) {
                    $_SESSION['letrasAdivinadas'][$i] = $letra;
                }
            }
        } else {
            $_SESSION['intentosFallidos']++;
        }
    }
}

// Verificar si el jugador ganó o perdió
$palabraCompleta = strpos($_SESSION['letrasAdivinadas'], "_") === false;
$juegoTerminado = $_SESSION['intentosFallidos'] >= $_SESSION['intentosMaximos'];

// Condiciones de fin de juego: ganar o perder
if ($palabraCompleta) {
    $_SESSION['racha']++; // Incrementar racha si gana
    $juegoTerminado = true;
} elseif ($juegoTerminado) {
    $_SESSION['racha'] = 0; // Reiniciar racha si pierde
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Juego de Ahorcados</title>
</head>
<body>
    <h1>Juego de Ahorcados</h1>
    <p>Progreso: <?= implode(" ", str_split($_SESSION['letrasAdivinadas'])); ?></p>
    <p>Intentos restantes: <?= $_SESSION['intentosMaximos'] - $_SESSION['intentosFallidos']; ?></p>
    <p>Letras intentadas: <?= implode(", ", $_SESSION['letrasIntentadas']); ?></p>
    <p>Racha actual: <?= $_SESSION['racha']; ?></p>

    <?php if ($juegoTerminado): ?>
        <?php if ($palabraCompleta): ?>
            <p>¡Felicidades! Has adivinado la palabra '<?= $_SESSION['palabraSecreta']; ?>'.</p>
            <form method="post">
                <button type="submit" name="nueva_palabra">Pasar a la siguiente palabra</button>
            </form>
        <?php else: ?>
            <p>Lo siento, has perdido. La palabra era '<?= $_SESSION['palabraSecreta']; ?>'.</p>
            <a href="reset.php">Reiniciar juego</a>
        <?php endif; ?>
    <?php else: ?>
        <form method="post">
            <label for="letra">Introduce una letra:</label>
            <input type="text" name="letra" id="letra" maxlength="1" required>
            <button type="submit">Adivinar</button>
        </form>
    <?php endif; ?>
</body>
</html>
