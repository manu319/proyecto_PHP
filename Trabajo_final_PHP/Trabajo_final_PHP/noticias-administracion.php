<?php
session_start();
include 'config/config.php'; // Incluir la configuración de la base de datos

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Procesar la creación de una nueva noticia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $titulo = htmlspecialchars($_POST['titulo']); // Sanitizar título
    $texto = htmlspecialchars($_POST['texto']); // Sanitizar texto
    $fecha = date('Y-m-d');
    $idUser = $_SESSION['idUser'];

    // Verificar si se subió una imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['name'];
        $target_dir = "img/";
        $target_file = $target_dir . basename($imagen);

        // Validar tipo de archivo (por ejemplo, solo imágenes JPG, PNG)
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['imagen']['type'];

        if (in_array($file_type, $allowed_types)) {
            // Mover la imagen al directorio
            move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file);

            // Insertar en la base de datos
            $sql = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser) 
                    VALUES ('$titulo', '$imagen', '$texto', '$fecha', $idUser)";
            if ($conn->query($sql) === TRUE) {
                // Crear noticia en el XML
                $xml_file = 'data/noticias.xml';
                $xml = simplexml_load_file($xml_file);
                
                // Crear un nuevo nodo de noticia
                $noticia = $xml->addChild('noticia');
                $noticia->addChild('titulo', $titulo);
                $noticia->addChild('imagen', $imagen);
                $noticia->addChild('texto', $texto);
                $noticia->addChild('fecha', $fecha);

                // Guardar el XML actualizado
                $xml->asXML($xml_file);

                $message = "Noticia creada con éxito.";
            } else {
                $error = "Error al crear la noticia: " . $conn->error;
            }
        } else {
            $error = "El archivo no es una imagen válida.";
        }
    } else {
        $error = "Por favor, sube una imagen válida.";
    }
}

// Obtener todas las noticias
$sql = "SELECT noticias.*, users_data.nombre, users_data.apellidos 
        FROM noticias 
        INNER JOIN users_data ON noticias.idUser = users_data.idUser";
$result = $conn->query($sql);

// Procesar la eliminación de noticias
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $idNoticia = $_POST['idNoticia'];
    $sql_delete = "DELETE FROM noticias WHERE idNoticia = $idNoticia";
    if ($conn->query($sql_delete) === TRUE) {
        // Eliminar noticia en el XML
        $xml_file = 'data/noticias.xml';
        $xml = simplexml_load_file($xml_file);
        
        foreach ($xml->noticia as $index => $noticia) {
            if ((string) $noticia->titulo === (string) $_POST['titulo']) {
                unset($xml->noticia[$index]);
                break;
            }
        }
        $xml->asXML($xml_file); // Guardar el XML después de eliminar

        $message = "Noticia eliminada con éxito.";
        $result = $conn->query($sql); // Actualizar la lista de noticias
    } else {
        $error = "Error al eliminar la noticia: " . $conn->error;
    }
}

// Procesar la modificación de noticias
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modificar'])) {
    $idNoticia = $_POST['idNoticia'];
    $titulo = htmlspecialchars($_POST['titulo']); // Sanitizar título
    $texto = htmlspecialchars($_POST['texto']); // Sanitizar texto

    $sql_update = "UPDATE noticias 
                   SET titulo = '$titulo', texto = '$texto' 
                   WHERE idNoticia = $idNoticia";
    if ($conn->query($sql_update) === TRUE) {
        // Actualizar noticia en el XML
        $xml_file = 'data/noticias.xml';
        $xml = simplexml_load_file($xml_file);

        foreach ($xml->noticia as $noticia) {
            if ((string) $noticia->titulo === (string) $_POST['titulo']) {
                $noticia->titulo = $titulo;
                $noticia->texto = $texto;
                break;
            }
        }
        $xml->asXML($xml_file); // Guardar el XML después de modificar

        $message = "Noticia modificada con éxito.";
        $result = $conn->query($sql); // Actualizar la lista de noticias
    } else {
        $error = "Error al modificar la noticia: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Noticias</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h2>Administración de Noticias</h2>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <h3>Crear Nueva Noticia</h3>
        <!-- Formulario para crear nueva noticia -->
        <form method="POST" action="noticias-administracion.php" enctype="multipart/form-data" class="form-container">
    <h3>Crear Nueva Noticia</h3>
    <label for="titulo">Título:</label>
    <input type="text" id="titulo" name="titulo" required class="input-field">

    <label for="imagen">Imagen:</label>
    <input type="file" id="imagen" name="imagen" accept="image/*" required class="input-field">

    <label for="texto">Texto:</label>
    <textarea id="texto" name="texto" required class="input-field"></textarea>

    <button type="submit" name="crear" class="submit-btn">Crear Noticia</button>
</form>

<h3>Noticias Publicadas</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Título</th>
            <th>Fecha</th>
            <th>Autor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['titulo']; ?></td>
                <td><?php echo $row['fecha']; ?></td>
                <td><?php echo $row['nombre'] . ' ' . $row['apellidos']; ?></td>
                <td>
                    <form method="POST" action="noticias-administracion.php" class="action-form">
                        <input type="hidden" name="idNoticia" value="<?php echo $row['idNoticia']; ?>">
                        <button type="submit" name="eliminar" class="action-btn">Eliminar</button>
                    </form>
                    <form method="POST" action="noticias-administracion.php" class="action-form">
                        <input type="hidden" name="idNoticia" value="<?php echo $row['idNoticia']; ?>">
                        <input type="text" name="titulo" value="<?php echo $row['titulo']; ?>" required class="input-field">
                        <textarea name="texto" required class="input-field"><?php echo $row['texto']; ?></textarea>
                        <button type="submit" name="modificar" class="action-btn">Modificar</button>
                    </form>
                </td>
            </tr>
            
        <?php endwhile; ?>
    </tbody>
</table>
