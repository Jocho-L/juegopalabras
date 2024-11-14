<?php
session_start();

// Verificar si el usuario ya está logueado, si está logueado redirigirlo al dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

// Mensajes de error
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'models/Conexion.php';
    
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = $_POST['role_id'];

    // Validar campos
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el usuario o el correo electrónico ya existen
        $conn = Conexion::getConexion();
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombre_usuario = :nombre_usuario OR email = :email");
        $stmt->bindParam(':nombre_usuario', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error = "El nombre de usuario o el correo electrónico ya están registrados.";
        } else {
            // Cifrar la contraseña
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insertar el nuevo usuario
            $stmt = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, email, password, rol_id) VALUES (:nombre_usuario, :email, :password, :rol_id)");
            $stmt->bindParam(':nombre_usuario', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':rol_id', $role_id);
            $stmt->execute();

            // Redirigir al login o a una página de éxito
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
</head>
<body>
    <h2>Registro de Usuario</h2>

    <!-- Mostrar errores si los hay -->
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" name="username" required><br><br>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required><br><br>

        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" name="confirm_password" required><br><br>

        <label for="role_id">Rol:</label>
        <select name="role_id" required>
            <option value="1">Administrador</option>
            <option value="2">Usuario</option>
        </select><br><br>

        <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>
