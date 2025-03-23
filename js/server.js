document.addEventListener("DOMContentLoaded", function () {
    // Initialize map centered on Granada
    var map = L.map('map').setView([37.1773, -3.5986], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Touch drag functionality for mobile
    const sidebar = document.querySelector('.sidebar');
    let startY = 0;
    let currentY = 0;
    let initialY = 0;
    let isDragging = false;
    let lastY = 0;
    let velocity = 0;
    let currentPosition = 'bottom'; // 'bottom', 'half', 'full'

    function handleTouchStart(e) {
        if (window.innerWidth > 576) return;
        
        startY = e.touches[0].clientY;
        initialY = sidebar.getBoundingClientRect().top;
        lastY = startY;
        isDragging = true;
        velocity = 0;

        // Prevent default scroll behavior when touching the drag handle
        if (e.target.closest('.sidebar::before')) {
            e.preventDefault();
        }
    }

    function handleTouchMove(e) {
        if (!isDragging) return;
        
        // Prevent default scroll behavior while dragging
        e.preventDefault();
        
        currentY = e.touches[0].clientY;
        const diff = currentY - startY;
        velocity = currentY - lastY;
        lastY = currentY;
        
        // Calculate the new position based on the drag
        const newY = initialY + diff;
        const maxY = window.innerHeight * 0.85 - 60; // 85vh - 60px
        const halfY = window.innerHeight * 0.425; // 42.5vh
        
        // Snap to the nearest position while dragging
        if (newY < halfY / 2) {
            sidebar.style.transform = `translateY(0)`;
        } else if (newY < halfY * 1.5) {
            sidebar.style.transform = `translateY(${halfY}px)`;
        } else {
            sidebar.style.transform = `translateY(${maxY}px)`;
        }
    }

    function handleTouchEnd(e) {
        if (!isDragging) return;
        isDragging = false;
        
        const currentTransform = getComputedStyle(sidebar).transform;
        const matrix = new DOMMatrix(currentTransform);
        const currentY = matrix.m42;
        const halfHeight = window.innerHeight * 0.425; // 42.5vh
        
        // Determinar la posición final basada en la velocidad y la posición actual
        if (velocity > 0) { // Movimiento hacia abajo
            if (currentPosition === 'full') {
                currentPosition = 'half';
                sidebar.classList.remove('expanded');
                sidebar.classList.add('half-expanded');
            } else if (currentPosition === 'half') {
                currentPosition = 'bottom';
                sidebar.classList.remove('half-expanded');
                sidebar.classList.add('collapsed');
            }
        } else if (velocity < 0) { // Movimiento hacia arriba
            if (currentPosition === 'bottom') {
                currentPosition = 'half';
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('half-expanded');
            } else if (currentPosition === 'half') {
                currentPosition = 'full';
                sidebar.classList.remove('half-expanded');
                sidebar.classList.add('expanded');
            }
        } else { // Sin velocidad, usar la posición actual
            if (currentY < halfHeight / 2) {
                currentPosition = 'full';
                sidebar.classList.remove('half-expanded');
                sidebar.classList.add('expanded');
            } else if (currentY < halfHeight * 1.5) {
                currentPosition = 'half';
                sidebar.classList.remove('expanded', 'collapsed');
                sidebar.classList.add('half-expanded');
            } else {
                currentPosition = 'bottom';
                sidebar.classList.remove('half-expanded');
                sidebar.classList.add('collapsed');
            }
        }
    }

    // Add touch event listeners with passive: false to allow preventDefault
    sidebar.addEventListener('touchstart', handleTouchStart, { passive: false });
    sidebar.addEventListener('touchmove', handleTouchMove, { passive: false });
    sidebar.addEventListener('touchend', handleTouchEnd);

    // Función para manejar los filtros
    function handleFilters() {
        const categoryButtons = document.querySelectorAll('.category-btn');
        const markers = document.querySelectorAll('.marker');

        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                const category = button.dataset.category;
                
                // Toggle active class para el botón actual
                button.classList.toggle('active');
                
                // Mostrar/ocultar marcadores según la categoría
                markers.forEach(marker => {
                    if (marker.dataset.category === category) {
                        marker.style.display = button.classList.contains('active') ? 'block' : 'none';
                    }
                });
            });
        });
    }

    // Inicializar filtros
    handleFilters();


    document.getElementById('toggleView').addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth <= 576) {
            sidebar.classList.toggle('collapsed');
        }
    });
});
