<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido.'];

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    $response['message'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_SESSION['usuario_id'];
    $id_servicio = $_POST['servicio'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $fecha_hora = $fecha . ' ' . $hora;

    $stmt_check_citas = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha_hora = ? AND estado IN ('confirmada', 'pendiente')");
    $stmt_check_citas->execute([$fecha_hora]);
    $citas_existentes = $stmt_check_citas->fetchColumn();

    $stmt_check_bloqueo = $pdo->prepare("SELECT COUNT(*) FROM horarios_bloqueados WHERE ? >= fecha_inicio AND ? < fecha_fin");
    $stmt_check_bloqueo->execute([$fecha_hora, $fecha_hora]);
    $esta_bloqueado = $stmt_check_bloqueo->fetchColumn();

    // ***** VERIFICACIÓN DE DOBLE RESERVA *****
    $stmt_check_citas = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha_hora = ? AND estado IN ('confirmada', 'pendiente')");
    $stmt_check_citas->execute([$fecha_hora]);
    $citas_existentes = $stmt_check_citas->fetchColumn();

    $stmt_check_bloqueo = $pdo->prepare("SELECT COUNT(*) FROM horarios_bloqueados WHERE ? >= fecha_inicio AND ? < fecha_fin");
    $stmt_check_bloqueo->execute([$fecha_hora, $fecha_hora]);
    $esta_bloqueado = $stmt_check_bloqueo->fetchColumn();

    // Si la cita ya existe (porque el Cliente A la tomó) O está bloqueada
    if ($citas_existentes > 0 || $esta_bloqueado > 0) {
        // El Cliente B recibirá este error
        $response['message'] = 'Lo sentimos, esa hora ya no está disponible. Por favor, elige otra.';
    } else {
        // Solo si está libre, el Cliente B puede insertarla
        $stmt = $pdo->prepare("INSERT INTO citas (id_cliente, id_servicio, fecha_hora, estado) VALUES (?, ?, ?, 'pendiente')");
        // ... (lógica de inserción) ...
    }

    if ($citas_existentes > 0 || $esta_bloqueado > 0) {
        $response['message'] = 'Lo sentimos, esa hora ya no está disponible. Por favor, elige otra.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO citas (id_cliente, id_servicio, fecha_hora, estado) VALUES (?, ?, ?, 'pendiente')");
        if ($stmt->execute([$id_cliente, $id_servicio, $fecha_hora])) {
            $response['success'] = true;
            $response['message'] = '¡Cita agendada con éxito! Espera la confirmación del barbero.';

            // CAMBIO: Obtener los detalles de la cita recién creada para devolverlos
            $last_id = $pdo->lastInsertId();
            $stmt_new_cita = $pdo->prepare(
                "SELECT c.fecha_hora, c.estado, s.nombre AS servicio_nombre, s.precio
                 FROM citas c
                 JOIN servicios s ON c.id_servicio = s.id
                 WHERE c.id = ?"
            );
            $stmt_new_cita->execute([$last_id]);
            $new_cita = $stmt_new_cita->fetch(PDO::FETCH_ASSOC);
            
            if ($new_cita) {
                // Formateamos los datos para que sean fáciles de usar en JavaScript
                $response['cita'] = [
                    'fecha_formateada' => date('d/m/Y h:i A', strtotime($new_cita['fecha_hora'])),
                    'servicio_nombre' => htmlspecialchars($new_cita['servicio_nombre']),
                    'precio_formateado' => '$' . number_format($new_cita['precio'], 2),
                    'estado_formateado' => ucfirst($new_cita['estado'])
                ];
            }
        } else {
            $response['message'] = 'Error al agendar la cita en la base de datos.';
        }
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>