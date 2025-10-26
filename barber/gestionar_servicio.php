<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_servicio'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    if (!empty($nombre) && is_numeric($precio)) {
        $stmt = $pdo->prepare("INSERT INTO servicios (nombre, precio) VALUES (?, ?)");
        if ($stmt->execute([$nombre, $precio])) {
            $message = '<p class="p-4 mb-4 rounded-md bg-green-100 text-green-700 text-center">Servicio añadido con éxito.</p>';
        } else {
            $message = '<p class="p-4 mb-4 rounded-md bg-red-100 text-red-700 text-center">Error al añadir el servicio.</p>';
        }
    } else {
        $message = '<p class="p-4 mb-4 rounded-md bg-red-100 text-red-700 text-center">Por favor, introduce un nombre y un precio válido.</p>';
    }
}

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Gestionar Servicios de la Barbería</h2>

    <div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-lg mx-auto mb-8">
        <h3 class="text-2xl font-bold text-center mb-6 text-gray-800">Añadir Nuevo Servicio</h3>
        <?php echo $message; ?>
        <form action="gestionar_servicio.php" method="POST" class="space-y-4">
            <div>
                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Servicio:</label>
                <input type="text" name="nombre" id="nombre" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <div>
                <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio (ej: 250.00):</label>
                <input type="text" name="precio" id="precio" required pattern="[0-9]+(\.[0-9]{1,2})?" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <button type="submit" name="crear_servicio" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                Añadir Servicio
            </button>
        </form>
    </div>

    <h3 class="text-2xl font-bold mb-4 text-gray-800">Servicios Actuales</h3>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Nombre</th>
                    <th class="py-3 px-4 text-left">Precio</th>
                    <th class="py-3 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($servicios as $servicio): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4"><?= htmlspecialchars($servicio['nombre']) ?></td>
                    <td class="py-3 px-4">$<?= htmlspecialchars(number_format($servicio['precio'], 2)) ?></td>
                    <td class="py-3 px-4 space-x-2">
                        <a href="editar_servicio.php?id=<?= $servicio['id'] ?>" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-3 rounded-md no-underline transition-colors">
                            Editar
                        </a>
                        <a href="eliminar_servicio.php?id=<?= $servicio['id'] ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-3 rounded-md no-underline transition-colors" onclick="return confirm('ADVERTENCIA: Se eliminarán todas las citas asociadas a este servicio. ¿Estás seguro?');">
                            Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>