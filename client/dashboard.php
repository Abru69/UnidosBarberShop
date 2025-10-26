<?php
require_once '../config/database.php';
include '../includes/header.php'; // Usa el header normal

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<p class="text-2xl text-gray-800 mb-6">Bienvenido, aquí puedes agendar una nueva cita.</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <div class="md:col-span-1">
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold text-center mb-6 text-gray-800">Agendar Nueva Cita</h3>
            
            <div id="mensaje-ajax" class="mb-4"></div>
            
            <form id="form-agendar-cita" method="POST" class="space-y-4">
                <div>
                    <label for="servicio" class="block text-gray-700 text-sm font-bold mb-2">Selecciona un Servicio:</label>
                    <select name="servicio" id="servicio" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?> ($<?= number_format($servicio['precio'], 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="fechaCita" class="block text-gray-700 text-sm font-bold mb-2">Fecha:</label>
                    <input type="date" name="fecha" id="fechaCita" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="horaCita" class="block text-gray-700 text-sm font-bold mb-2">Hora:</label>
                    <input type="time" name="hora" id="horaCita" min="09:00" max="21:00" step="1800" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <small class="text-gray-600">Horario de atención: 9:00 AM a 9:00 PM</small>
                </div>
                <button type="submit" name="agendar" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                    Agendar Cita
                </button>
            </form>
        </div>
    </div>
    
    <div class="md:col-span-2">
         <div class="w-full max-w-3xl mx-auto text-center">
            <img src="../images/Corte_barber.png" alt="Corte de cabello" class="max-w-full h-auto inline-block rounded-lg shadow-xl">
         </div>
    </div>
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
            // Aplicar clases de Tailwind a los mensajes de respuesta
            let messageClass = data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            mensajeDiv.innerHTML = `<p class="p-4 rounded-md ${messageClass}">${data.message}</p>`;

            if (data.success) {
                form.reset();
                // Opcional: redirigir a 'Mis Citas' después de un éxito
                setTimeout(() => {
                    window.location.href = 'citas.php';
                }, 2000);
            }
        })
        .catch(error => {
            mensajeDiv.innerHTML = `<p class="p-4 rounded-md bg-red-100 text-red-700">Error de conexión. Inténtalo de nuevo.</p>`;
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