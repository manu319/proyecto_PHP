<?php
session_start();
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consulta para obtener la contraseña hash del usuario
    $sql = "SELECT * FROM users_login WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifica si la contraseña es correcta
        if (password_verify($password, $user['password'])) {
            $_SESSION['idUser'] = $user['idUser'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            // Redirige al usuario según el rol
            if ($user['rol'] == 'admin') {
                // Redirigir al administrador (cambia 'admin.php' a lo que quieras)
                header('Location: index.php'); // Cambia esto a tu archivo deseado para admins
            } else {
                // Redirigir al usuario normal
                header('Location: index.php'); // Cambia esto a tu archivo de inicio
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h2>Iniciar sesión</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Iniciar sesión</button>
        </form>

        <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
