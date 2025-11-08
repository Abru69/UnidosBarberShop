<?php
require_once '../config/database.php';
session_start(); // Necesario para leer $_SESSION['csrf_token']

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido.'];

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    $response['message'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- INICIO DE VALIDACIÓN CSRF ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $response['message'] = 'Error de validación de seguridad.';
        echo json_encode($response);
        exit;
    }
    // --- FIN VALIDACIÓN CSRF ---

    $id_cliente = $_SESSION['usuario_id'];
    $id_servicio = $_POST['servicio'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $fecha_hora = $fecha . ' ' . $hora;

    // (Lógica de verificación de doble reserva...)
    $stmt_check_citas = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha_hora = ? AND estado IN ('confirmada', 'pendiente')");
    $stmt_check_citas->execute([$fecha_hora]);
    $citas_existentes = $stmt_check_citas->fetchColumn();

    $stmt_check_bloqueo = $pdo->prepare("SELECT COUNT(*) FROM horarios_bloqueados WHERE ? >= fecha_inicio AND ? < fecha_fin");
    $stmt_check_bloqueo->execute([$fecha_hora, $fecha_hora]);
    $esta_bloqueado = $stmt_check_bloqueo->fetchColumn();

    if ($citas_existentes > 0 || $esta_bloqueado > 0) {
        $response['message'] = 'Lo sentimos, esa hora ya no está disponible. Por favor, elige otra.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO citas (id_cliente, id_servicio, fecha_hora, estado) VALUES (?, ?, ?, 'pendiente')");
        if ($stmt->execute([$id_cliente, $id_servicio, $fecha_hora])) {
            $response['success'] = true;
            $response['message'] = '¡Cita agendada con éxito! Espera la confirmación del barbero.';
        } else {
            $response['message'] = 'Error al agendar la cita en la base de datos.';
        }
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>