<?php
session_start();

// Tiempo inicial y ajustes de tiempo (en segundos)
$tiempoInicial = 60; // Tiempo en segundos para cada partida
$tiempoExtraCorrecto = 5; // Segundos a añadir por respuesta correcta
$tiempoPenalizacionIncorrecto = 10; // Segundos a restar por error

// Inicializar el juego o restablecer si es necesario
if (!isset($_SESSION['palabraSecreta']) || isset($_POST['nueva_palabra'])) {
    $palabras = ["ahorcado", "programacion", "desarrollo", "servidor", "navegador"];
    $_SESSION['palabraSecreta'] = $palabras[array_rand($palabras)];
    $_SESSION['letrasAdivinadas'] = str_repeat("_", strlen($_SESSION['palabraSecreta']));
    $_SESSION['intentosFallidos'] = 0;
    $_SESSION['intentosMaximos'] = 6;
    $_SESSION['letrasIntentadas'] = [];

    // Inicializar tiempo restante y contador de racha
    $_SESSION['tiempoRestante'] = $tiempoInicial;
    if (!isset($_SESSION['racha'])) {
        $_SESSION['racha'] = 0;
    }
}

// Asegurarse de que tiempo restante esté inicializado
if (!isset($_SESSION['tiempoRestante'])) {
    $_SESSION['tiempoRestante'] = $tiempoInicial;
}

// Procesar la letra enviada por el usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['letra'])) {
    $letra = strtolower($_POST['letra']);

    if (!in_array($letra, $_SESSION['letrasIntentadas'])) {
        $_SESSION['letrasIntentadas'][] = $letra;

        if (strpos($_SESSION['palabraSecreta'], $letra) !== false) {
            for ($i = 0; $i < strlen($_SESSION['palabraSecreta']); $i++) {
                if ($_SESSION['palabraSecreta'][$i] === $letra) {
                    $_SESSION['letrasAdivinadas'][$i] = $letra;
                }
            }
            $_SESSION['tiempoRestante'] += $tiempoExtraCorrecto; // Aumentar tiempo por respuesta correcta
        } else {
            $_SESSION['intentosFallidos']++;
            $_SESSION['tiempoRestante'] -= $tiempoPenalizacionIncorrecto; // Reducir tiempo por error
        }
    }
}

// Verificar si el jugador ganó o perdió
$palabraCompleta = strpos($_SESSION['letrasAdivinadas'], "_") === false;
$juegoTerminado = $_SESSION['intentosFallidos'] >= $_SESSION['intentosMaximos'] || $_SESSION['tiempoRestante'] <= 0;

if ($palabraCompleta) {
    $_SESSION['racha']++;
    $juegoTerminado = true;
} elseif ($juegoTerminado) {
    $_SESSION['racha'] = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Juego de Ahorcados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="../styles.css">
    <script>
        let tiempoRestante = <?= $_SESSION['tiempoRestante']; ?>;

        function actualizarTemporizador() {
            if (tiempoRestante <= 0) {
                document.getElementById("form-letra").style.display = "none";
                document.getElementById("mensajeFin").textContent = "Tiempo agotado. Has perdido.";
            } else {
                document.getElementById("temporizador").textContent = tiempoRestante + "s";
                tiempoRestante--;
                setTimeout(actualizarTemporizador, 1000);
            }
        }

        window.onload = actualizarTemporizador;
    </script>
</head>
<body>
    <h1>Juego de Ahorcados</h1>
    <p>Progreso: <?= implode(" ", str_split($_SESSION['letrasAdivinadas'])); ?></p>
    <p>Intentos restantes: <?= $_SESSION['intentosMaximos'] - $_SESSION['intentosFallidos']; ?></p>
    <p>Letras intentadas: <?= implode(", ", $_SESSION['letrasIntentadas']); ?></p>
    <p>Racha actual: <?= $_SESSION['racha']; ?></p>
    <p>Tiempo restante: <span id="temporizador"><?= $_SESSION['tiempoRestante']; ?>s</span></p>
    <a class="button is-danger is-dark" href="seleccion.html">Volver al menu</a>

    <div id="mensajeFin"></div>

    <?php if ($juegoTerminado): ?>
        <?php if ($palabraCompleta): ?>
            <p>¡Felicidades! Has adivinado la palabra '<?= $_SESSION['palabraSecreta']; ?>'.</p>
            <form method="post">
                <button type="submit" name="nueva_palabra">Pasar a la siguiente palabra</button>
            </form>
        <?php else: ?>
            <p>Lo siento, has perdido. La palabra era '<?= $_SESSION['palabraSecreta']; ?>'.</p>
            <a href="reset.php" class="button is-warning is-dark">Reset</a>
        <?php endif; ?>
    <?php else: ?>
        <form method="post" id="form-letra">
            <label for="letra">Introduce una letra:</label>
            <input type="text" name="letra" id="letra" maxlength="1" required>
            <button type="submit">Adivinar</button>
        </form>
    <?php endif; ?>
</body>
</html>
