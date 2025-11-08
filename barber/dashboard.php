<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

// (La consulta de $citas permanece igual)
$citas_stmt = $pdo->query("SELECT c.id, c.fecha_hora, c.estado, u.nombre AS cliente_nombre, s.nombre AS servicio_nombre FROM citas c JOIN usuarios u ON c.id_cliente = u.id JOIN servicios s ON c.id_servicio = s.id WHERE c.estado != 'cancelada' ORDER BY c.fecha_hora ASC");
$citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="text-3xl font-bold mb-4">Panel de Administración de Citas</h2>
<p class="text-gray-600 mb-6">Aquí puedes ver y gestionar todas las citas agendadas.</p>

<div class="overflow-x-auto bg-white rounded-lg shadow-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Servicio</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha y Hora</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (count($citas) > 0): ?>
                <?php foreach ($citas as $cita): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars($cita['cliente_nombre']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars($cita['servicio_nombre']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php 
                                    if ($cita['estado'] === 'confirmada') echo 'bg-green-100 text-green-800';
                                    elseif ($cita['estado'] === 'pendiente') echo 'bg-yellow-100 text-yellow-800';
                                    else echo 'bg-red-100 text-red-800';
                                ?>">
                                <?= htmlspecialchars(ucfirst($cita['estado'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if ($cita['estado'] === 'pendiente'): ?>
                                <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=confirmada&token=<?= $csrf_token ?>" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-green-700 transition-colors mr-2">Confirmar</a>
                                <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=cancelada&token=<?= $csrf_token ?>" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700 transition-colors">Cancelar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay citas para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>