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

<div class="container">
    <div class="form-container">
        <h2>Editar Servicio</h2>
        <form action="editar_servicio.php?id=<?= $id_servicio ?>" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Servicio:</label>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="text" name="precio" id="precio" value="<?= htmlspecialchars($servicio['precio']) ?>" required pattern="[0-9]+(\.[0-9]{1,2})?">
            </div>
            <button type="submit" class="btn">Actualizar Servicio</button>
            <a href="gestionar_servicio.php" style="display:block; text-align:center; margin-top:10px;">Cancelar</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>