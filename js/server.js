document.addEventListener("DOMContentLoaded", function () {
    // Initialize map centered on Granada
    var map = L.map('map').setView([37.1773, -3.5986], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

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
        const content = document.getElementById('contenido');
        const map = document.getElementById('divmap');
        if (window.innerWidth <= 991) {
            if (content.style.display === 'none') {
                content.style.display = 'block';
                map.style.height = '40vh';
            } else {
                content.style.display = 'none';
                map.style.height = '100vh';
            }
        }
    });
});
