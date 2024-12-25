<?php
session_start();
include('db.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['correo'])) {
    echo "Inicia sesión para acceder a los resultados.";
    exit();
}

// Procesar formulario y consultar registros
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$registros = [];
$suma_total = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($fecha_inicio) && !empty($fecha_fin)) {
    $query = "SELECT * FROM visitas WHERE correo_id = ? AND fecha BETWEEN ? AND ? ORDER BY fecha DESC";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $_SESSION['correo'], $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $registros[] = $row;
            $suma_total += $row['total']; // Sumar el valor del campo "total" de cada registro
        }
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Visitas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container dashboard-container">
        <h1>Resultados de Visitas</h1>
        <div class="button-container">
            <a href="dashboard.php"><button>Registrar</button></a>
            <a href="consulta.php"><button>Consultar</button></a>
            <a href="Modificar.php"><button>Modificar</button></a>
            <a href="logout.php"><button>Cerrar sesión</button></a>
        </div>

        <?php if (!empty($registros)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Visita 1</th>
                        <th>Visita 2</th>
                        <th>Visita 3</th>
                        <th>Visita 4</th>
                        <th>Visita 5</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td><?= htmlspecialchars($registro['fecha']) ?></td>
                            <td><?= htmlspecialchars($registro['visita1']) ?></td>
                            <td><?= htmlspecialchars($registro['visita2']) ?></td>
                            <td><?= htmlspecialchars($registro['visita3']) ?></td>
                            <td><?= htmlspecialchars($registro['visita4']) ?></td>
                            <td><?= htmlspecialchars($registro['visita5']) ?></td>
                            <td><?= htmlspecialchars($registro['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: right; font-weight: bold;">Total General:</td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($suma_total) ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>No se encontraron registros para el rango de fechas seleccionado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
