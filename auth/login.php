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
<div class="bg-white p-8 mt-6 mx-auto max-w-md rounded-lg shadow-xl border border-gray-200">
    <h2 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h2>
    <?php if(!empty($message)): ?>
        <p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center"><?= $message ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-1">Correo Electrónico</label>
            <input type="email" name="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-6">
            <label for="password" class="block text-gray-700 font-medium mb-1">Contraseña</label>
            <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
            Entrar
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>