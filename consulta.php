<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['correo'])) {
    echo "Inicia sesión para acceder a los resultados.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Resultados de Visitas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container dashboard-container">
        <h1>Consultar Resultados de Visitas</h1>
        <div class="button-container">
            <a href="dashboard.php"><button>Registrar</button></a>
            <a href="Modificar.php"><button>Modificar</button></a>
            <a href="logout.php"><button>Cerrar sesión</button></a>
        </div>
        <br>
        <div class="form-container">
    <div class="form-group">
        <label for="fecha_inicio">Fecha de inicio:</label>
        
            <input type="date" name="fecha_inicio" required>
        
    </div>
    <div class="form-group">
        <label for="fecha_fin">Fecha de fin:</label>
        
            <input type="date" name="fecha_fin" required>
        
    </div>
    <br>
    <div class="form-group">
        <button type="submit">Consultar</button>
    </div>
</div>

</body>
</html>


