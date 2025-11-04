<?php
require_once '../config/database.php';
include '../includes/header_cliente.php'; // Usa el header de cliente

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$message_info = '';
$message_pass = '';

// --- Lógica para Actualizar Información (Nombre/Email) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_info'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    // Criterio RF1 (Email Único): Verificar que el email no esté en uso por OTRO usuario
    $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt_check->execute([$email, $id_usuario]);
    
    if ($stmt_check->rowCount() > 0) {
        $message_info = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Ese correo electrónico ya está en uso por otra cuenta.</p>';
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $email, $id_usuario])) {
            $message_info = '<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">Información actualizada con éxito.</p>';
        } else {
            $message_info = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error al actualizar la información.</p>';
        }
    }
}

// --- Lógica para Cambiar Contraseña (Criterio de Éxito RF7) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_pass'])) {
    $pass_actual = $_POST['password_actual'];
    $pass_nueva = $_POST['password_nueva'];
    $pass_confirmar = $_POST['password_confirmar'];

    // 1. Obtener la contraseña actual hasheada del usuario
    $stmt_user = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt_user->execute([$id_usuario]);
    $usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // 2. Verificar que la contraseña actual es correcta
    if ($usuario && password_verify($pass_actual, $usuario['password'])) {
        // 3. Verificar que las nuevas contraseñas coinciden
        if ($pass_nueva === $pass_confirmar) {
            // 4. Hashear y actualizar la nueva contraseña
            $password_hash = password_hash($pass_nueva, PASSWORD_BCRYPT);
            $stmt_update = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            if ($stmt_update->execute([$password_hash, $id_usuario])) {
                $message_pass = '<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">Contraseña cambiada con éxito.</p>';
            } else {
                $message_pass = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error al cambiar la contraseña.</p>';
            }
        } else {
            $message_pass = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Las nuevas contraseñas no coinciden.</p>';
        }
    } else {
        $message_pass = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">La contraseña actual es incorrecta.</p>';
    }
}

// Obtener los datos actuales del usuario para rellenar el formulario
$stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);
$usuario_actual = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2 class="text-3xl font-bold mb-6">Mi Perfil</h2>

<div class="bg-white p-8 mt-6 mx-auto max-w-lg rounded-lg shadow-xl border border-gray-200 mb-8">
    <h3 class="text-2xl font-bold text-center mb-6">Información Personal</h3>
    <?= $message_info ?>
    <form action="perfil.php" method="POST">
        <div class="mb-4">
            <label for="nombre" class="block text-gray-700 font-medium mb-1">Nombre Completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($usuario_actual['nombre']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-6">
            <label for="email" class="block text-gray-700 font-medium mb-1">Correo Electrónico</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario_actual['email']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <button type="submit" name="actualizar_info" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors">
            Actualizar Información
        </button>
    </form>
</div>

<div class="bg-white p-8 mt-6 mx-auto max-w-lg rounded-lg shadow-xl border border-gray-200">
    <h3 class="text-2xl font-bold text-center mb-6">Cambiar Contraseña</h3>
    <?= $message_pass ?>
    <form action="perfil.php" method="POST">
        <div class="mb-4">
            <label for="password_actual" class="block text-gray-700 font-medium mb-1">Contraseña Actual</label>
            <input type="password" name="password_actual" id="password_actual" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label for="password_nueva" class="block text-gray-700 font-medium mb-1">Nueva Contraseña</label>
            <input type="password" name="password_nueva" id="password_nueva" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-6">
            <label for="password_confirmar" class="block text-gray-700 font-medium mb-1">Confirmar Nueva Contraseña</label>
            <input type="password" name="password_confirmar" id="password_confirmar" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <button type="submit" name="cambiar_pass" class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg font-bold hover:bg-gray-700 transition-colors">
            Cambiar Contraseña
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>