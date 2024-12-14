<?php
// Iniciar sesión para verificar si el usuario está autenticado
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['correo'])) {
    header("Location: index.php");
    exit();
}

$correo = $_SESSION['correo'];

// Incluir la conexión a la base de datos
include('db.php');

// Inicializar variables
$mensaje = '';
$error = '';
$registro = null;

// Procesar la búsqueda de la fecha
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar_fecha'])) {
    $fecha_buscar = $_POST['fecha_buscar'];
    $query = "SELECT * FROM visitas WHERE fecha = ? AND correo_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $fecha_buscar, $correo);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $registro = $result->fetch_assoc();
        } else {
            $error = "No se encontró un registro para la fecha especificada.";
        }
    } else {
        $error = "Error al consultar los datos: " . $stmt->error;
    }
    $stmt->close();
}

// Procesar la actualización del registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    $id = intval($_POST['id']);
    $fecha = $_POST['fecha'];
    $visita_1 = intval($_POST['visita_1']);
    $visita_2 = intval($_POST['visita_2']);
    $visita_3 = intval($_POST['visita_3']);
    $visita_4 = intval($_POST['visita_4']);
    $visita_5 = intval($_POST['visita_5']);
    $total = $visita_1 + $visita_2 + $visita_3 + $visita_4 + $visita_5;

    $query = "UPDATE visitas SET fecha = ?, visita1 = ?, visita2 = ?, visita3 = ?, visita4 = ?, visita5 = ?, total = ? 
              WHERE id = ? AND correo_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("siiiiisis", $fecha, $visita_1, $visita_2, $visita_3, $visita_4, $visita_5, $total, $id, $correo);

        if ($stmt->execute()) {
            $mensaje = "Registro actualizado exitosamente.";
        } else {
            $error = "Error al actualizar los datos: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error al preparar la consulta: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Registro</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function calcularTotal() {
            var visita_1 = parseInt(document.getElementById('visita_1').value) || 0;
            var visita_2 = parseInt(document.getElementById('visita_2').value) || 0;
            var visita_3 = parseInt(document.getElementById('visita_3').value) || 0;
            var visita_4 = parseInt(document.getElementById('visita_4').value) || 0;
            var visita_5 = parseInt(document.getElementById('visita_5').value) || 0;

            var total = visita_1 + visita_2 + visita_3 + visita_4 + visita_5;
            document.getElementById('total').value = total;
        }

        function validarFecha() {
            var fecha = document.getElementById('fecha').value;
            var fechaRegex = /^\d{4}-\d{2}-\d{2}$/;

            if (!fechaRegex.test(fecha)) {
                alert("La fecha debe estar en el formato: YYYY-MM-DD.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container dashboard-container">
        <h1>Modificar Registro</h1>
        <div class="button-container">
        <a href="dashboard.php"><button>Registrar</button></a>
        <a href="consulta.php"><button>Consultar</button></a>
        <a href="logout.php"><button>Cerrar sesión</button></a>
    </div>
        <?php 
        if ($mensaje) { 
            echo "<p class='success'>$mensaje</p>"; 
        } 
        if ($error) { 
            echo "<p class='error'>$error</p>"; 
        } 
        ?>

        <!-- Formulario para buscar fecha -->
        <form method="POST" action="">
            <div class="input-group">
                <label for="fecha_buscar">Buscar por Fecha (YYYY-MM-DD):</label>
                <input type="text" id="fecha_buscar" name="fecha_buscar" required>
            </div>
            <button type="submit" name="buscar_fecha">Buscar</button>
        </form>

        <?php if ($registro) { ?>
        <!-- Formulario para modificar registro -->
        <form method="POST" action="" onsubmit="return validarFecha()">
            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
            <div class="input-group">
                <label for="fecha">Fecha (YYYY-MM-DD):</label>
                <input type="text" id="fecha" name="fecha" required value="<?php echo $registro['fecha']; ?>">
            </div>
            <div class="input-group">
                <label for="visita_1">Visita 1:</label>
                <input type="number" id="visita_1" name="visita_1" required value="<?php echo $registro['visita1']; ?>" oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_2">Visita 2:</label>
                <input type="number" id="visita_2" name="visita_2" required value="<?php echo $registro['visita2']; ?>" oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_3">Visita 3:</label>
                <input type="number" id="visita_3" name="visita_3" required value="<?php echo $registro['visita3']; ?>" oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_4">Visita 4:</label>
                <input type="number" id="visita_4" name="visita_4" required value="<?php echo $registro['visita4']; ?>" oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_5">Visita 5:</label>
                <input type="number" id="visita_5" name="visita_5" required value="<?php echo $registro['visita5']; ?>" oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="total">Total:</label>
                <input type="number" id="total" name="total" readonly value="<?php echo $registro['total']; ?>">
            </div>
            <button type="submit" name="actualizar">Actualizar</button>
        </form>
        <?php } ?>
        
</body>
</html>

