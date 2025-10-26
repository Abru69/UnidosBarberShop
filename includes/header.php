<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidos Barber Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800 leading-normal">
    <header class="bg-gray-800 text-white p-4 border-b-4 border-blue-600 shadow-lg">
        <nav class="container mx-auto flex justify-between items-center px-4 md:px-0">
            <h1 class="text-2xl font-bold">
                <a href="../index.php" class="text-white no-underline hover:text-blue-300 transition-colors">Unidos Barber Shop</a>
            </h1>
            <div class="flex space-x-3">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_rol'] === 'barbero'): ?>
                        <a href="../barber/dashboard.php" class="px-3 py-2 rounded-md hover:bg-blue-600 transition-colors">Ver Citas</a>
                        <a href="../barber/gestionar_servicio.php" class="px-3 py-2 rounded-md hover:bg-blue-600 transition-colors">Servicios</a>
                        <a href="../barber/gestionar_horario.php" class="px-3 py-2 rounded-md hover:bg-blue-600 transition-colors">Disponibilidad</a>
                    <?php else: ?>
                        <a href="../client/citas.php" class="px-3 py-2 rounded-md hover:bg-blue-600 transition-colors">Mis Citas</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 transition-colors">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="px-3 py-2 rounded-md hover:bg-blue-600 transition-colors">Iniciar Sesión</a>
                    <a href="../auth/registro.php" class="px-3 py-2 rounded-md hover:bg-blue-600 transition-colors">Registrarse</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container mx-auto p-4 md:p-6">