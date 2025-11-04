<?php
// barber/eliminar_servicio.php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_servicio = $_GET['id'];
    // CAMBIO CLAVE: Implementar Soft Delete (establecer activo = 0) en lugar de DELETE.
    // Criterio de Éxito RF4: NO elimina citas asociadas, solo oculta el servicio.
    $stmt = $pdo->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
    $stmt->execute([$id_servicio]);
}

header('Location: gestionar_servicio.php');
exit;
?>