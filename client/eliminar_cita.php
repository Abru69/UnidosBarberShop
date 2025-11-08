<?php
require_once '../config/database.php';
session_start();

// --- INICIO DE VALIDACIÓN CSRF ---
if (!isset($_GET['token']) || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
    die('Error de validación de seguridad (CSRF).');
}
// --- FIN DE VALIDACIÓN CSRF ---

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$id_cita = $_GET['id'];

// (El código original permitía borrar solo citas pasadas, lo cual es correcto)
$stmt = $pdo->prepare("DELETE FROM citas WHERE id = ? AND id_cliente = ? AND fecha_hora < NOW()");
$stmt->execute([$id_cita, $id_cliente]);

header('Location: citas.php');
exit;
?>