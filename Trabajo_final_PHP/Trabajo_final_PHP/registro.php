<?php
include 'config/config.php'; // Incluir la configuración de la base de datos

// Comprobar si existe una sesión activa
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrarse'])){
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellidos = htmlspecialchars($_POST['apellidos']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars($_POST['telefono']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = htmlspecialchars($_POST['direccion']);
    $sexo = $_POST['sexo'];
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Insertar en users_data
    $sql_data = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
                 VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$sexo')";
    if ($conn->query($sql_data) === TRUE) {
        $idUser = $conn->insert_id;
        
        // Insertar en users_login
        $sql_login = "INSERT INTO users_login (idUser, usuario, password, rol)
                      VALUES ('$idUser', '$usuario', '$password', 'user')";
        if ($conn->query($sql_login) === TRUE) {
            echo "Registro exitoso. Redirigiendo al login...";
            header('Refresh: 2; URL=login.php');
            exit();
        } else {
            $error = "Error al registrar el usuario: " . $conn->error;
        }
    } else {
        $error = "Error al registrar los datos: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
    
    <main>
        <h2>Registro</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="registro.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" name="registrarse" value="Enviar">
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
