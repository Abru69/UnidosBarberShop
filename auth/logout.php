<?php
session_start();
session_unset();
session_destroy();

// Redirige al login usando la ruta absoluta correcta
header('Location: login.php');
exit;
?>