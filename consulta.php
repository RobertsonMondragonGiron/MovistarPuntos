<?php
session_start(); // Iniciar la sesión para verificar si el usuario está logueado

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['correo'])) {
    header("Location: index.php"); // Redirigir al login si no ha iniciado sesión
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Visitas</title>
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de incluir tu archivo de estilos -->
</head>
<body>
    <div class="container dashboard-container">
        <!-- Cabecera de la página con los botones -->
        <header>
            <h1>Consultar Visitas</h1>
            <div class="button-container">
                
            <a href="dashboard.php"><button>Registrar</button></a>
        <a href="modificar.php"><button>Modificar</button></a>
        <a href="logout.php"><button>Cerrar sesión</button></a>
    </div>
        </header>
        
        <!-- Formulario para seleccionar las fechas -->
        <form method="POST" action="resultados.php">
            <div class="input-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="input-group">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
            <button type="submit">Resultados</button>
        </form>
        
</body>
</html>

