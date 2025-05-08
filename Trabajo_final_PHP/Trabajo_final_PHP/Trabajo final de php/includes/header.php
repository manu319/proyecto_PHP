<?php


// Verificar si el usuario está logueado
$is_logged_in = isset($_SESSION['idUser']);  // Verificar si el usuario está logueado
$role = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';  // Obtener el rol (user o admin)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="css/styles.css">  <!-- Ruta al archivo de estilos -->
</head>
<body>

<header>
    <!-- Logo o nombre de la web -->
    
        
    <nav class="nav-bar">
    <div class="logo">
            
        
        
        <ul>
      
            <!-- Enlaces visibles para todos los usuarios -->
            <li><a href="index.php">Inicio</a></li>
            <li><a href="noticias.php">Noticias</a></li>
            <li><a href="contacto.php">Contacto</a></li>

            <?php if ($is_logged_in): ?>
                <!-- Enlaces para usuarios logueados -->
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="citas.php">Citas</a></li>

                <?php if ($role == 'admin'): ?>
                    <!-- Enlaces exclusivos para el admin -->
                    <li><a href="noticias-administracion.php">noticias-admin</a></li>
                    <li><a href="citas-administracion.php">citas-admin</a></li>
                    <li><a href="usuarios-administracion.php">Registros</a></li>
                <?php endif; ?>

                <!-- Enlace para cerrar sesión -->
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <!-- Enlaces para usuarios no logueados -->
                <li><a href="login.php">Iniciar sesión</a></li>
                <li><a href="registro.php">Registrarse</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

</body>
</html>
