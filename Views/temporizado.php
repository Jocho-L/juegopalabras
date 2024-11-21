<?php
session_start();
include_once '../models/Conexion.php';
$pdo = Conexion::getConexion();

if (!isset($_SESSION['palabraSecreta']) || isset($_POST['nueva_palabra'])) {
    // Inicialización del juego
    $stmt = $pdo->query("SELECT palabra, id FROM Palabras ORDER BY RAND() LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['palabraSecreta'] = $row['palabra'];
    $_SESSION['idPalabra'] = $row['id'];
    $_SESSION['letrasAdivinadas'] = str_repeat("_", strlen($_SESSION['palabraSecreta']));
    $_SESSION['intentosFallidos'] = 0;
    $_SESSION['intentosMaximos'] = 4;
    $_SESSION['letrasIntentadas'] = [];
    $_SESSION['racha'] = $_SESSION['racha'] ?? 0;
    $_SESSION['tiempoRestante'] = 30; // Tiempo inicial en segundos

    // Obtener las imágenes asociadas a la palabra
    $stmtImagenes = $pdo->prepare("SELECT ruta_imagen FROM Imagenes WHERE id_palabra = :id_palabra");
    $stmtImagenes->execute(['id_palabra' => $_SESSION['idPalabra']]);
    $_SESSION['imagenes'] = $stmtImagenes->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar la letra enviada por el usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['letra'])) {
    $letra = strtolower($_POST['letra']);
    if (!in_array($letra, $_SESSION['letrasIntentadas'])) {
        $_SESSION['letrasIntentadas'][] = $letra;
        if (strpos($_SESSION['palabraSecreta'], $letra) !== false) {
            // Acierto: actualiza el progreso y añade tiempo
            for ($i = 0; $i < strlen($_SESSION['palabraSecreta']); $i++) {
                if ($_SESSION['palabraSecreta'][$i] === $letra) {
                    $_SESSION['letrasAdivinadas'][$i] = $letra;
                }
            }
            $_SESSION['tiempoRestante'] += 2; // Añadir tiempo por acierto
        } else {
            $_SESSION['intentosFallidos']++;
            $_SESSION['tiempoRestante'] -= 8; // Reducir tiempo por fallo
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
        let tiempoRestante = <?= $_SESSION['tiempoRestante'] ?>;

        function actualizarTemporizador() {
            document.getElementById("temporizador").textContent = "Tiempo restante: " + tiempoRestante + "s";
            if (tiempoRestante < 1) {
                clearInterval(intervalo);
                document.getElementById("form-letra").submit(); // Finaliza el juego automáticamente
                
            }
            tiempoRestante--;
        }

        const intervalo = setInterval(actualizarTemporizador, 1000);

        // Función para autoseleccionar el input cuando la página cargue
        window.onload = function() {
            document.getElementById("letra").select();
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Título principal -->
        <h1 class="title is-1 has-text-centered">Juego de Ahorcados</h1>

        <!-- Progreso del juego -->
        <div class="box">
            <p class="title is-3">Progreso: <?= implode(" ", str_split($_SESSION['letrasAdivinadas'])); ?></p>
            <p id="temporizador" class="subtitle is-4">Tiempo restante: <?= $_SESSION['tiempoRestante']; ?>s</p>
            <p class="subtitle is-4">Intentos restantes: <?= $_SESSION['intentosMaximos'] - $_SESSION['intentosFallidos']; ?></p>
            <p class="subtitle is-4">Letras intentadas: <?= implode(", ", $_SESSION['letrasIntentadas']); ?></p>
            <p class="subtitle is-4">Racha actual: <?= $_SESSION['racha']; ?></p>
        </div>

        <!-- Botón para volver al menú -->
        <div class="has-text-centered">
            <a class="button is-danger is-dark" href="reset.php">Volver al menú</a>
        </div>

        <!-- Imágenes asociadas al juego -->
        <?php if (!empty($_SESSION['imagenes'])): ?>
            <div class="columns is-multiline is-centered mt-5">
                <?php foreach ($_SESSION['imagenes'] as $imagen): ?>
                    <div class="column is-one-quarter">
                        <img src="../imagenes/<?= htmlspecialchars($imagen['ruta_imagen']); ?>" alt="Imagen asociada" class="image is-228x228">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Estado del juego (terminado o en curso) -->
        <?php if ($juegoTerminado): ?>
            <div class="box">
                <?php if ($palabraCompleta): ?>
                    <p class="has-text-success">¡Felicidades! Has adivinado la palabra '<strong><?= $_SESSION['palabraSecreta']; ?></strong>'.</p>
                    <form method="post">
                        <button type="submit" name="nueva_palabra" value="1" class="button is-primary">Pasar a la siguiente palabra</button>
                    </form>
                <?php else: ?>
                    <p class="has-text-danger">Lo siento, has perdido. La palabra era '<strong><?= $_SESSION['palabraSecreta']; ?></strong>'.</p>
                    <a href="reset.php" class="button is-warning is-dark">Reiniciar</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Formulario para adivinar una letra -->
            <div class="box">
                <form method="post" id="form-letra" class="has-text-centered">
                    <label for="letra" class="title is-5">Introduce una letra:</label>
                    <input class="input is-medium is-rounded" type="text" name="letra" id="letra" maxlength="1" required>
                    <button type="submit" class="button is-success is-medium">Adivinar</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

