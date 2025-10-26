<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: gestionar_servicio.php');
    exit;
}
$id_servicio = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    if (!empty($nombre) && is_numeric($precio)) {
        $stmt = $pdo->prepare("UPDATE servicios SET nombre = ?, precio = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $precio, $id_servicio])) {
            header('Location: gestionar_servicio.php');
            exit;
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->execute([$id_servicio]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    header('Location: gestionar_servicio.php');
    exit;
}
?>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-lg mx-auto my-8">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Editar Servicio</h2>
    <form action="editar_servicio.php?id=<?= $id_servicio ?>" method="POST" class="space-y-4">
        <div>
            <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Servicio:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($servicio['nombre']) ?>" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
            <input type="text" name="precio" id="precio" value="<?= htmlspecialchars($servicio['precio']) ?>" required pattern="[0-9]+(\.[0-9]{1,2})?" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
            Actualizar Servicio
        </button>
        <a href="gestionar_servicio.php" class="block text-center mt-4 text-gray-600 hover:text-blue-600 hover:underline">Cancelar</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>