<?php
require_once '../config/database.php';
// Importante: Usar el header_cliente para la navegación correcta
include '../includes/header_cliente.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$citas_stmt = $pdo->prepare("SELECT c.id, c.fecha_hora, c.estado, s.nombre AS servicio_nombre, s.precio FROM citas c JOIN servicios s ON c.id_servicio = s.id WHERE c.id_cliente = ? ORDER BY c.fecha_hora DESC");
$citas_stmt->execute([$id_cliente]);
$citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="loader.css">

<div class="loader-overlay">
    <div class="loader"></div>
</div>

<h3 class="text-3xl font-bold mb-6 text-gray-800">Mis Citas</h3>

<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Fecha y Hora</th>
                <th class="py-3 px-4 text-left">Servicio</th>
                <th class="py-3 px-4 text-left">Precio</th>
                <th class="py-3 px-4 text-left">Estado</th>
                <th class="py-3 px-4 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody class"divide-y divide-gray-200" id="citas-tbody">
            <?php if (count($citas) > 0): ?>
                <?php foreach ($citas as $cita): ?>
                    <tr class="hover:bg-gray-50 border-b border-gray-200">
                        <td class="py-3 px-4"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($cita['servicio_nombre']) ?></td>
                        <td class="py-3 px-4">$<?= htmlspecialchars(number_format($cita['precio'], 2)) ?></td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-sm
                                <?php if ($cita['estado'] === 'pendiente'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif ($cita['estado'] === 'confirmada'): ?> bg-green-100 text-green-800
                                <?php elseif ($cita['estado'] === 'cancelada'): ?> bg-red-100 text-red-700
                                <?php endif; ?>">
                                <?= htmlspecialchars(ucfirst($cita['estado'])) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <?php
                            $fecha_cita = new DateTime($cita['fecha_hora']);
                            $ahora = new DateTime();
                            // Permitir eliminar solo si la cita ya pasó (para limpiar historial)
                            if ($fecha_cita < $ahora):
                            ?>
                                <a href="eliminar_cita.php?id=<?= $cita['id'] ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-3 rounded-md no-underline transition-colors" onclick="return confirm('¿Estás seguro de que quieres eliminar esta cita de tu historial?');">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">Aún no tienes citas agendadas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="loader.js"></script>
<?php include '../includes/footer.php'; ?>