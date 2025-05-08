<?php
session_start();
include 'config/config.php'; // Incluir la configuración de la base de datos
include 'includes/header.php'; // Incluir el encabezado

// Verificar si el usuario está logueado
if (!isset($_SESSION['rol'])) {
    header('Location: login.php');
    exit();
}

$idUser = $_SESSION['idUser']; // Obtener el ID del usuario desde la sesión

// Procesar el formulario de creación de cita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_cita'])) {
    $titulo = $_POST['titulo'];        // Título de la cita
    $descripcion = $_POST['descripcion'];  // Descripción de la cita
    $fecha_inicio = $_POST['fecha_inicio'];          // Fecha de la cita
    $motivo = $_POST['motivo'];        // Motivo de la cita

    // Insertar la cita en la base de datos de forma segura
    $stmt = $conn->prepare("INSERT INTO citas (idUser, titulo, descripcion, fecha_inicio, motivo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $idUser, $titulo, $descripcion, $fecha_inicio, $motivo);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Cita registrada con éxito.</p>";
    } else {
        echo "<p class='error'>Error al registrar la cita: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Modificar cita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modificar_cita'])) {
    $idCita = $_POST['idCita'];
    $titulo = $_POST['titulo'];        // Título de la cita
    $descripcion = $_POST['descripcion'];  // Descripción de la cita
    $fecha_inicio = $_POST['fecha_inicio'];          // Fecha de la cita
    $motivo = $_POST['motivo'];        // Motivo de la cita

    // Actualizar la cita en la base de datos de forma segura
    $stmt = $conn->prepare("UPDATE citas SET titulo = ?, descripcion = ?, fecha_cita = ?, motivo_cita = ? WHERE idCita = ? AND idUser = ?");
    $stmt->bind_param("ssssii", $titulo, $descripcion, $fecha, $motivo, $idCita, $idUser);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Cita modificada con éxito.</p>";
    } else {
        echo "<p class='error'>Error al modificar la cita: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Cancelar cita
if (isset($_GET['cancelar_cita'])) {
    $idCita = $_GET['cancelar_cita'];

    // Eliminar la cita de la base de datos de forma segura
    $stmt = $conn->prepare("DELETE FROM citas WHERE idCita = ? AND idUser = ?");
    $stmt->bind_param("ii", $idCita, $idUser);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Cita cancelada con éxito.</p>";
    } else {
        echo "<p class='error'>Error al cancelar la cita: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Mostrar las citas registradas por el usuario
$stmt_citas = $conn->prepare("SELECT * FROM citas WHERE idUser = ? ORDER BY fecha_inicio DESC");
$stmt_citas->bind_param("i", $idUser);
$stmt_citas->execute();
$result_citas = $stmt_citas->get_result();

?>

<main>
    <h2>Crear Cita</h2>
    <form action="citas.php" method="POST">
        <label for="titulo">Título de la cita:</label>
        <input type="text" id="titulo" name="titulo" required>

        <label for="descripcion">Descripción de la cita:</label>
        <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

        <label for="fecha_fechainicio">Fecha de la cita:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>

        <label for="motivo">Motivo de la cita:</label>
        <textarea id="motivo" name="motivo" rows="4" required></textarea>

        <button type="submit" name="crear_cita">Crear cita</button>
    </form>

    <h3>Tus citas registradas</h3>
    <?php
    if ($result_citas->num_rows > 0) {
        echo "<table><tr><th>Título</th><th>Fecha</th><th>Motivo</th><th>Acciones</th></tr>";
        
        while ($row = $result_citas->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['titulo'] . "</td>
                    <td>" . $row['fecha_inicio'] . "</td>
                    <td>" . $row['motivo'] . "</td>
                    <td>
                        <a href='?modificar_cita=" . $row['idCita'] . "'>Modificar</a> |
                        <a href='?cancelar_cita=" . $row['idCita'] . "' onclick='return confirm(\"¿Estás seguro de cancelar esta cita?\")'>Cancelar</a>
                    </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tienes citas registradas.</p>";
    }

    // Si el usuario quiere modificar una cita
    if (isset($_GET['modificar_cita'])) {
        $idCita = $_GET['modificar_cita'];
        
        // Obtener los detalles de la cita de forma segura
        $stmt_cita = $conn->prepare("SELECT * FROM citas WHERE idCita = ? AND idUser = ?");
        $stmt_cita->bind_param("ii", $idCita, $idUser);
        $stmt_cita->execute();
        $result_cita = $stmt_cita->get_result();
        $cita = $result_cita->fetch_assoc();

        // Mostrar el formulario de modificación
        if ($cita) {
            echo "
            <h3>Modificar Cita</h3>
            <form action='citas.php' method='POST'>
                <input type='hidden' name='idCita' value='" . $cita['idCita'] . "'>
                <label for='titulo'>Nuevo Título:</label>
                <input type='text' id='titulo' name='titulo' value='" . $cita['titulo'] . "' required>

                <label for='descripcion'>Nueva Descripción:</label>
                <textarea id='descripcion' name='descripcion' rows='4' required>" . $cita['descripcion'] . "</textarea>

                <label for='fecha'>Nueva Fecha:</label>
                <input type='date' id='fecha' name='fecha' value='" . $cita['fecha_inicio'] . "' required>

                <label for='motivo'>Nuevo Motivo:</label>
                <textarea id='motivo' name='motivo' rows='4' required>" . $cita['motivo'] . "</textarea>

                <button type='submit' name='modificar_cita'>Modificar cita</button>
            </form>";
        } else {
            echo "<p class='error'>Cita no encontrada.</p>";
        }
        $stmt_cita->close();
    }
    ?>
</main>

<?php
include 'includes/footer.php'; // Incluir el pie de página
?>
