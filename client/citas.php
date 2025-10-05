<?php

require_once '../config/database.php';
include '../includes/header_cliente.php';

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
    <link rel="stylesheet" href="loader.css">
    <title>Document</title>
</head>
<body>
    <div class="loader-overlay">
    <div class="loader"></div>
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
            <script src="loader.js"></script>
        </tbody>
    </table>

    
    <script>
   // citas.php
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-agendar-cita');
    const mensajeDiv = document.getElementById('mensaje-ajax');
    const inputFecha = document.getElementById('fechaCita');

    // mínimo fecha = hoy
    const hoy = new Date();
    inputFecha.setAttribute('min', hoy.toISOString().split("T")[0]);

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

            if (data.success && data.cita) {
                form.reset();
                const tbody = document.getElementById('citas-tbody');

                const noCitasRow = tbody.querySelector('td[colspan="5"]');
                if (noCitasRow) noCitasRow.parentElement.remove();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${data.cita.fecha_formateada}</td>
                    <td>${data.cita.servicio_nombre}</td>
                    <td>${data.cita.precio_formateado}</td>
                    <td>${data.cita.estado_formateado}</td>
                    <td class="action-links"></td>
                `;
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