<?php
require_once '../config/database.php';
include '../includes/header.php'; // $csrf_token disponible

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<p class="text-lg text-gray-700 mb-6">Bienvenido, aquí puedes agendar una nueva cita.</p>

<div class="bg-white p-8 mt-6 mx-auto max-w-lg rounded-lg shadow-xl border border-gray-200">
    <h3 class="text-2xl font-bold text-center mb-6">Agendar Nueva Cita</h3>
    
    <input type="hidden" id="csrf_token_js" value="<?= htmlspecialchars($csrf_token); ?>">

    <div id="mensaje-ajax" class="mb-4"></div>
    
    <form id="form-agendar-cita" method="POST">
        <div class="mb-4">
            <label for="servicio" class="block text-gray-700 font-medium mb-1">1. Selecciona un Servicio:</label>
            <select name="servicio" id="servicio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?= $servicio['id'] ?>">
                        <?= htmlspecialchars($servicio['nombre']) ?> ($<?= number_format($servicio['precio'], 2) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-4">
            <label for="fechaCita" class="block text-gray-700 font-medium mb-1">2. Selecciona una Fecha:</label>
            <input type="date" name="fecha" id="fechaCita" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">3. Selecciona una Hora:</label>
            <div id="time-slots-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                <p class="col-span-full text-gray-500 text-sm">Por favor, selecciona una fecha para ver los horarios.</p>
            </div>
            <input type="hidden" name="hora" id="horaSeleccionada" value="">
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
    
    // (Lógica de Fecha Mínima y Carga de Horarios...)
    // ... (El código de 'consultar_disponibilidad.php' no necesita token 
    //      porque es una operación de LECTURA (GET) no sensible.
    //      Solo las acciones de ESCRITURA (POST/DELETE/UPDATE) lo necesitan.)

    // --- Lógica de Envío del Formulario (Agendar Cita) ---
    const form = document.getElementById('form-agendar-cita');
    const mensajeDiv = document.getElementById('mensaje-ajax');
    // Capturar el token del DOM
    const csrfToken = document.getElementById('csrf_token_js').value;

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!document.getElementById('horaSeleccionada').value) {
            mensajeDiv.innerHTML = `<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Por favor, selecciona una hora disponible.</p>`;
            return;
        }
        
        const boton = form.querySelector('button[type="submit"]');
        boton.disabled = true;
        boton.textContent = 'Agendando...';
        
        const formData = new FormData(form);
        
        // --- AÑADIR TOKEN CSRF AL FORMDATA DE AJAX ---
        formData.append('csrf_token', csrfToken);
        // --- FIN DE AÑADIR TOKEN ---

        fetch('agendar_cita.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mensajeDiv.innerHTML = `<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">${data.message}</p>`;
                form.reset();
                // (Resetear la cuadrícula de hora...)
                document.getElementById('time-slots-grid').innerHTML = '<p class="col-span-full text-gray-500 text-sm">Por favor, selecciona una fecha para ver los horarios.</p>';
                document.getElementById('horaSeleccionada').value = '';
            } else {
                mensajeDiv.innerHTML = `<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">${data.message}</p>`;
                if (document.getElementById('fechaCita').value) {
                    document.getElementById('fechaCita').dispatchEvent(new Event('change'));
                }
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