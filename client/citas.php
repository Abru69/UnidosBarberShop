<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

// (La lógica PHP para obtener $citas ahora se maneja en obtener_eventos.php)
?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<h3 class="text-3xl font-bold mb-6">Mis Citas</h3>

<div id="calendario-container" class="bg-white p-4 md:p-6 rounded-lg shadow-xl border border-gray-200">
    <div id="calendario"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario');
    
    // --- INICIO DE LÓGICA RESPONSIVA (Riesgo 10) ---
    // Define el breakpoint (768px es el 'md' de Tailwind)
    const mobileBreakpoint = 768; 
    
    // Determinar la vista inicial y el header basado en el ancho de la pantalla
    let initialViewSetting = 'dayGridMonth';
    let headerToolbarSetting = {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
    };

    // Si la pantalla es de móvil (iPhone 11, iPhone 17 Pro Max, etc.)
    if (window.innerWidth < mobileBreakpoint) {
        initialViewSetting = 'listWeek'; // Cambiar a vista de "Lista Semanal"
        headerToolbarSetting = {
            left: 'prev,next',  // Simplificar header para más espacio
            center: 'title',
            right: 'listWeek,dayGridMonth' // Poner la vista de lista primero
        };
    }
    // --- FIN DE LÓGICA RESPONSIVA ---

    var calendar = new FullCalendar.Calendar(calendarEl, {
        // Aplicar la configuración responsiva
        initialView: initialViewSetting,
        headerToolbar: headerToolbarSetting,
        
        locale: 'es', // Poner el calendario en Español
        
        // Cargar los eventos desde el endpoint
        events: 'obtener_eventos.php', //

        // Ajustar la altura para que sea responsivo
        height: 'auto',
        
        // Texto del botón para la vista de lista (más amigable)
        buttonText: {
            list: 'Lista'
        },
        
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