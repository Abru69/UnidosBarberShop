<?php
require_once '../config/database.php';
include '../includes/header.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')";
    $stmt = $pdo->prepare($sql);
    $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':password', $password_hash);
    
    if ($stmt->execute()) {
        $message = 'Usuario creado con éxito. Ahora puedes <a href="login.php">iniciar sesión</a>.';
    } else {
        $message = 'Lo sentimos, hubo un problema al crear tu cuenta.';
    }
}
?>

<div class="form-container">
    <h2>Crear una Cuenta</h2>
    <?php if(!empty($message)): ?>
        <p class="message success"><?= $message ?></p>
    <?php endif; ?>
    <form action="registro.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="btn">Registrarse</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>