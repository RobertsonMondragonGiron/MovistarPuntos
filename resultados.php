<?php
// Conectar a la base de datos
include('db.php');

// Inicializar variables
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$registros = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "SELECT * FROM visitas WHERE correo_id = ? AND fecha BETWEEN ? AND ? ORDER BY fecha DESC";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $_SESSION['correo'], $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $registros[] = $row;
            }
        }
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}

// Generar y descargar el archivo CSV
if (!empty($registros)) {
    // Configurar encabezados para descarga
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="resultados_visitas.csv"');

    // Abrir flujo de salida
    $output = fopen('php://output', 'w');

    // Agregar encabezados al CSV
    fputcsv($output, ['Fecha', 'Visita 1', 'Visita 2', 'Visita 3', 'Visita 4', 'Visita 5', 'Total']);

    // Agregar filas de datos
    foreach ($registros as $registro) {
        fputcsv($output, [
            $registro['fecha'],
            $registro['visita1'],
            $registro['visita2'],
            $registro['visita3'],
            $registro['visita4'],
            $registro['visita5'],
            $registro['total']
        ]);
    }

    // Cerrar flujo
    fclose($output);
    exit();
} else {
    echo "No hay datos para exportar.";
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
    <div class="dashboard-container">
        <h1>Resultados de Visitas</h1>
        
        <?php if (!empty($registros)): ?>
            <h2>Resultados</h2>
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
                            <td><?= $registro['fecha'] ?></td>
                            <td><?= $registro['visita1'] ?></td>
                            <td><?= $registro['visita2'] ?></td>
                            <td><?= $registro['visita3'] ?></td>
                            <td><?= $registro['visita4'] ?></td>
                            <td><?= $registro['visita5'] ?></td>
                            <td><?= $registro['total'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>No se encontraron registros para el rango de fechas seleccionado.</p>
        <?php endif; ?>

        <form method="POST">
    <button type="submit" name="exportar_excel">Exportar a Excel</button>
</form>
        <a href="dashboard.php"><button>Registrar</button></a>
        <a href="consulta.php"><button>Consultar</button></a>
        <a href="Modificar.php"><button>Modificar</button></a>
        <a href="logout.php"><button>Cerrar sesión</button></a>
    </div>
</body>
</html>


