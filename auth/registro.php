<?php
require_once '../config/database.php';
include '../includes/header.php'; // Incluye el layout principal
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')";
    $stmt = $pdo->prepare($sql);
    $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':password', $password_hash);
    
    try {
        if ($stmt->execute()) {
            $message = 'Usuario creado con éxito. Ahora puedes <a href="login.php" class="font-bold text-blue-600 hover:underline">iniciar sesión</a>.';
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // Error de entrada duplicada (email)
            $message = 'Lo sentimos, ese correo electrónico ya está registrado.';
        } else {
            $message = 'Lo sentimos, hubo un problema al crear tu cuenta.';
        }
    }
}
?>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-md mx-auto my-8">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Crear una Cuenta</h2>
    
    <?php if(!empty($message)): ?>
        <p class="p-4 mb-4 rounded-md <?= strpos($message, 'éxito') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> text-center">
            <?= $message ?>
        </p>
    <?php endif; ?>
    
    <form action="registro.php" method="POST" class="space-y-4">
        <div>
            <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo</label>
            <input type="text" name="nombre" id="nombre" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico</label>
            <input type="email" name="email" id="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
            <input type="password" name="password" id="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
            Registrarse
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>