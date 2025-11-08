<?php
require_once '../config/database.php';
session_start();

// --- INICIO DE VALIDACIÓN CSRF ---
if (!isset($_GET['token']) || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
    die('Error de validación de seguridad (CSRF).');
}
// --- FIN DE VALIDACIÓN CSRF ---

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id_cita = $_GET['id'];
    $accion = $_GET['accion'];
    
    if ($accion === 'confirmada' || $accion === 'cancelada') {
        $stmt = $pdo->prepare("UPDATE citas SET estado = ? WHERE id = ?");
        $stmt->execute([$accion, $id_cita]);
    }
}

header('Location: dashboard.php');
exit;
?>