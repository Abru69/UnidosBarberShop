<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$id_servicio = $_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- INICIO DE VALIDACIÓN CSRF ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error de validación de seguridad.</p>';
    } else {
        // --- FIN VALIDACIÓN CSRF ---
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];

        if (!empty($nombre) && is_numeric($precio)) {
            $stmt = $pdo->prepare("UPDATE servicios SET nombre = ?, precio = ? WHERE id = ?");
            if ($stmt->execute([$nombre, $precio, $id_servicio])) {
                $message = '<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">Servicio actualizado con éxito.</p>';
            } else {
                $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error al actualizar el servicio.</p>';
            }
        } else {
            $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Por favor, introduce un nombre y un precio válido.</p>';
        }
    }
}

// Obtener datos del servicio para el formulario
$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->execute([$id_servicio]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="max-w-lg mx-auto">
    <h2 class="text-3xl font-bold mb-6">Editar Servicio</h2>

    <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200">
        <?php echo $message; ?>
        <form action="editar_servicio.php?id=<?= $id_servicio ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 font-medium mb-1">Nombre del Servicio:</label>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($servicio['nombre']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="mb-6">
                <label for="precio" class="block text-gray-700 font-medium mb-1">Precio (ej: 250.00):</label>
                <input type="text" name="precio" id="precio" value="<?= htmlspecialchars($servicio['precio']) ?>" required pattern="[0-9]+(\.[0-9]{1,2})?" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors">
                Actualizar Servicio
            </button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>