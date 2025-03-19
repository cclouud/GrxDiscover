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

    // Sample sites data
    const sites = [
        {
            id: 1,
            name: "Cafetería Alhambra",
            location: [37.1773, -3.5986],
            type: "restaurant",
            rating: 4.8,
            address: "Granada, España",
            description: "Especialidad en café y repostería artesanal",
            hours: "Abierto de 8:00 AM a 10:00 PM",
            images: ["img/restaurante.jpg", "img/restaurante2.jpg", "img/restaurante.jpg"],
            reviews: [
                {
                    user: "Juan Pérez",
                    rating: 5,
                    date: "12 Feb 2025",
                    text: "El mejor café de Granada ☕. Excelente atención y repostería deliciosa.",
                    avatar: "https://via.placeholder.com/40"
                },
                {
                    user: "María López",
                    rating: 4,
                    date: "10 Feb 2025",
                    text: "Buena calidad, pero algo caro. Aun así, lo recomiendo!",
                    avatar: "https://via.placeholder.com/40"
                }
            ]
        },
        {
            id: 2,
            name: "La Alhambra",
            location: [37.1760, -3.5880],
            type: "monument",
            rating: 4.9,
            address: "Calle Real de la Alhambra, Granada",
            description: "Palacio y fortaleza medieval, Patrimonio de la Humanidad",
            hours: "Abierto de 8:30 AM a 6:00 PM",
            images: ["img/alhambra1.jpg", "img/alhambra2.jpg", "img/alhambra3.jpg"],
            reviews: [
                {
                    user: "Carlos Ruiz",
                    rating: 5,
                    date: "15 Feb 2025",
                    text: "Una experiencia inolvidable. La arquitectura es impresionante.",
                    avatar: "https://via.placeholder.com/40"
                },
                {
                    user: "Ana García",
                    rating: 5,
                    date: "13 Feb 2025",
                    text: "Merece la pena visitar cada rincón. Los jardines son espectaculares.",
                    avatar: "https://via.placeholder.com/40"
                }
            ]
        },
        {
            id: 3,
            name: "Mirador de San Nicolás",
            location: [37.1817, -3.5927],
            type: "viewpoint",
            rating: 4.7,
            address: "Calle Mirador de San Nicolás, Granada",
            description: "Vista panorámica de la Alhambra y Sierra Nevada",
            hours: "Abierto 24 horas",
            images: ["img/mirador1.jpg", "img/mirador2.jpg", "img/mirador3.jpg"],
            reviews: [
                {
                    user: "Pedro Martínez",
                    rating: 5,
                    date: "11 Feb 2025",
                    text: "La mejor vista de Granada. Perfecto para fotos al atardecer.",
                    avatar: "https://via.placeholder.com/40"
                },
                {
                    user: "Laura Torres",
                    rating: 4,
                    date: "09 Feb 2025",
                    text: "Muy bonito, pero suele estar muy concurrido.",
                    avatar: "https://via.placeholder.com/40"
                }
            ]
        }
    ];

    // Create markers and add them to the map
    const markers = sites.map(site => {
        const marker = L.marker(site.location)
            .addTo(map)
            .on('click', () => showSiteInfo(site.id));
        return { ...site, marker };
    });

    // Function to show site information in the content section
    function showSiteInfo(siteId) {
        const site = sites.find(s => s.id === siteId);
        if (!site) return;

        // Update content section
        const content = document.getElementById('contenido');
        content.style.display = 'block'; // Ensure content is visible
        
        // Update place info
        const placeInfo = content.querySelector('.info-card');
        placeInfo.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>${site.name}</h2>
                <div class="rating">
                    <i class="fas fa-star text-warning"></i>
                    <span class="ms-1">${site.rating}</span>
                </div>
            </div>
            <p><i class="fas fa-map-marker-alt text-danger me-2"></i>${site.address}</p>
            <p><i class="fas fa-info-circle text-primary me-2"></i>${site.description}</p>
            <p><i class="fas fa-clock text-success me-2"></i>${site.hours}</p>
        `;

        // Update carousel
        const carousel = content.querySelector('#placeCarousel .carousel-inner');
        carousel.innerHTML = site.images.map((img, index) => `
            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                <img src="${img}" class="d-block w-100" alt="${site.name}">
            </div>
        `).join('');

        // Update reviews
        const reviewsCard = content.querySelector('.info-card:last-child');
        reviewsCard.innerHTML = `
            <h2>Opiniones</h2>
            <div class="reviews-summary mb-3">
                <div class="d-flex align-items-center">
                    <div class="rating-large me-3">
                        <span class="display-4">${site.rating}</span>
                        <div class="stars">
                            ${Array(5).fill().map((_, i) => `
                                <i class="fas fa-star${i < Math.floor(site.rating) ? '' : i === Math.floor(site.rating) && site.rating % 1 !== 0 ? '-half-alt' : ' far'} text-warning"></i>
                            `).join('')}
                        </div>
                    </div>
                    <div class="rating-bars">
                        ${[5, 4, 3].map(rating => `
                            <div class="rating-bar">
                                <span>${rating}</span>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: ${Math.random() * 100}%"></div>
                                </div>
                                <span>${Math.floor(Math.random() * 100)}%</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            ${site.reviews.map(review => `
                <div class="opinion">
                    <div class="opinion-header">
                        <img src="${review.avatar}" alt="Usuario" class="user-img">
                        <div>
                            <strong>${review.user}</strong>
                            <p class="text-muted mb-0">
                                ${Array(5).fill().map((_, i) => `
                                    <i class="fas fa-star${i < review.rating ? '' : ' far'} text-warning"></i>
                                `).join('')}
                                <span class="ms-2">${review.date}</span>
                            </p>
                        </div>
                    </div>
                    <p class="mt-2">"${review.text}"</p>
                </div>
            `).join('')}
            <button class="btn btn-outline-primary w-100 mt-3">
                <i class="fas fa-comments me-2"></i>Ver más opiniones
            </button>
        `;

        // Adjust layout based on screen size
        if (window.innerWidth <= 991) {
            const map = document.getElementById('divmap');
            map.style.height = '40vh';
        }
    }

    // Map controls functionality
    document.getElementById('locateMe').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                map.setView([position.coords.latitude, position.coords.longitude], 15);
                L.marker([position.coords.latitude, position.coords.longitude])
                    .bindPopup('Tu ubicación actual')
                    .addTo(map);
            });
        } else {
            alert('La geolocalización no está disponible en tu navegador.');
        }
    });

    document.getElementById('toggleView').addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth <= 576) {
            sidebar.classList.toggle('collapsed');
        }
    });
});
