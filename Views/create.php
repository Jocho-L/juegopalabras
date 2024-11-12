<?php
require_once '../models/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $palabra = $_POST['palabra'];
    $imagenes = $_FILES['imagenes'];

    // Paso 1: Convertir la palabra a minúsculas
    $palabra = strtolower($palabra);
    
    // Paso 2: Reemplazar caracteres acentuados por sus versiones sin tilde
    $trans = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u'
    ];
    $palabra = strtr($palabra, $trans);

    // Paso 3: Eliminar caracteres especiales y reemplazar espacios por guiones bajos
    // Asegúrate de reemplazar solo los caracteres que no son letras o números.
    $palabra = preg_replace('/[^a-z0-9]+/i', '_', $palabra);

    // Verificar que se subieron exactamente 4 imágenes
    if (count($imagenes['name']) !== 4) {
        die("Debes subir exactamente 4 imágenes.");
    }

    // Conexión a la base de datos
    $conexion = Conexion::getConexion();
    
    try {
        // 1. Insertar la palabra procesada en la tabla `Palabras`
        $stmt = $conexion->prepare("INSERT INTO Palabras (palabra) VALUES (:palabra)");
        $stmt->bindParam(':palabra', $palabra);
        $stmt->execute();
        
        // Obtener el ID de la palabra recién insertada
        $id_palabra = $conexion->lastInsertId();

        // 2. Subir y guardar cada imagen
        $rutaCarpeta = '../img/'; // Ruta donde se guardarán las imágenes
        if (!is_dir($rutaCarpeta)) {
            mkdir($rutaCarpeta, 0777, true);  // Crear la carpeta si no existe
        }
        
        foreach ($imagenes['tmp_name'] as $index => $tmpName) {
            $extension = pathinfo($imagenes['name'][$index], PATHINFO_EXTENSION);
            $nombreArchivo = $palabra . "_" . ($index + 1) . "." . $extension; // Ejemplo: palabra_1.jpg
            $rutaImagen = $rutaCarpeta . $nombreArchivo;

            // Mover la imagen a la carpeta de destino con el nuevo nombre
            if (move_uploaded_file($tmpName, $rutaImagen)) {
                // Insertar la ruta de la imagen en la tabla `Imagenes`
                $stmt = $conexion->prepare("INSERT INTO Imagenes (id_palabra, ruta_imagen) VALUES (:id_palabra, :ruta_imagen)");
                $stmt->bindParam(':id_palabra', $id_palabra);
                $stmt->bindParam(':ruta_imagen', $rutaImagen);
                $stmt->execute();
            } else {
                throw new Exception("Error al subir la imagen: $nombreArchivo");
            }
        }

        echo "Palabra e imágenes subidas con éxito.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
