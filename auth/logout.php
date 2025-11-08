<?php
session_start();

// --- INICIO DE VALIDACIÓN CSRF ---
// Validar que el token de la URL coincida con el de la sesión
if (!isset($_GET['token']) || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
    die('Error de validación de seguridad (CSRF).');
}
// --- FIN DE VALIDACIÓN CSRF ---

session_unset();
session_destroy();
header('Location: ../index.php');
exit;
?>