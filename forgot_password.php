<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace al archivo de estilos -->
</head>
<body>
    <div class="forgot-password-container">
        <h1>Recuperar Contraseña</h1>
        <form method="POST" action="process_forgot_password.php"> <!-- Enviar datos a script PHP -->
            <div class="input-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>
            </div>
            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>


