<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

// Lógica para AÑADIR un nuevo bloqueo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bloquear_horario'])) {
    $fecha_inicio = $_POST['fecha_inicio'] . ' ' . $_POST['hora_inicio'];
    $fecha_fin = $_POST['fecha_fin'] . ' ' . $_POST['hora_fin'];
    $motivo = $_POST['motivo'];

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        // Validar que la fecha de fin no sea anterior a la de inicio
        if (strtotime($fecha_fin) > strtotime($fecha_inicio)) {
            $stmt = $pdo->prepare("INSERT INTO horarios_bloqueados (fecha_inicio, fecha_fin, motivo) VALUES (?, ?, ?)");
            if ($stmt->execute([$fecha_inicio, $fecha_fin, $motivo])) {
                $message = '<p class="message success">Horario bloqueado con éxito.</p>';
            } else {
                $message = '<p class="message error">Error al bloquear el horario.</p>';
            }
        } else {
            $message = '<p class="message error">La fecha y hora de fin debe ser posterior a la de inicio.</p>';
        }
    } else {
        $message = '<p class="message error">Por favor, completa todos los campos de fecha y hora.</p>';
    }
}

// Obtener todos los horarios bloqueados para mostrarlos
$bloqueos = $pdo->query("SELECT * FROM horarios_bloqueados ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestionar Mi Disponibilidad</h2>

    <div class="form-container">
        <h3>Bloquear un Período de Tiempo</h3>
        <?php echo $message; ?>
        <form action="gestionar_horario.php" method="POST">
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" required>
            </div>
             <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" name="hora_inicio" id="hora_inicio" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" required>
            </div>
            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" name="hora_fin" id="hora_fin" required>
            </div>
            <div class="form-group">
                <label for="motivo">Motivo (opcional):</label>
                <input type="text" name="motivo" id="motivo" placeholder="Ej: Vacaciones, Comida, Asunto personal">
            </div>
            <button type="submit" name="bloquear_horario" class="btn">Bloquear Horario</button>
        </form>
    </div>

    <h3>Mis Períodos Bloqueados</h3>
    <table>
        <thead>
            <tr>
                <th>Desde</th>
                <th>Hasta</th>
                <th>Motivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bloqueos as $bloqueo): ?>
            <tr>
                <td><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($bloqueo['fecha_inicio']))) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($bloqueo['fecha_fin']))) ?></td>
                <td><?= htmlspecialchars($bloqueo['motivo']) ?></td>
                <td class="action-links">
                    <a href="eliminar_horario.php?id=<?= $bloqueo['id'] ?>" class="cancel-link" onclick="return confirm('¿Estás seguro de que quieres eliminar este bloqueo?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>