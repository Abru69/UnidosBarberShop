<?php 
include 'includes/header.php'; 

// Lógica para el botón principal
$cta_link = 'auth/login.php';
if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] === 'cliente') {
    $cta_link = 'client/dashboard.php';
}

// Sobrescribir la clase <body> añadida en header.php para esta página específica
echo '<script>document.body.classList.add("loading");</script>';
?>

    <div id="loader-overlay" class="fixed top-0 left-0 w-full h-full bg-gray-800 flex flex-col justify-center items-center z-50 transition-opacity duration-800 ease-out">
        <img src="images/UBS.svg" alt="Cargando..." id="loader-logo" class="max-w-xs md:max-w-md opacity-0">
        <p class="text-white text-lg mt-4">Cargando</p>
    </div>
    
    <div id="main-content">
        <section class="hero bg-cover bg-center text-white min-h-[80vh] flex justify-center items-center text-center p-5" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1599351548092-93c691884047?q=80&w=2070&auto=format&fit=crop');">
            <div class="hero-text">
                <img src="images/UBS.svg" alt="Logo de Unidos Barber Shop" class="max-w-lg lg:max-w-2xl mx-auto mb-10">
                <h1 class="text-4xl md:text-5xl font-bold mb-5" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Estilo y Tradición en Cada Corte</h1>
                <p class="text-lg md:text-xl max-w-xl mx-auto mb-8">La experiencia de una barbería clásica con un toque moderno. Tu estilo es nuestra pasión.</p>
                <a href="<?= $cta_link ?>" class="bg-blue-600 text-white py-3 px-8 no-underline text-lg font-bold rounded-lg shadow-lg transition-all duration-300 ease-out hover:bg-blue-700 hover:-translate-y-1 transform">
                    Agendar Cita Ahora
                </a>
            </div>
        </section>

        <section class="services-preview py-16">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800">Nuestros Servicios</h2>
            <div class="service-cards flex flex-wrap justify-center gap-8 px-4">
                
                <div class="card bg-white rounded-lg shadow-xl w-full md:w-80 p-8 text-center transition-transform duration-300 ease-out hover:-translate-y-2">
                    <div class="text-5xl mb-4">✂️</div>
                    <h3 class="text-2xl font-bold mb-3">Corte de Cabello</h3>
                    <p>Desde los cortes más clásicos hasta las últimas tendencias, definimos tu estilo con precisión.</p>
                </div>
                
                <div class="card bg-white rounded-lg shadow-xl w-full md:w-80 p-8 text-center transition-transform duration-300 ease-out hover:-translate-y-2">
                    <div class="text-5xl mb-4">🧔</div>
                    <h3 class="text-2xl font-bold mb-3">Afeitado y Barba</h3>
                    <p>Un afeitado clásico con toalla caliente y navaja, o un diseño y mantenimiento de barba perfecto.</p>
                </div>
                
                <div class="card bg-white rounded-lg shadow-xl w-full md:w-80 p-8 text-center transition-transform duration-300 ease-out hover:-translate-y-2">
                    <div class="text-5xl mb-4">✨</div>
                    <h3 class="text-2xl font-bold mb-3">Tratamientos</h3>
                    <p>Relájate con nuestros tratamientos capilares y faciales para revitalizar tu piel y cabello.</p>
                </div>
            </div>
        </section>

        <section class="gallery-preview py-16 bg-white">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800">Nuestro Trabajo</h2>
            <div class="gallery-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 px-4">
                <img src="images/Corte_barber.png" alt="Corte de barbería" class="w-full h-full object-cover rounded-lg shadow-md">
                 </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

    <script>
        window.addEventListener('load', () => {
            const logo = '#loader-logo';

            // Crea una línea de tiempo para encadenar animaciones
            const timeline = anime.timeline({
                easing: 'easeOutExpo',
                duration: 800 // Duración base para la mayoría de los pasos
            });

            timeline
            // 1. Entrada: Aparece rebotando, escalando y girando ligeramente
            .add({
                targets: logo,
                opacity: [0, 1], // Inicia invisible, termina visible
                scale: [0.4, 1.1, 1], // Efecto de rebote (scale up, over, back to normal)
                rotate: ['-10deg', '0deg'],
                duration: 1200, // Un poco más de tiempo para la entrada
                easing: 'spring(1, 80, 10, 0)' // Easing con sensación de resorte/rebote
            })
            // 2. Animación media: Pulso sutil para mantener la atención
            .add({
                targets: logo,
                scale: [1, 1.05, 1], // Ligeramente más grande y de vuelta a 1
                rotate: ['0deg', '2deg', '0deg'], // Pequeño giro
                duration: 1000,
                easing: 'easeInOutSine'
            }, '-=200') // Inicia 200ms antes de que termine el paso 1 para un flujo más suave
            
            // 3. Salida: Se encoge rápidamente y activa el cierre del loader
            .add({
                targets: logo,
                scale: 0.8,
                opacity: 0,
                duration: 400,
                easing: 'easeInSine',
                complete: function() {
                    // Cierra el loader después de que el logo se ha encogido
                    document.body.classList.remove('loading');
                    
                    // Espera a que termine la transición de CSS (0.8s en tu style.css)
                    setTimeout(() => {
                        const loaderOverlay = document.getElementById('loader-overlay');
                        if (loaderOverlay) loaderOverlay.style.display = 'none';
                    }, 800);
                }
            }, '+=300'); // Espera 300ms después del pulso antes de iniciar la salida

            // --- Lógica para la animación de 'display: none' ---
            // Este CSS es necesario para que el contenido principal no aparezca
            const style = document.createElement('style');
            style.innerHTML = `
                .loading #main-content { display: none; }
                body:not(.loading) #loader-overlay { opacity: 0; pointer-events: none; }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>