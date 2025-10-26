<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bloquear_horario'])) {
    $fecha_inicio = $_POST['fecha_inicio'] . ' ' . $_POST['hora_inicio'];
    $fecha_fin = $_POST['fecha_fin'] . ' ' . $_POST['hora_fin'];
    $motivo = $_POST['motivo'];

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        if (strtotime($fecha_fin) > strtotime($fecha_inicio)) {
            $stmt = $pdo->prepare("INSERT INTO horarios_bloqueados (fecha_inicio, fecha_fin, motivo) VALUES (?, ?, ?)");
            if ($stmt->execute([$fecha_inicio, $fecha_fin, $motivo])) {
                $message = '<p class="p-4 mb-4 rounded-md bg-green-100 text-green-700 text-center">Horario bloqueado con éxito.</p>';
            } else {
                $message = '<p class="p-4 mb-4 rounded-md bg-red-100 text-red-700 text-center">Error al bloquear el horario.</p>';
            }
        } else {
            $message = '<p class="p-4 mb-4 rounded-md bg-red-100 text-red-700 text-center">La fecha y hora de fin debe ser posterior a la de inicio.</p>';
        }
    } else {
        $message = '<p class="p-4 mb-4 rounded-md bg-red-100 text-red-700 text-center">Por favor, completa todos los campos de fecha y hora.</p>';
    }
}

$bloqueos = $pdo->query("SELECT * FROM horarios_bloqueados ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Gestionar Mi Disponibilidad</h2>

    <div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-2xl mx-auto mb-8">
        <h3 class="text-2xl font-bold text-center mb-6 text-gray-800">Bloquear un Período de Tiempo</h3>
        <?php echo $message; ?>
        <form action="gestionar_horario.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="fecha_inicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                 <div>
                    <label for="hora_inicio" class="block text-gray-700 text-sm font-bold mb-2">Hora de Inicio:</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Fin:</label>

                    <input type="date" name="fecha_fin" id="fecha_fin" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div>
                    <label for="hora_fin" class="block text-gray-700 text-sm font-bold mb-2">Hora de Fin:</label>
                    <input type="time" name="hora_fin" id="hora_fin" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>
            <div class="mb-4">
                <label for="motivo" class="block text-gray-700 text-sm font-bold mb-2">Motivo (opcional):</label>
                <input type="text" name="motivo" id="motivo" placeholder="Ej: Vacaciones, Comida, Asunto personal" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <button type="submit" name="bloquear_horario" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                Bloquear Horario
            </button>
        </form>
    </div>

    <h3 class="text-2xl font-bold mb-4 text-gray-800">Mis Períodos Bloqueados</h3>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Desde</th>
                    <th class="py-3 px-4 text-left">Hasta</th>
                    <th class="py-3 px-4 text-left">Motivo</th>
                    <th class="py-3 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($bloqueos as $bloqueo): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($bloqueo['fecha_inicio']))) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($bloqueo['fecha_fin']))) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($bloqueo['motivo']) ?></td>
                    <td class="py-3 px-4">
                        <a href="eliminar_horario.php?id=<?= $bloqueo['id'] ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-3 rounded-md no-underline transition-colors" onclick="return confirm('¿Estás seguro de que quieres eliminar este bloqueo?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>