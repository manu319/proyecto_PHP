<?php
session_start();

// Verificar que el usuario esté logueado y sea un administrador
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] != 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main>
        <h1>Bienvenido, Administrador</h1>
        <p>Este es el panel de administración. Aquí podrás gestionar todas las funciones exclusivas para administradores.</p>
        
        <!-- Aquí puedes agregar más contenido o funcionalidades para el administrador -->
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
