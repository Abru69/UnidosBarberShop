<?php
require_once '../config/database.php';
// Usamos el header_cliente para este dashboard
include '../includes/header_cliente.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
// Criterio RF4: Solo mostrar servicios ACTIVOS
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// (La consulta de citas se movió a client/citas.php, este dashboard es solo para agendar)
?>
<p class="text-lg text-gray-700 mb-6">Bienvenido, aquí puedes agendar una nueva cita.</p>

<div class="bg-white p-8 mt-6 mx-auto max-w-lg rounded-lg shadow-xl border border-gray-200">
    <h3 class="text-2xl font-bold text-center mb-6">Agendar Nueva Cita</h3>
    
    <div id="mensaje-ajax" class="mb-4"></div>
    
    <form id="form-agendar-cita" method="POST">
        <div class="mb-4">
            <label for="servicio" class="block text-gray-700 font-medium mb-1">Selecciona un Servicio:</label>
            <select name="servicio" id="servicio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?= $servicio['id'] ?>">
                        <?= htmlspecialchars($servicio['nombre']) ?> ($<?= number_format($servicio['precio'], 2) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-4">
            <label for="fechaCita" class="block text-gray-700 font-medium mb-1">Fecha:</label>
            <input type="date" name="fecha" id="fechaCita" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div class="mb-6">
            <label for="horaCita" class="block text-gray-700 font-medium mb-1">Hora:</label>
            <select name="hora" id="horaCita" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-gray-100">
                <option value="">Selecciona una fecha primero</option>
            </select>
            <small class="text-gray-500">Horario de atención: 9:00 AM a 9:00 PM</small>
        </div>
        <button type="submit" name="agendar" class="w-full bg-primary text-white py-2 px-4 rounded-lg font-bold hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
            Agendar Cita
        </button>
    </form>
</div>

<div class="mt-8 max-w-4xl mx-auto">
    <img src="../images/Corte_barber.png" alt="Corte de cabello" class="w-full h-auto rounded-lg shadow-lg">
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Lógica de Fecha Mínima (Evitar agendar en el pasado) ---
    const inputFecha = document.getElementById('fechaCita');
    const hoy = new Date();
    const anio = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    const fechaMinima = `${anio}-${mes}-${dia}`;
    inputFecha.setAttribute('min', fechaMinima);

    // --- LÓGICA DE HORARIOS DISPONIBLES (RF2/HU1) ---
    const selectHora = document.getElementById('horaCita');

    inputFecha.addEventListener('change', function() {
        const fechaSeleccionada = inputFecha.value;

        // Resetear y deshabilitar el select de hora
        selectHora.innerHTML = '<option value="">Cargando...</option>';
        selectHora.disabled = true;
        selectHora.classList.add('bg-gray-100');

        // Validar que la fecha sea válida y no sea anterior a hoy
        if (!fechaSeleccionada || fechaSeleccionada < fechaMinima) {
            selectHora.innerHTML = '<option value="">Selecciona una fecha válida</option>';
            return;
        }

        // Llamada AJAX (Fetch) al nuevo endpoint
        fetch(`consultar_disponibilidad.php?fecha=${fechaSeleccionada}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor.');
            }
            return response.json();
        })
        .then(data => {
            selectHora.innerHTML = ''; // Limpiar el "Cargando..."
            
            if (data.success && data.horas.length > 0) {
                data.horas.forEach(hora => {
                    const option = document.createElement('option');
                    option.value = hora;
                    option.textContent = hora;
                    selectHora.appendChild(option);
                });
                selectHora.disabled = false;
                selectHora.classList.remove('bg-gray-100');
            } else {
                selectHora.innerHTML = '<option value="">No hay horas disponibles</option>';
            }
        })
        .catch(error => {
            console.error('Error al cargar horarios:', error);
            selectHora.innerHTML = '<option value="">Error al cargar horarios</option>';
        });
    });

    // --- Lógica de Envío del Formulario (Agendar Cita) ---
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
            // Mostrar mensaje de éxito o error
            if (data.success) {
                mensajeDiv.innerHTML = `<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">${data.message}</p>`;
                form.reset();
                // Resetear el selector de hora
                selectHora.innerHTML = '<option value="">Selecciona una fecha primero</option>';
                selectHora.disabled = true;
                selectHora.classList.add('bg-gray-100');
            } else {
                mensajeDiv.innerHTML = `<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">${data.message}</p>`;
            }
        })
        .catch(error => {
            mensajeDiv.innerHTML = `<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Error de conexión. Inténtalo de nuevo.</p>`;
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