<?php 
include 'includes/header.php'; 

// Lógica para el botón principal: si el usuario ya inició sesión como cliente,
// lo lleva a su panel; si no, lo lleva a la página de login.
$cta_link = 'auth/login.php';
if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] === 'cliente') {
    $cta_link = 'client/dashboard.php';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>

/* Sección Hero (Principal) */
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

/* Títulos de Sección */
.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-top: 40px;
    margin-bottom: 30px;
    color: #333;
}

/* Tarjetas de Servicios */
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

/* Galería de Imágenes */
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
</style>
<body>
    <section class="hero">
        <div class="hero-text">
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

    <?php 
include 'includes/footer.php'; 
?>
</body>

</html>