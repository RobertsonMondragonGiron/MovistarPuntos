<?php
// Iniciar sesión
session_start();

// Conectar a la base de datos
include('db.php'); // Asegúrate de que tu archivo db.php esté correctamente configurado

// Verificar si el usuario ya está logueado
if (isset($_SESSION['correo'])) {
    header('Location: dashboard.php'); // Redirigir al dashboard si ya está logueado
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar si las contraseñas coinciden
    if ($password != $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Proteger contra inyecciones SQL
        $correo = mysqli_real_escape_string($conn, $correo);
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        // Verificar si el correo ya está registrado
        $query = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Este correo ya está registrado.";
        } else {
            // Encriptar la contraseña antes de guardarla
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertar los datos del nuevo usuario en la base de datos
            $query = "INSERT INTO usuarios (correo, username, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $correo, $username, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['correo'] = $correo; // Iniciar sesión automáticamente
                header('Location: dashboard.php'); // Redirigir al dashboard
                exit();
            } else {
                $error = "Hubo un problema al registrar la cuenta. Por favor, intenta nuevamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu archivo de estilos -->
</head>
<body>
    <div class="container register-container">
        <h2>Registrarse</h2>
        
        <!-- Mostrar error si existe -->
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        
        <!-- Formulario de registro -->
        <form method="POST" action="">
            <div class="input-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="input-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrar</button>
        </form>
        
        <!-- Enlace a iniciar sesión -->
        <p><center>¿Ya tienes cuenta?</center></p>
        <p><a href="index.php">Inicia sesión aquí.</a></p>
    </div>
</body>
</html>


