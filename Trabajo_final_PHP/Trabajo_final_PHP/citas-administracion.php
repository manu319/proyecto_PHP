<?php
session_start();
include 'config/config.php'; // Incluir la configuración de la base de datos

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener todos los usuarios para el formulario de creación de citas
$sql_users = "SELECT idUser, nombre, apellidos FROM users_data";
$result_users = $conn->query($sql_users);

// Procesar la creación de una nueva cita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $idUser = $_POST['idUser'];
    $titulo = $_POST['titulo'];           // Título de la cita
    $descripcion = $_POST['descripcion']; // Descripción de la cita
    $fecha_inicio = $_POST['fecha_inicio'];
    $motivo = $_POST['motivo'];

    // Insertar en citas
    $stmt = $conn->prepare("INSERT INTO citas (idUser, titulo, descripcion, fecha_cita, motivo_cita) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $idUser, $titulo, $descripcion, $fecha_cita, $motivo_cita);
    
    if ($stmt->execute()) {
        $message = "Cita creada con éxito.";
    } else {
        $error = "Error al crear la cita: " . $stmt->error;
    }
}

// Obtener todas las citas
$sql = "SELECT citas.*, users_data.nombre, users_data.apellidos 
        FROM citas 
        INNER JOIN users_data ON citas.idUser = users_data.idUser";
$result = $conn->query($sql);

// Procesar la eliminación de citas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $idCita = $_POST['idCita'];

    // Confirmar antes de eliminar
    $stmt = $conn->prepare("DELETE FROM citas WHERE idCita = ?");
    $stmt->bind_param("i", $idCita);

    if ($stmt->execute()) {
        $message = "Cita eliminada con éxito.";
        $result = $conn->query($sql); // Actualizar la lista de citas
    } else {
        $error = "Error al eliminar la cita: " . $stmt->error;
    }
}

// Procesar la modificación de citas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modificar'])) {
    $idCita = $_POST['idCita'];
    $titulo = $_POST['titulo'];           // Título de la cita
    $descripcion = $_POST['descripcion']; // Descripción de la cita
    $fecha_inicio = $_POST['fecha_inicio'];
    $motivo = $_POST['motivo'];

    $stmt = $conn->prepare("UPDATE citas SET titulo = ?, descripcion = ?, fecha_cita = ?, motivo_cita = ? WHERE idCita = ?");
    $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_cita, $motivo_cita, $idCita);

    if ($stmt->execute()) {
        $message = "Cita modificada con éxito.";
        $result = $conn->query($sql); // Actualizar la lista de citas
    } else {
        $error = "Error al modificar la cita: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Citas</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        // Confirmación para eliminar la cita
        function confirmDelete() {
            return confirm("¿Estás seguro de que deseas eliminar esta cita?");
        }
    </script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h2>Administración de Citas</h2>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <h3>Crear Nueva Cita</h3>
        <form method="POST" action="citas-administracion.php" class="form-container">
    <h3>Crear Nueva Cita</h3>
    <label for="idUser">Usuario:</label>
    <select id="idUser" name="idUser" required class="input-field">
        <?php while ($row_users = $result_users->fetch_assoc()): ?>
            <option value="<?php echo $row_users['idUser']; ?>"><?php echo $row_users['nombre'] . ' ' . $row_users['apellidos']; ?></option>
        <?php endwhile; ?>
    </select>

    <label for="titulo">Título de la Cita:</label>
    <input type="text" id="titulo" name="titulo" required class="input-field">

    <label for="descripcion">Descripción de la Cita:</label>
    <textarea id="descripcion" name="descripcion" rows="4" required class="input-field"></textarea>

    <label for="fecha_inicio">Fecha de la Cita:</label>
    <input type="date" id="fecha_inicio" name="fecha_inicio" required class="input-field">

    <label for="motivo_cita">Motivo de la Cita:</label>
    <textarea id="motivo" name="motivo" required class="input-field"></textarea>

    <button type="submit" name="crear" class="submit-btn">Crear Cita</button>
</form>

<h3>Citas Programadas</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Título</th>
            <th>Fecha</th>
            <th>Motivo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['nombre'] . ' ' . $row['apellidos']; ?></td>
                <td><?php echo $row['titulo']; ?></td>
                <td><?php echo $row['fecha_inicio']; ?></td>
                <td><?php echo $row['motivo']; ?></td>
                <td>
                    <form method="POST" action="citas-administracion.php" class="action-form" onsubmit="return confirmDelete();">
                        <input type="hidden" name="idCita" value="<?php echo $row['idCita']; ?>">
                        <button type="submit" name="eliminar" class="action-btn">Eliminar</button>
                    </form>
                    <form method="POST" action="citas-administracion.php" class="action-form">
                        <input type="hidden" name="idCita" value="<?php echo $row['idCita']; ?>">
                        <input type="text" name="titulo" value="<?php echo $row['titulo']; ?>" required class="input-field">
                        <textarea name="descripcion" rows="4" required class="input-field"><?php echo $row['descripcion']; ?></textarea>
                        <input type="date" name="fecha_inicio" value="<?php echo $row['fecha_inicio']; ?>" required class="input-field">
                        <textarea name="motivo" required class="input-field"><?php echo $row['motivo']; ?></textarea>
                        <button type="submit" name="modificar" class="action-btn">Modificar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
