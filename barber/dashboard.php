<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

// --- 1. Consulta para la TABLA (se mantiene igual) ---
$citas_stmt = $pdo->query("SELECT c.id, c.fecha_hora, c.estado, u.nombre AS cliente_nombre, s.nombre AS servicio_nombre FROM citas c JOIN usuarios u ON c.id_cliente = u.id JOIN servicios s ON c.id_servicio = s.id WHERE c.estado != 'cancelada' ORDER BY c.fecha_hora ASC");
$citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="text-3xl font-bold mb-4">Panel de Administración de Citas</h2>
<p class="text-gray-600 mb-6">Aquí puedes ver y gestionar todas las citas agendadas.</p>

<!-- =========TABLA DE CITAS========= -->
<h3 class="text-2xl font-semibold mb-4">Citas en Lista</h3>

<div class="bg-white rounded-lg shadow-xl border border-gray-200 mb-12" id="CitaListas">

    <!-- Este es solo para las pantallas grandes-->
    <div class="encabezado hidden md:grid grid-cols-5 bg-gray-800 text-white px-6 py-3 text-xs font-medium uppercase tracking-wider">
        <div>Cliente</div>
        <div>Servicio</div>
        <div>Fecha y Hora</div>
        <div>Estado</div>
        <div>Acciones</div>
    </div>

    <div class="divide-y divide-gray-200">
        <?php if (count($citas) > 0): ?>
            <?php foreach ($citas as $cita): ?>

                <div class="cita-row grid md:grid-cols-5 gap-4 px-6 py-4 hover:bg-gray-50">

                    
                    <div>
                        <span class="label block md:hidden font-semibold text-gray-600">Cliente:</span>
                        <?= htmlspecialchars($cita['cliente_nombre']) ?>
                    </div>

                    <div>
                        <span class="label block md:hidden font-semibold text-gray-600">Servicio:</span>
                        <?= htmlspecialchars($cita['servicio_nombre']) ?>
                    </div>

                    <div>
                        <span class="label block md:hidden font-semibold text-gray-600">Fecha y Hora:</span>
                        <?= htmlspecialchars(date('d/m/Y h:i A', strtotime($cita['fecha_hora']))) ?>
                    </div>

               
                    <div>
                        <span class="label block md:hidden font-semibold text-gray-600">Estado:</span>

                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php 
                                if ($cita['estado'] === 'confirmada') echo 'bg-green-100 text-green-800';
                                elseif ($cita['estado'] === 'pendiente') echo 'bg-yellow-100 text-yellow-800';
                            ?>">
                            <?= htmlspecialchars(ucfirst($cita['estado'])) ?>
                        </span>
                    </div>

                
                    <div class="acciones text-sm text-right md:text-left font-medium">
                        <span class="label block md:hidden font-semibold text-gray-600">Acciones:</span>

                        <?php if ($cita['estado'] === 'pendiente'): ?>
                            <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=confirmada&token=<?= $csrf_token ?>"
                            class="bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-green-700 transition-colors mr-2">
                                Confirmar
                            </a>

                            <a href="actualizar_cita.php?id=<?= $cita['id'] ?>&accion=cancelada&token=<?= $csrf_token ?>"
                            class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700 transition-colors">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="px-6 py-4 text-center text-gray-500">
                No hay citas para mostrar en la lista.
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- =========  CALENDARIO  ======== -->
<h3 class="text-2xl font-semibold mb-4">Citas en Calendario</h3>

<!-- Cargar el script de FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<!-- Contenedor del Calendario -->
<div id="calendario-barbero-container" class="bg-white p-4 md:p-6 rounded-lg shadow-xl border border-gray-200">
    <div id="calendario-barbero"></div>
</div>

<!-- Script de Inicialización de FullCalendar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario-barbero');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        // Vistas solicitadas: Semana (default) y Día
        initialView: 'timeGridWeek', 
        locale: 'es', // Español
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            // Vistas solicitadas por el usuario: Semana y Día
            right: 'timeGridWeek,timeGridDay,dayGridMonth'
        },
        
        // Cargar los eventos desde el nuevo endpoint
        events: 'obtener_eventos_barbero.php', 

        height: 'auto', // Responsivo
        
        // Limitar las horas visibles a las de la barbería
        slotMinTime: '09:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false, // No mostrar el slot "todo el día"
        
        eventTimeFormat: { 
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false // Formato 24h (ej. 14:30)
        },
        
        buttonText: {
            week: 'Semana',
            day: 'Día',
            month: 'Mes'
        }
    });
    
    // Renderizar el calendario
    calendar.render();
});
</script>

<?php include '../includes/footer.php'; ?>