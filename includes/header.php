<?php 
session_start(); 

// --- INICIO DE PROTECCIÓN CSRF  ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
// --- FIN DE PROTECCIÓN CSRF ---

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidos Barber Shop</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    // Configuración personalizada de Tailwind
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#0779e4', 
                        'primary-dark': '#0a65b8',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 leading-relaxed">
    
    <header class="bg-gray-800 text-white border-b-4 border-primary shadow-lg sticky top-0 z-50 w-full">
        
        <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center relative">
            
            <h1 class="text-2xl font-bold">
                <a href="../index.php" class="text-white no-underline hover:text-primary transition-colors">Unidos Barber Shop</a>
            </h1>

            <button id="hamburger-button" class_exists="" class="md:hidden p-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" aria-label="Abrir menú" aria-expanded="false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
            
            <div id="desktop-menu" class="hidden md:flex md:flex-row md:space-x-4 text-sm font-medium md:w-auto">
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_rol'] === 'barbero'): ?>
                        <a href="../barber/dashboard.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Ver Citas</a>
                        <a href="../barber/gestionar_servicio.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Servicios</a>
                        <a href="../barber/gestionar_horario.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Disponibilidad</a>
                        <a href="../barber/perfil.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Mi Perfil</a>
                    <?php else: ?>
                        <a href="../client/citas.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Mis Citas</a>
                        <a href="../client/dashboard.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Agendar Cita</a>
                        <a href="../client/perfil.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Mi Perfil</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php?token=<?= $csrf_token ?>" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded transition-colors">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="hover:bg-primary px-3 py-1 rounded transition-colors">Iniciar Sesión</a>
                    <a href="../auth/registro.php" class="bg-primary hover:bg-primary-dark px-3 py-1 rounded transition-colors">Registrarse</a>
                <?php endif; ?>
            </div>

            <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 right-0 bg-gray-800 shadow-lg p-4 space-y-2 flex flex-col">
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_rol'] === 'barbero'): ?>
                        <a href="../barber/dashboard.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Ver Citas</a>
                        <a href="../barber/gestionar_servicio.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Servicios</a>
                        <a href="../barber/gestionar_horario.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Disponibilidad</a>
                        <a href="../barber/perfil.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Mi Perfil</a>
                    <?php else: ?>
                        <a href="../client/citas.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Mis Citas</a>
                        <a href="../client/dashboard.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Agendar Cita</a>
                        <a href="../client/perfil.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Mi Perfil</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php?token=<?= $csrf_token ?>" class="block bg-red-500 hover:bg-red-600 px-3 py-2 rounded transition-colors text-center">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="block hover:bg-primary px-3 py-2 rounded transition-colors">Iniciar Sesión</a>
                    <a href="../auth/registro.php" class="block bg-primary hover:bg-primary-dark px-3 py-2 rounded transition-colors text-center">Registrarse</a>
                <?php endif; ?>
            </div>
            
        </nav>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburgerButton = document.getElementById('hamburger-button');
            const mobileMenu = document.getElementById('mobile-menu');

            hamburgerButton.addEventListener('click', function() {
                // Alterna la clase 'hidden' de Tailwind
                mobileMenu.classList.toggle('hidden');
                
                // (Opcional: para accesibilidad, indica si el menú está expandido)
                const isExpanded = mobileMenu.classList.contains('hidden') ? 'false' : 'true';
                hamburgerButton.setAttribute('aria-expanded', isExpanded);
            });
        });
    </script>
    
    <main class="max-w-7xl mx-auto px-4 py-8 pt-16">