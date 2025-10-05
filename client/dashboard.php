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
    <!--<h2>Panel del Cliente</h2>-->
    <p>Bienvenido, aquí puedes agendar una nueva cita.</p>

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

            if (data.success) {
                form.reset();
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
    <div class="dashboard-banner">
 </div>
 <div class="ImagenCorte"> <img src="../images/Corte_barber.png" alt="Corte de cabello"> </div>
</body>
</html>
