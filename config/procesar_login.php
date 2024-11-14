<?php
// Incluir la clase Conexion
require_once '../Models/Conexion.php';
session_start(); // Iniciar la sesión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $password = $_POST['password'];

    try {
        // Obtener la conexión a la base de datos
        $conn = Conexion::getConexion();

        // Verificar si el nombre de usuario existe
        $stmt = $conn->prepare("SELECT idUsuario, nombre_usuario, password, idRol FROM Usuarios WHERE nombre_usuario = :nombre_usuario");
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Obtener el usuario de la base de datos
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Iniciar sesión
                $_SESSION['idUsuario'] = $user['idUsuario'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['idRol'] = $user['idRol'];

                // Redirigir a la página protegida
                header("Location: ../views/seleccion.php");
                exit;
            } else {
                // Contraseña incorrecta
                header("Location: ../login.php?error=Contraseña incorrecta.");
                exit;
            }
        } else {
            // Usuario no encontrado
            header("Location: ../login.php?error=Usuario no encontrado.");
            exit;
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
