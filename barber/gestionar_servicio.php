<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_servicio'])) {
    
    // --- INICIO DE VALIDACIÓN CSRF ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error de validación de seguridad.</p>';
    } else {
        // --- FIN VALIDACIÓN CSRF ---
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];

        if (!empty($nombre) && is_numeric($precio)) {
            $stmt = $pdo->prepare("INSERT INTO servicios (nombre, precio, activo) VALUES (?, ?, 1)");
            if ($stmt->execute([$nombre, $precio])) {
                $message = '<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">Servicio añadido con éxito.</p>';
            } else {
                $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error al añadir el servicio.</p>';
            }
        } else {
            $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Por favor, introduce un nombre y un precio válido.</p>';
        }
    }
}

// CAMBIO: Solo se muestran los servicios activos
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold mb-6">Gestionar Servicios de la Barbería</h2>

    <div class="bg-white p-6 mb-8 rounded-lg shadow-xl border border-gray-200">
        <h3 class="text-xl font-semibold mb-4">Añadir Nuevo Servicio</h3>
        <?php echo $message; ?>
        <form action="gestionar_servicio.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 font-medium mb-1">Nombre del Servicio:</label>
                <input type="text" name="nombre" id="nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="mb-6">
                <label for="precio" class="block text-gray-700 font-medium mb-1">Precio (ej: 250.00):</label>
                <input type="text" name="precio" id="precio" required pattern="[0-9]+(\.[0-9]{1,2})?" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <button type="submit" name="crear_servicio" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                Añadir Servicio
            </button>
        </form>
    </div>

    <h3 class="text-2xl font-semibold mb-4">Servicios Actuales (Activos)</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($servicios as $servicio): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars($servicio['nombre']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">$<?= htmlspecialchars(number_format($servicio['precio'], 2)) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="editar_servicio.php?id=<?= $servicio['id'] ?>" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-green-700 transition-colors mr-2">Editar</a>
                        
                        <a href="eliminar_servicio.php?id=<?= $servicio['id'] ?>&token=<?= $csrf_token ?>" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700 transition-colors"
                            onclick="return confirm('¿Estás seguro de que quieres DESACTIVAR este servicio? Las citas existentes NO se eliminarán, pero el servicio ya no estará disponible para nuevas reservas.');">Desactivar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>