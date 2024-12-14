<?php
include('db.php'); // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($conn, $_POST['email']); // Asegúrate de usar el nombre correcto de la variable

    // Verificar si el correo existe en la base de datos
    $stmt = $conn->prepare("SELECT id, username FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Generar un token único
        $token = bin2hex(random_bytes(50));
        $user_id = $user['id'];

        // Insertar o actualizar el token en la base de datos
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE token = ?, created_at = NOW()");
        $stmt->bind_param("iss", $user_id, $token, $token);
        $stmt->execute();

        // Crear el enlace para restablecer la contraseña
        $reset_link = "https://tu-dominio.com/reset_password.php?token=" . $token;

        // Configuración del correo electrónico
        $to = $correo;
        $subject = "Recuperación de Contraseña";
        $message = "
        <html>
        <head>
            <title>Recuperar Contraseña</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <div style='max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                <h2 style='text-align: center; color: #333;'>Recuperar Contraseña</h2>
                <p>Hola, <strong>{$user['username']}</strong>,</p>
                <p>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el enlace de abajo para continuar:</p>
                <p style='text-align: center;'>
                    <a href='$reset_link' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a>
                </p>
                <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                <p style='font-size: 0.9em; color: #666;'>Este enlace es válido por 1 hora.</p>
            </div>
        </body>
        </html>
        ";

        // Encabezados del correo
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@tu-dominio.com" . "\r\n";

        // Enviar el correo
        if (mail($to, $subject, $message, $headers)) {
            echo "Un enlace para restablecer tu contraseña ha sido enviado a tu correo.";
        } else {
            echo "Error al enviar el correo. Inténtalo más tarde.";
        }
    } else {
        // Si el correo no existe
        echo "El correo ingresado no está registrado.";
    }
}
?>
