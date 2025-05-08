<?php
session_start();
include 'config/config.php';
$xml = simplexml_load_file('data/noticias.xml') or die("Error: No se pudo cargar el archivo XML.");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Ruta al archivo de estilos -->
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="contenedor-noticias">
    <main>
        <?php
        // Iterar a través de las noticias en el archivo XML
        foreach ($xml->noticia as $noticia) {
            $imagen = $noticia->imagen;
            $titulo = $noticia->titulo;
            $texto = $noticia->texto;
            $fecha = $noticia->fecha;

            echo "<div class='noticia-caja'>";

            // Mostrar la imagen de la noticia
            if (!empty($imagen)) {
                $ruta_imagen = "img/" . $imagen;
                echo "<div class='noticia-imagen'><img src='$ruta_imagen' alt='$titulo'></div>";
            }

            // Mostrar el título de la noticia centrado
            echo "<h2 class='noticia-titulo'>$titulo</h2>";

            // Mostrar el texto de la noticia centrado
            echo "<p class='noticia-texto'>$texto</p>";

            // Mostrar la fecha de la noticia
            echo "<p class='noticia-fecha'><strong>Fecha:</strong> $fecha</p>";

            echo "</div>";
        }
        ?>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
