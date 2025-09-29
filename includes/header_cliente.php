<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuario - Unidos Barber Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <h1>
                <a href="../index.php" style="color:white; text-decoration:none;">Unidos Barber Shop</a>
            </h1>
            <div>
                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] === 'cliente'): ?>
                        <?php if (basename($_SERVER['PHP_SELF']) !== 'citas.php'): ?>
                        <a href="../client/citas.php">Mis Citas</a>
                    <?php endif; ?>
                    <a href="../client/dashboard.php">Agendar Nueva Cita</a>
                    <a href="../auth/logout.php">Cerrar Sesión</a>
                    
                <?php else: ?>
                    <a href="../auth/login.php">Iniciar Sesión</a>
                    <a href="../auth/registro.php">Registrarse</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
