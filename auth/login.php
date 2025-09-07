<?php
require_once '../config/database.php';
// Requerimos el header.php de la carpeta includes
include '../includes/header.php';
$message = '';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . ($_SESSION['usuario_rol'] === 'barbero' ? '../barber/dashboard.php' : '../client/dashboard.php'));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare('SELECT id, email, password, rol FROM usuarios WHERE email = :email');
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($_POST['password'], $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        if ($usuario['rol'] === 'barbero') {
            header('Location: ../barber/dashboard.php');
        } else {
            header('Location: ../client/dashboard.php');
        }
        exit;
    } else {
        $message = 'Email o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="form-container">
    <h2>Iniciar Sesión</h2>
    <?php if(!empty($message)): ?>
        <p class="message error"><?= $message ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="btn">Entrar</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>