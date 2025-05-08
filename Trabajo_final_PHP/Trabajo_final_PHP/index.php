<?php
session_start(); // Inicia la sesión

include 'config/config.php'; // Incluir la configuración de la base de datos

// Redirigir a subdominios dependiendo del estado de sesión del usuario
if (isset($_SESSION['idUser'])) {
    // Si el usuario está logueado
    if ($_SESSION['rol'] === 'admin') {
        // Acciones para el administrador, si es necesario
    } else {
        // Otros roles si es necesario
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Página Principal</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <section>
        <h2>Bienvenido a mi web</h2>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci veniam facere quisquam temporibus numquam excepturi quasi sed magni quidem hic aspernatur, laudantium natus repudiandae corporis earum itaque possimus! Delectus, eius?</p>
    </section>

    <section class="contenido">
        <h2>Nuestros inicios</h2>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora eaque accusamus voluptatibus minus consectetur accusantium ex, ea repellat quis corrupti quod soluta repellendus delectus iure sint natus quibusdam fugiat impedit.
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto mollitia ipsa, aliquam impedit inventore magni! Cumque sint aspernatur quae quisquam, enim illo. Neque molestias asperiores temporibus minima repudiandae porro voluptatibus!
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet laboriosam sequi exercitationem quos? Suscipit, dolor unde. Error itaque autem quae quisquam, recusandae debitis asperiores consequuntur officia ipsum ipsam totam eaque?
        </p>
    </section>

    <div class="redes_sociales">
        <div class="red_social">
           <a href="www.facebook.com"><img src="./img/facebook.png" alt="icono facebook" height="100" width="100"></a>
        </div>
        <div class="red_social">
            <a href="www.youtube.com"><img src="./img/youtube.png" alt="icono youtube" height="100" width="100">
        </div></a>
        <div class="red_social">
            <a href="www.instagram.com"><img src="./img/instagram.jpg" alt="icono instagram" height="100" width="100"></a>
        </div>
    </div>

   
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
