<?php
require_once '../config/database.php';
session_start();

// Establecer la cabecera como JSON
header('Content-Type: application/json');
$eventos = [];

// Seguridad: Asegurarse de que el usuario sea un cliente logueado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    echo json_encode($eventos); // Devuelve un array vacío si no está autorizado
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

try {
    // Consultar solo citas que no estén canceladas
    $citas_stmt = $pdo->prepare(
        "SELECT 
            c.fecha_hora, 
            c.estado, 
            s.nombre AS servicio_nombre
         FROM citas c 
         JOIN servicios s ON c.id_servicio = s.id 
         WHERE c.id_cliente = ? AND c.estado IN ('confirmada', 'pendiente')"
    );
    $citas_stmt->execute([$id_cliente]);
    $citas = $citas_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear los datos para FullCalendar
    foreach ($citas as $cita) {
        
        // Asignar colores según el estado de la cita
        $color = '';
        $textColor = 'black'; // Texto negro por defecto
        
        if ($cita['estado'] === 'confirmada') {
            // Verde (basado en los colores de Tailwind)
            $color = '#10B981'; // emerald-500
            $textColor = 'white';
        } else {
            // Pendiente (Amarillo)
            $color = '#F59E0B'; // amber-500
        }

        $eventos[] = [
            'title' => $cita['servicio_nombre'], // Título del evento
            'start' => $cita['fecha_hora'],     // Fecha y hora de inicio
            'backgroundColor' => $color,         // Color de fondo del evento
            'borderColor' => $color,           // Color del borde
            'textColor' => $textColor          // Color del texto
        ];
    }

} catch (PDOException $e) {
    // Manejar error de DB
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Devolver el array de eventos como JSON
echo json_encode($eventos);
?>