<?php
require_once '../config/database.php';
session_start();

// 1. Se asegura de que el usuario haya iniciado sesión y sea un cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit;
}

// 2. Comprueba que se haya enviado el ID de la cita
if (isset($_GET['id'])) {
    $id_cita = $_GET['id'];
    $id_cliente = $_SESSION['usuario_id'];

    // 3. Prepara la consulta para borrar la cita
    // IMPORTANTE: La condición "AND id_cliente = ?" asegura que un cliente
    // solo pueda borrar SUS PROPIAS citas, y no las de otros.
    $stmt = $pdo->prepare("DELETE FROM citas WHERE id = ? AND id_cliente = ?");
    $stmt->execute([$id_cita, $id_cliente]);
}

// 4. Redirige al usuario de vuelta a su panel
header('Location: dashboard.php');
exit;
?>