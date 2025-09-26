<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

// Consulta CORREGIDA para no mostrar citas con estado 'cancelada'
$citas_stmt = $pdo->query("SELECT c.id, c.fecha_hora, c.estado, u.nombre AS cliente_nombre, s.nombre AS servicio_nombre FROM citas c JOIN usuarios u ON c.id_cliente = u.id JOIN servicios s ON c.id_servicio = s.id WHERE c.estado != 'cancelada' ORDER BY c.fecha_hora ASC");
$citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Panel de Administración de Citas</h2>
<p>Aquí puedes ver y gestionar todas las citas agendadas.</p>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Fecha y Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($citas) > 0): ?>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?= htmlspecialchars($cita['cliente_nombre']) ?></td>
                    <td><?= htmlspecialchars($cita['servicio_nombre']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($cita['estado'])) ?></td>
                    <td class="action-links">
                        <?php if ($cita['estado'] === 'pendiente'): ?>
                            <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=confirmada" class="confirm-link">Confirmar</a>
                            <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=cancelada" class="cancel-link">Cancelar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No hay citas para mostrar.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>