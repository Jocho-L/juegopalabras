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
            $_SESSION['tiempoRestante'] += 5; // Añadir tiempo por acierto
        } else {
            $_SESSION['intentosFallidos']++;
            $_SESSION['tiempoRestante'] -= 3; // Reducir tiempo por fallo
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
            if (tiempoRestante <= 0) {
                clearInterval(intervalo);
                document.getElementById("form-letra").submit(); // Finaliza el juego automáticamente
            }
            tiempoRestante--;
        }

        const intervalo = setInterval(actualizarTemporizador, 1000);
    </script>
</head>
<body>
<div>
    <h1 class="title is-1">Juego de Ahorcados</h1>
    <p class="title is-3">Progreso: <?= implode(" ", str_split($_SESSION['letrasAdivinadas'])); ?></p>
    <div class="title is-4">
        <p id="temporizador">Tiempo restante: <?= $_SESSION['tiempoRestante']; ?>s</p>
        <p>Intentos restantes: <?= $_SESSION['intentosMaximos'] - $_SESSION['intentosFallidos']; ?></p>
        <p>Letras intentadas: <?= implode(", ", $_SESSION['letrasIntentadas']); ?></p>
        <p>Racha actual: <?= $_SESSION['racha']; ?></p>
    </div>
    <a class="button is-danger is-dark" href="reset.php">Volver al menú</a>
</div>

<div class="imagenes">
    <?php if (!empty($_SESSION['imagenes'])): ?>
        <?php foreach ($_SESSION['imagenes'] as $imagen): ?>
            <img src="../imagenes/<?= htmlspecialchars($imagen['ruta_imagen']); ?>" alt="Imagen asociada" class="image is-128x128">
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($juegoTerminado): ?>
    <?php if ($palabraCompleta): ?>
        <p>¡Felicidades! Has adivinado la palabra '<?= $_SESSION['palabraSecreta']; ?>'.</p>
        <form method="post">
            <button type="submit" name="nueva_palabra" value="1">Pasar a la siguiente palabra</button>
        </form>
    <?php else: ?>
        <p>Lo siento, has perdido. La palabra era '<?= $_SESSION['palabraSecreta']; ?>'.</p>
        <a href="reset.php" class="button is-warning is-dark">Reiniciar</a>
    <?php endif; ?>
<?php else: ?>
    <form method="post" id="form-letra">
        <label for="letra" class="title is-5">Introduce una letra:</label>
        <input class="box field" type="text" name="letra" id="letra" maxlength="1" required>
        <button type="submit" class="button is-success">Adivinar</button>
    </form>
<?php endif; ?>
</body>
</html>
