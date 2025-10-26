<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$citas_stmt = $pdo->query("SELECT c.id, c.fecha_hora, c.estado, u.nombre AS cliente_nombre, s.nombre AS servicio_nombre FROM citas c JOIN usuarios u ON c.id_cliente = u.id JOIN servicios s ON c.id_servicio = s.id WHERE c.estado != 'cancelada' ORDER BY c.fecha_hora ASC");
$citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="text-3xl font-bold mb-4 text-gray-800">Panel de Administración de Citas</h2>
<p class="text-lg text-gray-700 mb-6">Aquí puedes ver y gestionar todas las citas agendadas.</p>

<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Cliente</th>
                <th class="py-3 px-4 text-left">Servicio</th>
                <th class="py-3 px-4 text-left">Fecha y Hora</th>
                <th class="py-3 px-4 text-left">Estado</th>
                <th class="py-3 px-4 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (count($citas) > 0): ?>
                <?php foreach ($citas as $cita): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4"><?= htmlspecialchars($cita['cliente_nombre']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($cita['servicio_nombre']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?></td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                <?php if ($cita['estado'] === 'pendiente'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif ($cita['estado'] === 'confirmada'): ?> bg-green-100 text-green-800
                                <?php endif; ?>">
                                <?= htmlspecialchars(ucfirst($cita['estado'])) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 space-x-2">
                            <?php if ($cita['estado'] === 'pendiente'): ?>
                                <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=confirmada" class="inline-block bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-1 px-3 rounded-md no-underline transition-colors">Confirmar</a>
                                <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=cancelada" class="inline-block bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-3 rounded-md no-underline transition-colors">Cancelar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">No hay citas para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>