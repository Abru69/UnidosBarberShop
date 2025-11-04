<?php 
include 'includes/header.php'; 

// Lógica para el botón principal
$cta_link = 'auth/login.php';
if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] === 'cliente') {
    $cta_link = 'client/dashboard.php';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidos Barber Shop</title>
    <style>
        /* --- ESTILOS PARA LA ANIMACIÓN (PRESERVADOS) --- */
        #loader-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #1f2937; /* Fondo oscuro */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease-out;
        }

        #loader-logo {
            max-width: 500px;
            opacity: 0;
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5));
        }

        .loading #main-content {
            display: none;
        }

        body:not(.loading) #loader-overlay {
            opacity: 0;
            pointer-events: none;
        }
        
        /* Estilo para el fondo HERO (Se debe mantener aquí o en un CSS externo) */
        .hero-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1599351548092-93c691884047?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="loading">

    <div id="loader-overlay">
        <img src="images/UBS.svg" alt="Cargando..." id="loader-logo" class="max-w-xs md:max-w-lg">
        <p class="text-white mt-4">Cargando</p>
    </div>
    
    <div id="main-content">
        <section class="hero-bg text-white min-h-screen flex justify-center items-center text-center p-4">
            <div class="hero-text max-w-4xl px-4">
                <img src="images/UBS.svg" alt="Logo de Unidos Barber Shop" class="hero-logo w-full max-w-2xl mx-auto mb-8 filter drop-shadow-lg">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-shadow-lg">Estilo y Tradición en Cada Corte</h1>
                <p class="text-lg mb-8 max-w-xl mx-auto">La experiencia de una barbería clásica con un toque moderno. Tu estilo es nuestra pasión.</p>
                
                <a href="<?= $cta_link ?>" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-bold text-lg hover:bg-primary-dark transition duration-300 transform hover:scale-105 shadow-xl">
                    Agendar Cita Ahora
                </a>
            </div>
        </section>

        <section class="py-12 md:py-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-10 text-gray-800">Nuestros Servicios</h2>
            
            <div class="flex flex-wrap justify-center gap-6">
                <div class="bg-white rounded-xl shadow-lg w-full md:w-80 p-8 text-center transition duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <div class="text-4xl mb-4">✂️</div>
                    <h3 class="text-xl font-bold mb-2">Corte de Cabello</h3>
                    <p class="text-gray-600">Desde los cortes más clásicos hasta las últimas tendencias, definimos tu estilo con precisión.</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg w-full md:w-80 p-8 text-center transition duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <div class="text-4xl mb-4">🧔</div>
                    <h3 class="text-xl font-bold mb-2">Afeitado y Barba</h3>
                    <p class="text-gray-600">Un afeitado clásico con toalla caliente y navaja, o un diseño y mantenimiento de barba perfecto.</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg w-full md:w-80 p-8 text-center transition duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <div class="text-4xl mb-4">✨</div>
                    <h3 class="text-xl font-bold mb-2">Tratamientos</h3>
                    <p class="text-gray-600">Relájate con nuestros tratamientos capilares y faciales para revitalizar tu piel y cabello.</p>
                </div>
            </div>
        </section>

        <section class="py-12 md:py-16 bg-white">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-10 text-gray-800">Nuestro Trabajo</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

    <script>
        // Lógica de animación preservada (requiere animejs)
        window.addEventListener('load', () => {
            const logo = '#loader-logo';
            const timeline = anime.timeline({
                easing: 'easeOutExpo',
                duration: 800
            });

            timeline
            .add({
                targets: logo,
                opacity: [0, 1],
                scale: [0.4, 1.1, 1],
                rotate: ['-10deg', '0deg'],
                duration: 1200,
                easing: 'spring(1, 80, 10, 0)'
            })
            .add({
                targets: logo,
                scale: [1, 1.05, 1],
                rotate: ['0deg', '2deg', '0deg'],
                duration: 1000,
                easing: 'easeInOutSine'
            }, '-=200')
            
            .add({
                targets: logo,
                scale: 0.8,
                opacity: 0,
                duration: 400,
                easing: 'easeInSine',
                complete: function() {
                    document.body.classList.remove('loading');
                    setTimeout(() => {
                        const loaderOverlay = document.getElementById('loader-overlay');
                        if (loaderOverlay) loaderOverlay.style.display = 'none';
                    }, 800);
                }
            }, '+=300');
        });
    </script>
</body>
</html>