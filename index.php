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
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        /* --- ESTILOS PARA LA ANIMACIÓN (AÑADIDOS) --- */
        #loader-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease-out;
        }

        #loader-logo {
            max-width: 250px;
            opacity: 0; /* Inicia invisible para la animación */
        }
        
        .loading #main-content {
            display: none;
        }
        
        body:not(.loading) #loader-overlay {
            opacity: 0;
            pointer-events: none;
        }
        
        /* --- TUS ESTILOS ORIGINALES (INTACTOS) --- */
        .hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1599351548092-93c691884047?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            color: #fff;
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-text p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px auto;
        }

        .cta-button {
            background-color: #0779e4;
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .cta-button:hover {
            background-color: #0a65b8;
            transform: translateY(-3px);
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-top: 40px;
            margin-bottom: 30px;
            color: #333;
        }

        .services-preview {
            padding: 40px 0;
        }

        .service-cards {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 300px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .gallery-preview {
            padding: 40px 0;
            background-color: #fff;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .gallery-grid img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .hero-logo {
            max-width: 850px; 
            margin-bottom: 40px;
        }
    </style>
</head>
<body class="loading">

    <div id="loader-overlay">
        <img src="images/UBS_LOGO.png" alt="Cargando..." id="loader-logo">
    </div>
    
    <div id="main-content">
        <section class="hero">
            <div class="hero-text">
                <img src="images/UBS_LOGO.png" alt="Logo de Unidos Barber Shop" class="hero-logo">
                <h1>Estilo y Tradición en Cada Corte</h1>
                <p>La experiencia de una barbería clásica con un toque moderno. Tu estilo es nuestra pasión.</p>
                <a href="<?= $cta_link ?>" class="cta-button">Agendar Cita Ahora</a>
            </div>
        </section>

        <section class="services-preview">
            <h2 class="section-title">Nuestros Servicios</h2>
            <div class="service-cards">
                <div class="card">
                    <div class="card-icon">✂️</div>
                    <h3>Corte de Cabello</h3>
                    <p>Desde los cortes más clásicos hasta las últimas tendencias, definimos tu estilo con precisión.</p>
                </div>
                <div class="card">
                    <div class="card-icon">🧔</div>
                    <h3>Afeitado y Barba</h3>
                    <p>Un afeitado clásico con toalla caliente y navaja, o un diseño y mantenimiento de barba perfecto.</p>
                </div>
                <div class="card">
                    <div class="card-icon">✨</div>
                    <h3>Tratamientos</h3>
                    <p>Relájate con nuestros tratamientos capilares y faciales para revitalizar tu piel y cabello.</p>
                </div>
            </div>
        </section>

        <section class="gallery-preview">
            <h2 class="section-title">Nuestro Trabajo</h2>
            <div class="gallery-grid">
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

    <script>
        window.addEventListener('load', () => {
            anime({
                targets: '#loader-logo',
                opacity: [0, 1],
                translateY: [25, 0],
                duration: 1500,
                easing: 'easeOutExpo',
                complete: function() {
                    setTimeout(() => {
                        document.body.classList.remove('loading');
                        setTimeout(() => {
                            const loaderOverlay = document.getElementById('loader-overlay');
                            if (loaderOverlay) loaderOverlay.style.display = 'none';
                        }, 800);
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>