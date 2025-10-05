document.addEventListener("DOMContentLoaded", () => {
    const loader = document.querySelector(".loader-overlay");

    // Simula que tarda un poco en cargar
    setTimeout(() => {
        loader.classList.add("hidden");
    }, 1000); // 1 segundo (puedes ajustar el tiempo)
});
