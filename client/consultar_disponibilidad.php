<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');
$response = ['success' => false, 'horas' => []];

// 1. Seguridad: Verificar sesión del cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    $response['message'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit;
}

// 2. Validar que la fecha fue enviada
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
    $duracion_servicio = 30; // Duración de 30 min (basado en step="1800")
    $hora_inicio_dia = strtotime($fecha . ' 09:00:00'); //
    $hora_fin_dia = strtotime($fecha . ' 21:00:00'); //
    $horas_disponibles = [];

    try {
        // 3. Obtener citas ya ocupadas (confirmadas o pendientes)
        $stmt_citas = $pdo->prepare("SELECT fecha_hora FROM citas WHERE DATE(fecha_hora) = ? AND estado IN ('confirmada', 'pendiente')");
        $stmt_citas->execute([$fecha]);
        $citas_ocupadas_raw = $stmt_citas->fetchAll(PDO::FETCH_COLUMN);
        // Convertir a un formato rápido de búsqueda
        $citas_ocupadas = [];
        foreach ($citas_ocupadas_raw as $cita_time) {
            $citas_ocupadas[strtotime($cita_time)] = true;
        }

        // 4. Obtener horarios bloqueados por el barbero
        $stmt_bloqueos = $pdo->prepare("SELECT fecha_inicio, fecha_fin FROM horarios_bloqueados WHERE ? BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)");
        $stmt_bloqueos->execute([$fecha]);
        $bloqueos = $stmt_bloqueos->fetchAll(PDO::FETCH_ASSOC);

        // 5. Iterar cada slot de 30 minutos en el día
        for ($timestamp = $hora_inicio_dia; $timestamp < $hora_fin_dia; $timestamp += $duracion_servicio * 60) {
            
            $esta_ocupado = false;

            // 5A. Verificar si el slot actual (timestamp) ya está en las citas
            if (isset($citas_ocupadas[$timestamp])) {
                $esta_ocupado = true;
            }

            // 5B. Verificar si el slot cae dentro de un bloqueo
            if (!$esta_ocupado) {
                foreach ($bloqueos as $bloqueo) {
                    $inicio_bloqueo = strtotime($bloqueo['fecha_inicio']);
                    $fin_bloqueo = strtotime($bloqueo['fecha_fin']);
                    
                    if ($timestamp >= $inicio_bloqueo && $timestamp < $fin_bloqueo) {
                        $esta_ocupado = true;
                        break; // Salir del bucle de bloqueos
                    }
                }
            }
            
            // 5C. Verificar que el slot no sea en el pasado (para el día de hoy)
            if ($timestamp < time()) {
                 $esta_ocupado = true;
            }

            // 6. Si el slot está libre, añadirlo
            if (!$esta_ocupado) {
                $horas_disponibles[] = date('H:i', $timestamp);
            }
        }

        $response['success'] = true;
        $response['horas'] = $horas_disponibles;

    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Solicitud no válida.';
}

echo json_encode($response);
?>