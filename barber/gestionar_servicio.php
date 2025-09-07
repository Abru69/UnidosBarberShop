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
            $message = '<p class="message success">Servicio añadido con éxito.</p>';
        } else {
            $message = '<p class="message error">Error al añadir el servicio.</p>';
        }
    } else {
        $message = '<p class="message error">Por favor, introduce un nombre y un precio válido.</p>';
    }
}

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestionar Servicios de la Barbería</h2>

    <div class="form-container">
        <h3>Añadir Nuevo Servicio</h3>
        <?php echo $message; ?>
        <form action="gestionar_servicio.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Servicio:</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio (ej: 250.00):</label>
                <input type="text" name="precio" id="precio" required pattern="[0-9]+(\.[0-9]{1,2})?">
            </div>
            <button type="submit" name="crear_servicio" class="btn">Añadir Servicio</button>
        </form>
    </div>

    <h3>Servicios Actuales</h3>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $servicio): ?>
            <tr>
                <td><?= htmlspecialchars($servicio['nombre']) ?></td>
                <td>$<?= htmlspecialchars(number_format($servicio['precio'], 2)) ?></td>
                <td class="action-links">
                    <a href="/BARBERBROS/barber/editar_servicio.php?id=<?= $servicio['id'] ?>"
                        class="confirm-link">Editar</a>
                    <a href="/BARBERBROS/barber/eliminar_servicio.php?id=<?= $servicio['id'] ?>" class="cancel-link"
                        onclick="return confirm('ADVERTENCIA: Se eliminarán todas las citas asociadas a este servicio. ¿Estás seguro?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>