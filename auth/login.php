<?php
require_once '../config/database.php';
include '../includes/header.php'; // Esto ya incluye <html>, <head>, <header> y <main>
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

<div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-md mx-auto my-8">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Iniciar Sesión</h2>
    
    <?php if(!empty($message)): ?>
        <p class="p-4 mb-4 rounded-md bg-red-100 text-red-700 text-center"><?= $message ?></p>
    <?php endif; ?>
    
    <form action="login.php" method="POST" class="space-y-4">
        <div>
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico</label>
            <input type="email" name="email" id="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
            <input type="password" name="password" id="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
            Entrar
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; // Esto cierra </main>, <body> y <html> ?>