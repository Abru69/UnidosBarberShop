<?php
// barber/eliminar_servicio.php
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

if (isset($_GET['id'])) {
    $id_servicio = $_GET['id'];
    // Implementar Soft Delete (establecer activo = 0)
    $stmt = $pdo->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
    $stmt->execute([$id_servicio]);
}

header('Location: gestionar_servicio.php');
exit;
?>