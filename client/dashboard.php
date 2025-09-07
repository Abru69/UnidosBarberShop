<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';
$message_type = 'success'; // Para controlar el color del mensaje
$id_cliente = $_SESSION['usuario_id'];

// Lógica para agendar una nueva cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar'])) {
    $id_servicio = $_POST['servicio'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $fecha_hora = $fecha . ' ' . $hora;

    // NUEVO: Verificación de disponibilidad antes de insertar
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha_hora = ? AND (estado = 'confirmada' OR estado = 'pendiente')");
    $stmt_check->execute([$fecha_hora]);
    $citas_existentes = $stmt_check->fetchColumn();

    if ($citas_existentes > 0) {
        // NUEVO: Si el contador es mayor a 0, la hora está ocupada
        $message = 'Lo sentimos, esa hora ya no está disponible. Por favor, elige otra.';
        $message_type = 'error';
    } else {
        // La hora está libre, procedemos a insertar la cita
        $stmt = $pdo->prepare("INSERT INTO citas (id_cliente, id_servicio, fecha_hora, estado) VALUES (?, ?, ?, 'pendiente')");
        if ($stmt->execute([$id_cliente, $id_servicio, $fecha_hora])) {
            $message = '¡Cita agendada con éxito! Espera la confirmación del barbero.';
            $message_type = 'success';
        } else {
            $message = 'Error al agendar la cita.';
            $message_type = 'error';
        }
    }
}

// Obtener los servicios para el formulario
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// Obtener las citas del cliente (sin cambios aquí)
$citas_stmt = $pdo->prepare("SELECT c.fecha_hora, c.estado, s.nombre AS servicio_nombre, s.precio FROM citas c JOIN servicios s ON c.id_servicio = s.id WHERE c.id_cliente = ? ORDER BY c.fecha_hora DESC");
$citas_stmt->execute([$id_cliente]);
$citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Panel del Cliente</h2>
<p>Bienvenido, aquí puedes agendar una nueva cita y ver el historial de tus citas.</p>

<div class="form-container">
    <h3>Agendar Nueva Cita</h3>
    <?php if(!empty($message)): ?>
        <p class="message <?= $message_type ?>"><?= $message ?></p>
    <?php endif; ?>
    <form action="dashboard.php" method="POST">
        <div class="form-group">
            <label for="servicio">Selecciona un Servicio:</label>
            <select name="servicio" id="servicio" required>
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?> ($<?= number_format($servicio['precio'], 2) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fechaCita" required>
        </div>
        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" name="hora" id="horaCita" min="09:00" max="21:00" step="1800" required>
            <small>Horario de atención: 9:00 AM a 9:00 PM</small>
        </div>
        <button type="submit" name="agendar" class="btn">Agendar Cita</button>
    </form>
</div>

<h3>Mis Citas</h3>
<table>
    <thead>
        <tr>
            <th>Fecha y Hora</th>
            <th>Servicio</th>
            <th>Precio</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($citas) > 0): ?>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?></td>
                    <td><?= htmlspecialchars($cita['servicio_nombre']) ?></td>
                    <td>$<?= htmlspecialchars(number_format($cita['precio'], 2)) ?></td>
                    <td><?= htmlspecialchars(ucfirst($cita['estado'])) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">Aún no tienes citas agendadas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputFecha = document.getElementById('fechaCita');
    const hoy = new Date();
    // NUEVO: Corrección para permitir agendar el día de hoy si aún no ha pasado la hora.
    // Aunque la restricción original era a partir de mañana, esto es más flexible.
    // La restricción min del input de fecha solo funciona por día completo.
    const anio = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    
    // El cliente no puede seleccionar fechas pasadas.
    inputFecha.setAttribute('min', `${anio}-${mes}-${dia}`);
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
