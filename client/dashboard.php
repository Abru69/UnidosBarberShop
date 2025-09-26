<?php
require_once '../config/database.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$citas_stmt = $pdo->prepare("SELECT c.id, c.fecha_hora, c.estado, s.nombre AS servicio_nombre, s.precio FROM citas c JOIN servicios s ON c.id_servicio = s.id WHERE c.id_cliente = ? ORDER BY c.fecha_hora DESC");
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
        <div id="mensaje-ajax"></div>
        <form id="form-agendar-cita" method="POST">
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="citas-tbody">
            <?php if (count($citas) > 0): ?>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?></td>
                        <td><?= htmlspecialchars($cita['servicio_nombre']) ?></td>
                        <td>$<?= htmlspecialchars(number_format($cita['precio'], 2)) ?></td>
                        <td><?= htmlspecialchars(ucfirst($cita['estado'])) ?></td>
                        <td class="action-links">
                            <?php
                            $fecha_cita = new DateTime($cita['fecha_hora']);
                            $ahora = new DateTime();
                            if ($fecha_cita < $ahora):
                            ?>
                                <a href="eliminar_cita.php?id=<?= $cita['id'] ?>" class="cancel-link" onclick="return confirm('¿Estás seguro de que quieres eliminar esta cita de tu historial?');">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aún no tienes citas agendadas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputFecha = document.getElementById('fechaCita');
        const hoy = new Date();
        const anio = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');
        inputFecha.setAttribute('min', `${anio}-${mes}-${dia}`);

        const form = document.getElementById('form-agendar-cita');
        const mensajeDiv = document.getElementById('mensaje-ajax');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const boton = form.querySelector('button[type="submit"]');
            boton.disabled = true;
            boton.textContent = 'Agendando...';
            
            const formData = new FormData(form);

            fetch('agendar_cita.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mensajeDiv.innerHTML = `<p class="message ${data.success ? 'success' : 'error'}">${data.message}</p>`;

                // CAMBIO: Lógica para actualizar la tabla dinámicamente
                if (data.success && data.cita) {
                    form.reset(); // Limpiamos el formulario
                    const tbody = document.getElementById('citas-tbody');

                    // 1. Buscamos y eliminamos la fila de "no hay citas" si es que existe
                    const noCitasRow = tbody.querySelector('td[colspan="5"]');
                    if (noCitasRow) {
                        noCitasRow.parentElement.remove();
                    }

                    // 2. Creamos la nueva fila con los datos de la cita
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${data.cita.fecha_formateada}</td>
                        <td>${data.cita.servicio_nombre}</td>
                        <td>${data.cita.precio_formateado}</td>
                        <td>${data.cita.estado_formateado}</td>
                        <td class="action-links"></td>
                    `; // La nueva cita no tendrá acciones (como eliminar)

                    // 3. Añadimos la nueva fila al principio de la tabla
                    tbody.prepend(newRow);
                }
            })
            .catch(error => {
                mensajeDiv.innerHTML = `<p class="message error">Error de conexión. Inténtalo de nuevo.</p>`;
                console.error('Error:', error);
            })
            .finally(() => {
                boton.disabled = false;
                boton.textContent = 'Agendar Cita';
            });
        });
    });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>