<?php
require_once '../config/database.php';
// Usamos el header_cliente para este dashboard
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
// Criterio RF4: Solo mostrar servicios ACTIVOS
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
?>
<p class="text-lg text-gray-700 mb-6">Bienvenido <?= htmlspecialchars($usuario['nombre']) ?>, aquí puedes agendar una nueva cita.</p>

<div class="bg-white p-8 mt-6 mx-auto max-w-lg rounded-lg shadow-xl border border-gray-200">
    <h3 class="text-2xl font-bold text-center mb-6">Agendar Nueva Cita</h3>
    
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
            <input type="hidden" name="hora" id="horaSeleccionada" required>
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
    
    // --- Lógica de Fecha Mínima ---
    const inputFecha = document.getElementById('fechaCita');
    const hoy = new Date();
    const anio = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    const fechaMinima = `${anio}-${mes}-${dia}`;
    inputFecha.setAttribute('min', fechaMinima);

    // --- LÓGICA DE HORARIOS DISPONIBLES (NUEVA UX) ---
    const gridHorarios = document.getElementById('time-slots-grid');
    const inputHoraSeleccionada = document.getElementById('horaSeleccionada');

    inputFecha.addEventListener('change', function() {
        const fechaSeleccionada = inputFecha.value;
        
        // Resetear
        gridHorarios.innerHTML = '<p class="col-span-full text-gray-500 text-sm">Cargando...</p>';
        inputHoraSeleccionada.value = ''; // Limpiar la hora seleccionada

        if (!fechaSeleccionada || fechaSeleccionada < fechaMinima) {
            gridHorarios.innerHTML = '<p class="col-span-full text-red-500 text-sm">Selecciona una fecha válida.</p>';
            return;
        }

        // Llamada AJAX (Fetch) al endpoint
        fetch(`consultar_disponibilidad.php?fecha=${fechaSeleccionada}`)
        .then(response => response.json())
        .then(data => {
            gridHorarios.innerHTML = ''; // Limpiar el "Cargando..."
            
            if (data.success && data.horas.length > 0) {
                data.horas.forEach(hora => {
                    const button = document.createElement('button');
                    button.type = 'button'; // Importante: para que no envíe el formulario
                    button.dataset.hora = hora;
                    button.textContent = hora;
                    // Estilos de Tailwind para los botones de hora
                    button.className = 'py-2 px-3 border border-gray-300 rounded-lg text-center font-medium text-sm text-gray-700 hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors';
                    
                    button.addEventListener('click', function() {
                        // Guardar el valor en el input oculto
                        inputHoraSeleccionada.value = this.dataset.hora;
                        
                        // Resaltar el botón seleccionado
                        // 1. Quitar resaltado a todos
                        gridHorarios.querySelectorAll('button').forEach(btn => {
                            btn.classList.remove('bg-primary', 'text-white', 'border-primary');
                            btn.classList.add('text-gray-700', 'border-gray-300');
                        });
                        // 2. Añadir resaltado al clickeado
                        this.classList.add('bg-primary', 'text-white', 'border-primary');
                        this.classList.remove('text-gray-700', 'border-gray-300');
                    });
                    
                    gridHorarios.appendChild(button);
                });
            } else {
                gridHorarios.innerHTML = '<p class="col-span-full text-gray-500 text-sm">No hay horas disponibles para este día.</p>';
            }
        })
        .catch(error => {
            console.error('Error al cargar horarios:', error);
            gridHorarios.innerHTML = '<p class="col-span-full text-red-500 text-sm">Error al cargar horarios. Intente de nuevo.</p>';
        });
    });

    // --- Lógica de Envío del Formulario (Agendar Cita) ---
    const form = document.getElementById('form-agendar-cita');
    const mensajeDiv = document.getElementById('mensaje-ajax');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Validación: Asegurar que se seleccionó una hora
        if (!inputHoraSeleccionada.value) {
            mensajeDiv.innerHTML = `<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">Por favor, selecciona una hora disponible.</p>`;
            return;
        }
        
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
            if (data.success) {
                mensajeDiv.innerHTML = `<p class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">${data.message}</p>`;
                form.reset();
                // Resetear la cuadrícula de hora
                gridHorarios.innerHTML = '<p class="col-span-full text-gray-500 text-sm">Por favor, selecciona una fecha para ver los horarios.</p>';
                inputHoraSeleccionada.value = '';
            } else {
                mensajeDiv.innerHTML = `<p class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">${data.message}</p>`;
                // Si la hora falló (ej. alguien la reservó justo ahora), recargamos los horarios
                if (inputFecha.value) {
                    inputFecha.dispatchEvent(new Event('change')); // Dispara el evento 'change'
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