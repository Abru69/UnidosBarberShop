<?php
require_once '../config/database.php';
// Usar el header único
include '../includes/header.php'; 

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

// (La lógica PHP para obtener $citas se ha movido a obtener_eventos.php)
?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<h3 class="text-3xl font-bold mb-6">Mis Citas</h3>

<div id="calendario-container" class="bg-white p-4 md:p-6 rounded-lg shadow-xl border border-gray-200">
    <div id="calendario"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // Vista inicial de Mes
        locale: 'es', // Poner el calendario en Español
        headerToolbar: {
            left: 'prev,next today', // Botones de navegación
            center: 'title', // Título (Mes y Año)
            right: 'dayGridMonth,timeGridWeek,listWeek' // Botones para cambiar de vista (Mes, Semana, Lista)
        },
        
        // Cargar los eventos desde el endpoint que creamos
        events: 'obtener_eventos.php', 

        // Ajustar la altura para que sea responsivo
        height: 'auto',
        
        // Estilo de los eventos (ya lo definimos en el JSON, pero esto es un extra)
        eventTimeFormat: { 
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        }
    });
    
    // Renderizar el calendario
    calendar.render();
});
</script>

<?php 
// Incluimos el footer que cierra </body> y </html>
include '../includes/footer.php'; 
?>