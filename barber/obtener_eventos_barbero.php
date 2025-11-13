<?php
require_once '../config/database.php';
session_start();

// Establecer la cabecera como JSON
header('Content-Type: application/json');
$eventos = [];

// --- 1. Seguridad ---
// Solo el barbero puede ver todas las citas
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    echo json_encode($eventos); // Devuelve un array vacío si no está autorizado
    exit;
}

try {
    // --- 2. Consulta a la Base de Datos ---
    // Obtener todas las citas (confirmadas y pendientes) y unir con usuarios y servicios
    $citas_stmt = $pdo->prepare(
        "SELECT 
            c.fecha_hora, 
            c.estado, 
            s.nombre AS servicio_nombre,
            u.nombre AS cliente_nombre 
         FROM citas c 
         JOIN servicios s ON c.id_servicio = s.id 
         JOIN usuarios u ON c.id_cliente = u.id
         WHERE c.estado IN ('confirmada', 'pendiente')"
    );
    $citas_stmt->execute();
    $citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 3. Formatear para FullCalendar ---
    foreach ($citas as $cita) {
        
        // Asignar colores según el estado
        $color = '';
        $textColor = 'black';
        
        if ($cita['estado'] === 'confirmada') {
            $color = '#10B981'; // Verde (Tailwind emerald-500)
            $textColor = 'white';
        } else { // 'pendiente'
            $color = '#F59E0B'; // Amarillo (Tailwind amber-500)
        }

        $eventos[] = [
            // El título del evento mostrará: "Nombre Cliente - Nombre Servicio"
            'title' => $cita['cliente_nombre'] . ' - ' . $cita['servicio_nombre'], 
            'start' => $cita['fecha_hora'],     // Fecha y hora de inicio
            'backgroundColor' => $color,         // Color de fondo
            'borderColor' => $color,           // Color del borde
            'textColor' => $textColor          // Color del texto
        ];
    }

} catch (PDOException $e) {
    // Manejar error de DB (opcional, pero recomendado)
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Devolver el array de eventos como JSON
echo json_encode($eventos);
?>