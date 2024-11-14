<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
</head>
<body>

    <div class="form-container">
        <h2>Registro de Usuario</h2>
        
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error-message">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
        }

        if (isset($_GET['success'])) {
            echo '<p class="success-message">¡Usuario registrado exitosamente!</p>';
        }
        ?>

        <form action="config/procesar_registro.php" method="POST">
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="2">Usuario</option>
                <option value="1">Administrador</option>
            </select>

            <input type="submit" value="Registrarse">
        </form>
    </div>

</body>
</html>
