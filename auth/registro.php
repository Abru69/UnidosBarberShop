<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- INICIO DE VALIDACIÓN CSRF ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
         $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error de validación de seguridad. Intente de nuevo.</p>';
    } else {
        // --- FIN VALIDACIÓN CSRF ---

        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // (Lógica de validación de email único...)
        $stmt_check = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">El correo electrónico ya está registrado. Por favor, <a href="login.php" class="text-red-900 underline">inicia sesión</a>.</p>';
        } else {
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')";
            $stmt = $pdo->prepare($sql);
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            
            if ($stmt->execute()) {
                $message = '<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">Usuario creado con éxito. Ahora puedes <a href="login.php" class="text-green-900 underline">iniciar sesión</a>.</p>';
            } else {
                $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Lo sentimos, hubo un problema al crear tu cuenta.</p>';
            }
        }
    }
}
?>

<div class="bg-white p-8 mt-6 mx-auto max-w-md rounded-lg shadow-xl border border-gray-200">
    <h2 class="text-2xl font-bold text-center mb-6">Crear una Cuenta</h2>
    <?php if(!empty($message)): ?>
        <?= $message ?>
    <?php endif; ?>
    <form action="registro.php" method="POST">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
        
        <div class="mb-4">
            <label for="nombre" class="block text-gray-700 font-medium mb-1">Nombre Completo</label>
            <input type="text" name="nombre" id="nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-1">Correo Electrónico</label>
            <input type="email" name="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-6">
            <label for="password" class="block text-gray-700 font-medium mb-1">Contraseña</label>
            <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
            Registrarse
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>