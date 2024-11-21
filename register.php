<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container .error-message {
            color: #ff3860;
            background-color: #ffe5e5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .form-container .success-message {
            color: #23d160;
            background-color: #e8f9e2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .form-container label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-container input,
        .form-container select {
            margin-bottom: 15px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container input[type="submit"] {
            background-color: #00d1b2;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #00c4a7;
        }
    </style>
</head>
<body>

    <div class="form-container box">
        <h2 class="title is-4">Registro de Usuario</h2>

        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
        }

        if (isset($_GET['success'])) {
            echo '<p class="success-message">¡Usuario registrado exitosamente!</p>';
        }
        ?>

        <form action="config/procesar_registro.php" method="POST">
            <div class="field">
                <label for="nombre_usuario" class="label">Nombre de Usuario:</label>
                <div class="control">
                    <input class="input" type="text" id="nombre_usuario" name="nombre_usuario" required>
                </div>
            </div>

            <div class="field">
                <label for="email" class="label">Correo Electrónico:</label>
                <div class="control">
                    <input class="input" type="email" id="email" name="email" required>
                </div>
            </div>

            <div class="field">
                <label for="password" class="label">Contraseña:</label>
                <div class="control">
                    <input class="input" type="password" id="password" name="password" required>
                </div>
            </div>

            <div class="field">
                <label for="rol" class="label">Rol:</label>
                <div class="control">
                    <select class="select" id="rol" name="rol" required>
                        <option value="2">Usuario</option>
                        <option value="1">Administrador</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <input class="button is-primary" type="submit" value="Registrarse">
                </div>
            </div>
        </form>
    </div>

</body>
</html>
