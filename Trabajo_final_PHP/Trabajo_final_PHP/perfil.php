<?php
session_start();
include 'config/config.php'; // Incluir la configuración de la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUser'])) {
    header('Location: login.php');
    exit();
}

// Obtener la información del usuario
$idUser = $_SESSION['idUser'];
$sql = "SELECT * FROM users_data WHERE idUser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar la actualización de los datos del usuario
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellidos = htmlspecialchars($_POST['apellidos']);
    $email = htmlspecialchars($_POST['email']);
    $telefono = htmlspecialchars($_POST['telefono']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = htmlspecialchars($_POST['direccion']);
    $sexo = $_POST['sexo'];

    // Actualizar la información del usuario en la base de datos
    $sql_update = "UPDATE users_data 
                   SET nombre = ?, apellidos = ?, email = ?, telefono = ?, 
                       fecha_nacimiento = ?, direccion = ?, sexo = ? 
                   WHERE idUser = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssssi", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo, $idUser);

    if ($stmt_update->execute()) {
        $message = "Perfil actualizado con éxito.";
        // Actualizar la información del usuario para mostrar los cambios inmediatamente
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
    } else {
        $error = "Error al actualizar el perfil: " . $conn->error;
    }

    // Procesar la actualización de la contraseña
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql_update_password = "UPDATE users_login SET password = ? WHERE idUser = ?";
        $stmt_update_password = $conn->prepare($sql_update_password);
        $stmt_update_password->bind_param("si", $password, $idUser);

        if ($stmt_update_password->execute()) {
            $message = "Contraseña actualizada con éxito.";
        } else {
            $error = "Error al actualizar la contraseña: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

    <main>
        <h2>Perfil</h2>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="perfil.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user_data['nombre']); ?>" required>
            
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($user_data['apellidos']); ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user_data['telefono']); ?>" required>
            
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($user_data['fecha_nacimiento']); ?>" required>
            
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($user_data['direccion']); ?>">
            
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="M" <?php if ($user_data['sexo'] == 'M') echo 'selected'; ?>>Masculino</option>
                <option value="F" <?php if ($user_data['sexo'] == 'F') echo 'selected'; ?>>Femenino</option>
                <option value="Otro" <?php if ($user_data['sexo'] == 'Otro') echo 'selected'; ?>>Otro</option>
            </select>
            
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" readonly>
            
            <label for="password">Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" id="password" name="password">
            
            <button type="submit">Actualizar Perfil</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
