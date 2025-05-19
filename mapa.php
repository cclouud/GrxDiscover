<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: php/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRX Discover</title>

    <!-- Bootstrap y estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/mapa.css">
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/logorojo.svg" alt="GRXDiscover Logo" class="logo">
                GRXDiscover
            </a>
            <div class="navbar-icons">
                <button class="theme-toggle" id="themeToggle">
                    <i class="fas fa-sun"></i>
                </button>
                <a class="nav-link" href="<?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ? '/GrxDiscover/php/profile.php' : '/GrxDiscover/php/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']); ?>" title="Perfil">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="map-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Search Container -->
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="¿Qué quieres hacer hoy?">
                </div>
            </div>

            <!-- Categories -->
            <div class="categories">
                <h3>Filtros</h3>
                <div class="category-list">
                    <button class="category-btn" data-category="restaurantes">
                        <i class="fas fa-utensils"></i>
                        Restaurantes
                    </button>
                    <button class="category-btn" data-category="monumentos">
                        <i class="fas fa-landmark"></i>
                        Monumentos
                    </button>
                    <button class="category-btn" data-category="ocio">
                        <i class="fas fa-theater-masks"></i>
                        Ocio
                    </button>
                    <button class="category-btn" data-category="rutas">
                        <i class="fas fa-route"></i>
                        Rutas
                    </button>
                </div>
            </div>
            <!-- Lista de lugares -->
            <div id="places-list" style="padding: 0.8rem;"></div>
        </div>

        <!-- Map Section -->
        <div id="map"></div>


        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        // Inicializar Supabase
        const supabaseUrl = 'https://hoojcjagwvrelqytwgdg.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imhvb2pjamFnd3ZyZWxxeXR3Z2RnIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY3ODY2MjEsImV4cCI6MjA2MjM2MjYyMX0.gEqnLIAWSNIjUcM2KndKI5VfEn5jPCA9sQ-MPl5Of6U';
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        // Inicializar el mapa
        const map = L.map('map', {
            zoomControl: false, // Desactivamos el control de zoom por defecto
            touchZoom: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            boxZoom: true,
            keyboard: true,
            dragging: true,
            attributionControl: true
        }).setView([37.1882, -3.6067], 13);

        // Añadir el tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Añadir control de zoom personalizado
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        // Ajustar el mapa cuando cambia el tamaño de la ventana
        function adjustMapSize() {
            const isMobile = window.innerWidth <= 768;
            const mapElement = document.getElementById('map');
            const sidebarElement = document.querySelector('.sidebar');
            
            if (isMobile) {
                // En móvil, el mapa ocupa toda la altura menos el espacio del sidebar
                mapElement.style.height = 'calc(100vh - 60px)';
                sidebarElement.style.height = '85vh';
            } else {
                // En escritorio, el mapa ocupa toda la altura menos la navbar
                mapElement.style.height = 'calc(100vh - 56px)';
                sidebarElement.style.height = 'calc(100vh - 56px)';
            }
            
            // Forzar actualización del mapa
            map.invalidateSize();
        }

        // Ajustar el tamaño inicial
        adjustMapSize();

        // Ajustar cuando cambia el tamaño de la ventana
        window.addEventListener('resize', adjustMapSize);

        // Manejar eventos táctiles
        map.on('touchstart', function(e) {
            if (e.touches.length === 1) {
                map.dragging.enable();
            }
        });

        map.on('touchend', function() {
            map.dragging.disable();
        });

        // Crear icono personalizado
        const createCustomIcon = (category) => {
            const iconColors = {
                'restaurantes': '#ff5a5f',
                'monumentos': '#2c3e50',
                'ocio': '#27ae60',
                'rutas': '#f39c12'
            };

            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${iconColors[category] || '#ff5a5f'}; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <i class="fas ${getCategoryIcon(category)}"></i>
                </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15],
                popupAnchor: [0, -15]
            });
        };

        // Función para obtener el icono según la categoría
        const getCategoryIcon = (category) => {
            const icons = {
                'restaurantes': 'fa-utensils',
                'monumentos': 'fa-landmark',
                'ocio': 'fa-theater-masks',
                'rutas': 'fa-route'
            };
            return icons[category] || 'fa-map-marker-alt';
        };

        // Objeto para almacenar los marcadores
        let markers = L.layerGroup().addTo(map);
        let currentCategory = null;
        let markerMap = new Map(); // Relaciona el id del lugar con el marcador

        // Función para obtener imágenes de Wikimedia Commons
        async function getWikimediaImage(name) {
            try {
                // Limpiamos el nombre para la búsqueda
                const searchName = name.replace(/[^a-zA-Z0-9\s]/g, ' ').trim();
                const url = `https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrnamespace=6&gsrsearch=${encodeURIComponent(searchName)}&gsrlimit=1&prop=imageinfo&iiprop=url&format=json&origin=*`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.query && data.query.pages) {
                    const page = Object.values(data.query.pages)[0];
                    if (page.imageinfo && page.imageinfo[0]) {
                        return page.imageinfo[0].url;
                    }
                }
                return null;
            } catch (error) {
                console.error('Error obteniendo imagen de Wikimedia:', error);
                return null;
            }
        }

        // Función para obtener información de OpenStreetMap
        async function getOSMData(lat, lon, name) {
            try {
                const radius = 200;
                const url = `https://overpass-api.de/api/interpreter?data=[out:json];
                    (
                        node["name"~"${name}",i](around:${radius},${lat},${lon});
                        node["tourism"](around:${radius},${lat},${lon});
                        node["historic"](around:${radius},${lat},${lon});
                        node["amenity"](around:${radius},${lat},${lon});
                    );
                    out body;>;out skel qt;`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                let imageUrl = null;
                let osmId = null;
                let tags = {};

                if (data.elements && data.elements.length > 0) {
                    const elementsWithImages = data.elements.filter(element => 
                        element.tags?.image || 
                        element.tags?.['image:0'] || 
                        element.tags?.['image:1'] || 
                        element.tags?.wikimedia_commons ||
                        element.tags?.['image:url'] ||
                        element.tags?.['image:front']
                    );

                    if (elementsWithImages.length > 0) {
                        const place = elementsWithImages[0];
                        imageUrl = place.tags?.image || 
                                 place.tags?.['image:0'] || 
                                 place.tags?.['image:1'] || 
                                 place.tags?.wikimedia_commons ||
                                 place.tags?.['image:url'] ||
                                 place.tags?.['image:front'];
                        osmId = place.id;
                        tags = place.tags || {};
                    }
                }

                // Si no encontramos imagen en OSM, buscamos en Wikimedia
                if (!imageUrl) {
                    imageUrl = await getWikimediaImage(name);
                }
                
                return {
                    imageUrl,
                    osmId,
                    tags
                };
            } catch (error) {
                console.error('Error obteniendo datos de OSM:', error);
                return null;
            }
        }

        // Función para cargar los lugares
        async function loadPlaces(category = null, search = null) {
            try {
                console.log('Cargando lugares...');
                let query = supabaseClient
                    .from('places')
                    .select('*');

                // Aplicar filtros si se proporcionan
                if (category) {
                    console.log('Filtrando por categoría:', category);
                    query = query.eq('category', category);
                }

                if (search) {
                    console.log('Filtrando por búsqueda:', search);
                    query = query.or(`name.ilike.%${search}%,description.ilike.%${search}%`);
                }

                const { data, error } = await query;

                if (error) {
                    console.error('Error de Supabase:', error);
                    throw error;
                }

                console.log('Datos recibidos:', data);

                markerMap.clear();
                markers.clearLayers();
                const placesList = document.getElementById('places-list');
                placesList.innerHTML = '';
                let listHTML = '';
                if (data && Array.isArray(data)) {
                    for (const place of data) {
                        // Obtener datos de OSM
                        const osmData = await getOSMData(place.latitude, place.longitude, place.name);
                        
                        const marker = createMarker(place);
                        markerMap.set(place.id, marker);
                        markers.addLayer(marker);
                        // Renderiza el item de la lista
                        listHTML += `
                        <div class="place-list-item" data-place-id="${place.id}" style="cursor:pointer;display:flex;align-items:center;gap:0.7rem;padding:0.7rem 0;border-bottom:1px solid var(--border-color);">
                            ${place.images && place.images.length > 0 ? `<img src="${place.images[0]}" alt="${place.name}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">` : `<div style="width:48px;height:48px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fas ${getCategoryIcon(place.category)}" style="color:#bbb;font-size:1.5rem;"></i></div>`}
                            <div style="flex:1;">
                                <div style="font-weight:600;">${place.name}</div>
                                <div style="font-size:0.95em;color:var(--secondary-text);">${place.category.charAt(0).toUpperCase() + place.category.slice(1)}</div>
                            </div>
                        </div>`;
                    }
                    placesList.innerHTML = listHTML;
                    // Añade eventos a los items
                    document.querySelectorAll('.place-list-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const placeId = this.getAttribute('data-place-id');
                            const marker = markerMap.get(placeId);
                            if (marker) {
                                map.setView(marker.getLatLng(), 16, { animate: true });
                                marker.openPopup();
                            }
                        });
                    });
                } else {
                    placesList.innerHTML = '<div style="color:var(--secondary-text);padding:1rem;">No hay lugares para mostrar.</div>';
                }

            } catch (error) {
                console.error('Error cargando lugares:', error);
                alert('Error al cargar los lugares. Por favor, intenta de nuevo.');
            }
        }

        // Manejar los botones de categoría
        document.querySelectorAll('.category-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                console.log('Botón de categoría clickeado:', button.dataset.category);

                // Remover clase activa de todos los botones
                document.querySelectorAll('.category-btn').forEach(btn => 
                    btn.classList.remove('active'));
                
                // Añadir clase activa al botón clickeado
                button.classList.add('active');

                // Obtener la categoría
                const category = button.dataset.category;
                
                // Si se clickea la misma categoría, mostrar todos
                if (currentCategory === category) {
                    console.log('Desactivando filtro de categoría');
                    currentCategory = null;
                    button.classList.remove('active');
                    loadPlaces();
                } else {
                    console.log('Activando filtro de categoría:', category);
                    currentCategory = category;
                    loadPlaces(category);
                }
            });
        });

        // Manejar el buscador
        const searchInput = document.querySelector('.search-box input');
        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const search = e.target.value.trim();
                loadPlaces(currentCategory, search);
            }, 300);
        });

        // Cargar todos los lugares al inicio
        loadPlaces();

        // Función para crear marcador
        function createMarker(place) {
            const icon = createCustomIcon(place.category);
            const marker = L.marker([place.latitude, place.longitude], { icon }).addTo(markers);
            
            // Crear el contenido del popup
            const popupContent = `
                <div class="popup-content">
                    <div class="popup-header">
                        <h3>${place.name}</h3>
                        ${place.rating ? `
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                ${place.rating}
                            </div>
                        ` : ''}
                    </div>
                    ${place.images && place.images.length > 0 ? `
                        <div class="popup-image">
                            <img src="${place.images[0]}" alt="${place.name}" loading="lazy">
                        </div>
                    ` : ''}
                    <div class="popup-info">
                        ${place.description ? `
                            <div class="description">${place.description}</div>
                        ` : ''}
                        ${place.address ? `
                            <p><i class="fas fa-map-marker-alt"></i> ${place.address}</p>
                        ` : ''}
                        ${place.phone ? `
                            <p><i class="fas fa-phone"></i> ${place.phone}</p>
                        ` : ''}
                        ${place.website ? `
                            <p><i class="fas fa-globe"></i> <a href="${place.website}" target="_blank" rel="noopener noreferrer">Visitar sitio web</a></p>
                        ` : ''}
                    </div>
                </div>
            `;

            // Crear el popup con opciones personalizadas
            const popup = L.popup({
                maxWidth: 400,
                minWidth: 300,
                maxHeight: '70vh',
                className: 'custom-popup',
                closeButton: true,
                autoPan: true,
                autoPanPadding: [50, 50],
                offset: [0, 20]
            }).setContent(popupContent);

            // Agregar el popup al marcador
            marker.bindPopup(popup);

            // Centrar el mapa en el marcador cuando se hace clic
            marker.on('click', function() {
                const isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // En móvil, centramos el mapa y ajustamos el zoom
                    map.setView([place.latitude, place.longitude], 16, {
                        animate: true,
                        duration: 0.5
                    });
                } else {
                    // En escritorio, usamos la lógica anterior
                    const mapHeight = document.getElementById('map').offsetHeight;
                    const targetY = mapHeight * 0.75;
                    const markerPoint = map.latLngToContainerPoint([place.latitude, place.longitude]);
                    const offsetY = targetY - markerPoint.y;
                    const offsetLatLng = map.containerPointToLatLng([markerPoint.x, markerPoint.y + offsetY]);
                    
                    map.setView([offsetLatLng.lat, offsetLatLng.lng], map.getZoom(), {
                        animate: true,
                        duration: 0.5
                    });
                }

                // Forzar la actualización del popup después de la animación
                setTimeout(() => {
                    marker.openPopup();
                }, 100);
            });

            return marker;
        }
    </script>
    <script src="js/theme-handler.js"></script>
</body>
</html>
