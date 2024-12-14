<?php
session_start();
include('db.php');

if (isset($_SESSION['correo'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['correo'] = $usuario['correo'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container login-container">
        <h2>Iniciar Sesión</h2>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" action="">
            <div class="input-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($correo ?? '') ?>" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p><a href="register.php">¿No tienes cuenta? Regístrate aquí.</a></p>
    </div>
</body>
</html>






