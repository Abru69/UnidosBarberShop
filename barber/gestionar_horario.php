<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

// Lógica para AÑADIR un nuevo bloqueo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bloquear_horario'])) {
    
    // --- INICIO DE VALIDACIÓN CSRF ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error de validación de seguridad.</p>';
    } else {
        // --- FIN VALIDACIÓN CSRF ---
        $fecha_inicio = $_POST['fecha_inicio'] . ' ' . $_POST['hora_inicio'];
        $fecha_fin = $_POST['fecha_fin'] . ' ' . $_POST['hora_fin'];
        $motivo = $_POST['motivo'];

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            if (strtotime($fecha_fin) > strtotime($fecha_inicio)) {
                $stmt = $pdo->prepare("INSERT INTO horarios_bloqueados (fecha_inicio, fecha_fin, motivo) VALUES (?, ?, ?)");
                if ($stmt->execute([$fecha_inicio, $fecha_fin, $motivo])) {
                    $message = '<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">Horario bloqueado con éxito.</p>';
                } else {
                    $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error al bloquear el horario.</p>';
                }
            } else {
                $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">La fecha y hora de fin debe ser posterior a la de inicio.</p>';
            }
        } else {
            $message = '<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Por favor, completa todos los campos de fecha y hora.</p>';
        }
    }
}

// Obtener todos los horarios bloqueados para mostrarlos
$bloqueos = $pdo->query("SELECT * FROM horarios_bloqueados ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold mb-6">Gestionar Mi Disponibilidad</h2>

    <div class="bg-white p-6 mb-8 rounded-lg shadow-xl border border-gray-200">
        <h3 class="text-xl font-semibold mb-4">Bloquear un Período de Tiempo</h3>
        <?php echo $message; ?>
        <form action="gestionar_horario.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="fecha_inicio" class="block text-gray-700 font-medium mb-1">Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                 <div class="mb-4">
                    <label for="hora_inicio" class="block text-gray-700 font-medium mb-1">Hora de Inicio:</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="mb-4">
                    <label for="fecha_fin" class="block text-gray-700 font-medium mb-1">Fecha de Fin:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="mb-4">
                    <label for="hora_fin" class="block text-gray-700 font-medium mb-1">Hora de Fin:</label>
                    <input type="time" name="hora_fin" id="hora_fin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="mb-6">
                <label for="motivo" class="block text-gray-700 font-medium mb-1">Motivo (opcional):</label>
                <input type="text" name="motivo" id="motivo" placeholder="Ej: Vacaciones, Comida, Asunto personal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <button type="submit" name="bloquear_horario" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                Bloquear Horario
            </button>
        </form>
    </div>

    <h3 class="text-2xl font-semibold mb-4">Mis Períodos Bloqueados</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Desde</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Hasta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Motivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($bloqueos as $bloqueo): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($bloqueo['fecha_inicio']))) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($bloqueo['fecha_fin']))) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars($bloqueo['motivo']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="eliminar_horario.php?id=<?= $bloqueo['id'] ?>&token=<?= $csrf_token ?>" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700 transition-colors" onclick="return confirm('¿Estás seguro de que quieres eliminar este bloqueo?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>