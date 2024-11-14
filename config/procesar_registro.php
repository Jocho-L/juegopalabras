<?php
// Incluir la clase Conexion
require_once '../Models/Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    try {
        // Obtener la conexión a la base de datos
        $conn = Conexion::getConexion();

        // Validar si el nombre de usuario o el correo electrónico ya existen
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombre_usuario = :nombre_usuario OR email = :email");
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Si ya existe el nombre de usuario o el correo
            header("Location: index.php?error=El nombre de usuario o el correo ya están registrados.");
            exit;
        }

        // Encriptar la contraseña
        $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

        // Insertar el nuevo usuario
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, email, password, idRol) VALUES (:nombre_usuario, :email, :password, :idRol)");
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_encriptada);
        $stmt->bindParam(':idRol', $rol);

        if ($stmt->execute()) {
            // Redirigir al formulario con un mensaje de éxito
            header("Location: ../index.html?success=true");
        } else {
            // Redirigir al formulario con un mensaje de error
            header("Location: procesar_registro.php?error=Error al registrar el usuario.");
        }
    } catch (PDOException $e) {
        // Si ocurre un error en la conexión o la ejecución
        die("Error: " . $e->getMessage());
    }
}
?>
