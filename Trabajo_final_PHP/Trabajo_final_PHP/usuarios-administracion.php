<?php
session_start();
include 'config/config.php'; // Incluir la configuración de la base de datos

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener todos los usuarios
$sql = "SELECT users_data.*, users_login.usuario, users_login.rol 
        FROM users_data 
        INNER JOIN users_login ON users_data.idUser = users_login.idUser";
$result = $conn->query($sql);

// Procesar la modificación del rol de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modificar_rol'])) {
    $idUser = $_POST['idUser'];
    $nuevoRol = $_POST['rol'];

    // Actualizar el rol del usuario
    $stmt = $conn->prepare("UPDATE users_login SET rol = ? WHERE idUser = ?");
    $stmt->bind_param("si", $nuevoRol, $idUser);

    if ($stmt->execute()) {
        $message = "Rol del usuario actualizado con éxito.";
    } else {
        $error = "Error al actualizar el rol del usuario: " . $stmt->error;
    }
}

// Procesar la eliminación de usuarios (si fuera necesario)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $idUser = $_POST['idUser'];
    
    // Eliminar el usuario de la base de datos
    $stmt = $conn->prepare("DELETE FROM users_login WHERE idUser = ?");
    $stmt->bind_param("i", $idUser);

    if ($stmt->execute()) {
        $message = "Usuario eliminado con éxito.";
    } else {
        $error = "Error al eliminar el usuario: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>    <!-- Incluir la barra de navegación -->

    <main>
        <h2>Administración de Usuarios</h2>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <h3>Usuarios Registrados</h3>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['nombre'] . ' ' . $row['apellidos']; ?></td>
                    <td><?php echo $row['usuario']; ?></td>
                    <td>
                        <form method="POST" action="usuarios-administracion.php" style="display:inline;">
                            <input type="hidden" name="idUser" value="<?php echo $row['idUser']; ?>">
                            <select name="rol">
                                <option value="user" <?php echo ($row['rol'] == 'user') ? 'selected' : ''; ?>>Usuario</option>
                                <option value="admin" <?php echo ($row['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                            <button type="submit" name="modificar_rol">Cambiar Rol</button>
                        </form>
                    </td>
                    <td>
                        <!-- Formulario para eliminar un usuario -->
                        <form method="POST" action="usuarios-administracion.php" style="display:inline;">
                            <input type="hidden" name="idUser" value="<?php echo $row['idUser']; ?>">
                            <button type="submit" name="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>

    <?php include 'includes/footer.php'; ?> <!-- Incluir el footer -->
</body>
</html>

<?php
$conn->close(); // Cerrar la conexión
?>
