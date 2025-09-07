<<?php
// barber/eliminar_servicio.php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_servicio = $_GET['id'];
    // Este comando DELETE ahora activará el borrado en cascada en la tabla de citas
    $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->execute([$id_servicio]);
}

header('Location: gestionar_servicio.php');
exit;
?>