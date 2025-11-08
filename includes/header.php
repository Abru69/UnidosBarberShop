<?php 
session_start(); 

// --- INICIO DE PROTECCIÓN CSRF (Riesgo 2) ---
// Generar un token CSRF si no existe uno en la sesión
if (empty($_SESSION['csrf_token'])) {
    // Usar random_bytes para una alta entropía
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
    <script>
        // Configuración para usar la clase 'primary' con el color #0779e4
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
    <header class="bg-gray-800 text-white border-b-4 border-primary shadow-lg">
        <nav class="max-w-7xl mx-auto px-4 py-3 flex flex-col md:flex-row justify-between items-center">
            
            <h1 class="text-2xl font-bold mb-2 md:mb-0">
                <a href="../index.php" class="text-white no-underline hover:text-primary transition-colors">Unidos Barber Shop</a>
            </h1>
            
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 text-sm font-medium mt-2 md:mt-0">
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
        </nav>
    </header>
    
    <main class="max-w-7xl mx-auto px-4 py-8">