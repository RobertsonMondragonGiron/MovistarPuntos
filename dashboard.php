<?php
// Iniciar sesión para verificar si el usuario está autenticado
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['correo'])) {
    // Si no ha iniciado sesión, redirigir al login
    header("Location: index.php");
    exit();
}

$correo = $_SESSION['correo'];

// Incluir la conexión a la base de datos
include('db.php');

// Procesar el formulario al enviarlo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y filtrar los datos del formulario
    $fecha = $_POST['fecha'];
    $visita_1 = intval($_POST['visita_1']);
    $visita_2 = intval($_POST['visita_2']);
    $visita_3 = intval($_POST['visita_3']);
    $visita_4 = intval($_POST['visita_4']);
    $visita_5 = intval($_POST['visita_5']);
    $total = $visita_1 + $visita_2 + $visita_3 + $visita_4 + $visita_5;

    // Verificar si ya existe un registro con la misma fecha para el usuario
    $check_query = "SELECT * FROM visitas WHERE fecha = ? AND correo_id = ?";
    $stmt_check = $conn->prepare($check_query);

    if ($stmt_check) {
        $stmt_check->bind_param("ss", $fecha, $correo);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Si ya existe un registro, mostrar un mensaje de error
            $error = "Ya existe un registro para la fecha seleccionada.";
        } else {
            // Insertar los datos si no existe un registro con la misma fecha
            $query = "INSERT INTO visitas (fecha, visita1, visita2, visita3, visita4, visita5, total, correo_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($query);

            if ($stmt_insert) {
                // Bind de parámetros - usar 'siiiiiss' para corresponder con 8 parámetros
                $stmt_insert->bind_param("siiiiiss", $fecha, $visita_1, $visita_2, $visita_3, $visita_4, $visita_5, $total, $correo);

                // Ejecutar la consulta
                if ($stmt_insert->execute()) {
                    $mensaje = "Registro guardado exitosamente.";
                } else {
                    $error = "Error al guardar los datos: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                $error = "Error al preparar la consulta: " . $conn->error;
            }
        }
        $stmt_check->close();
    } else {
        $error = "Error al preparar la consulta de verificación: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Visitas</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Función para calcular el total
        function calcularTotal() {
            var visita_1 = parseInt(document.getElementById('visita_1').value) || 0;
            var visita_2 = parseInt(document.getElementById('visita_2').value) || 0;
            var visita_3 = parseInt(document.getElementById('visita_3').value) || 0;
            var visita_4 = parseInt(document.getElementById('visita_4').value) || 0;
            var visita_5 = parseInt(document.getElementById('visita_5').value) || 0;

            // Sumar las visitas
            var total = visita_1 + visita_2 + visita_3 + visita_4 + visita_5;

            // Mostrar el total en el campo correspondiente
            document.getElementById('total').value = total;
        }

        // Validar la fecha antes de enviar el formulario
        function validarFecha() {
            var fecha = document.getElementById('fecha').value;
            var fechaRegex = /^\d{4}-\d{2}-\d{2}$/; // Validar formato YYYY-MM-DD

            if (!fechaRegex.test(fecha)) {
                alert("La fecha debe estar en el formato: YYYY-MM-DD.");
                return false; // Evitar el envío del formulario si la fecha no es válida
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container dashboard-container">
        <!-- Cabecera de la página con los botones -->
        <header>
            <h1>Registrar Visitas</h1>
            <div class="button-container">
                
                <a href="consulta.php"><button>Consulta</button></a>
                <a href="Modificar.php"><button>Modificar</button></a>
                <a href="logout.php"><button>Cerrar sesión</button></a>
            </div>
        </header>

        <!-- Mensajes de éxito o error -->
        <?php 
        if (isset($mensaje)) { 
            echo "<p class='success'>$mensaje</p>"; 
        } 
        if (isset($error)) { 
            echo "<p class='error'>$error</p>"; 
        } 
        ?>

        <!-- Formulario de registro de visitas -->
        <form method="POST" action="" onsubmit="return validarFecha()">
            <div class="input-group">
                <label for="fecha">Fecha (YYYY-MM-DD):</label>
                <input type="text" id="fecha" name="fecha" required placeholder="2024-11-24">
            </div>
            <div class="input-group">
                <label for="visita_1">Visita 1:</label>
                <input type="number" id="visita_1" name="visita_1" required oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_2">Visita 2:</label>
                <input type="number" id="visita_2" name="visita_2" required oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_3">Visita 3:</label>
                <input type="number" id="visita_3" name="visita_3" required oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_4">Visita 4:</label>
                <input type="number" id="visita_4" name="visita_4" required oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="visita_5">Visita 5:</label>
                <input type="number" id="visita_5" name="visita_5" required oninput="calcularTotal()">
            </div>
            <div class="input-group">
                <label for="total">Total:</label>
                <input type="number" id="total" name="total" readonly>
            </div>
            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>

