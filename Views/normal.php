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

include_once '../models/Conexion.php';
$pdo = Conexion::getConexion();

// Inicializar el juego o restablecer si es necesario
if (!isset($_SESSION['palabraSecreta']) || isset($_POST['nueva_palabra'])) {
    // Seleccionar una palabra aleatoria de la base de datos
    $stmt = $pdo->query("SELECT palabra, id FROM Palabras ORDER BY RAND() LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['palabraSecreta'] = $row['palabra']; // Palabra aleatoria de la base de datos
    $_SESSION['idPalabra'] = $row['id']; // ID de la palabra
    $_SESSION['letrasAdivinadas'] = str_repeat("_", strlen($_SESSION['palabraSecreta']));
    $_SESSION['intentosFallidos'] = 0;
    $_SESSION['intentosMaximos'] = 4;
    $_SESSION['letrasIntentadas'] = [];

    // Inicializar o mantener el contador de racha
    if (!isset($_SESSION['racha'])) {
        $_SESSION['racha'] = 0;
    }

    // Obtener las imágenes asociadas a la palabra
    $stmtImagenes = $pdo->prepare("SELECT ruta_imagen FROM Imagenes WHERE id_palabra = :id_palabra");
    $stmtImagenes->execute(['id_palabra' => $_SESSION['idPalabra']]);
    $_SESSION['imagenes'] = $stmtImagenes->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <section class="section">
        <div class="container">
            <div class="box has-text-centered">
                <h1 class="title is-1">Juego de Ahorcados</h1>
                <p class="subtitle is-3">Progreso: <?= implode(" ", str_split($_SESSION['letrasAdivinadas'])); ?></p>

                <div class="content">
                    <p><strong>Intentos restantes:</strong> <?= $_SESSION['intentosMaximos'] - $_SESSION['intentosFallidos']; ?></p>
                    <p><strong>Letras intentadas:</strong> <?= implode(", ", $_SESSION['letrasIntentadas']); ?></p>
                    <p><strong>Racha actual:</strong> <?= $_SESSION['racha']; ?></p>
                </div>

                <a class="button is-danger is-dark" href="reset.php">Volver al menú</a>
            </div>

            <!-- Mostrar imágenes relacionadas con la palabra -->
            <div class="columns is-centered is-multiline mt-4">
                <?php if (!empty($_SESSION['imagenes'])): ?>
                    <?php foreach ($_SESSION['imagenes'] as $imagen): ?>
                        <div class="column is-3">
                            <figure class="image is-228x228">
                                <img src="../imagenes/<?= htmlspecialchars($imagen['ruta_imagen']); ?>" alt="Imagen asociada">
                            </figure>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="box has-text-centered mt-4">
                <?php if ($juegoTerminado): ?>
                    <?php if ($palabraCompleta): ?>
                        <p class="has-text-success">¡Felicidades! Has adivinado la palabra '<?= $_SESSION['palabraSecreta']; ?>'.</p>
                        <form method="post">
                            <button type="submit" name="nueva_palabra" value="1" class="button is-link mt-3">Pasar a la siguiente palabra</button>
                        </form>
                    <?php else: ?>
                        <p class="has-text-danger">Lo siento, has perdido. La palabra era '<?= $_SESSION['palabraSecreta']; ?>'.</p>
                        <a href="reset.php" class="button is-warning mt-3">Resetear</a>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="post">
                        <div class="field">
                            <label for="letra" class="label">Introduce una letra:</label>
                            <div class="control">
                                <input class="input is-medium" type="text" name="letra" id="letra" maxlength="1" required>
                            </div>
                        </div>
                        <button type="submit" class="button is-success mt-3">Adivinar</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Script para autoseleccionar el input -->
    <script>
        window.onload = function() {
            document.getElementById("letra").select(); // Selecciona el texto del input
        }
    </script>

</body>

</html>