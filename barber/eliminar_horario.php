<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'barbero') {
    header('Location: ../auth/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_bloqueo = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM horarios_bloqueados WHERE id = ?");
    $stmt->execute([$id_bloqueo]);
}

header('Location: gestionar_horario.php');
exit;
?>