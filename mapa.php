<?php
session_start();
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
                <a class="nav-link" href="<?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ? 'php/profile.php' : 'php/login.php'; ?>" title="Perfil">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="map-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-content">
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
                <button id="show-favs-btn" class="fav-btn" onclick="toggleShowFavorites()"><i class="fas fa-heart"></i> Ver favoritos</button>
                <div id="places-list" style="padding: 0.8rem;"></div>
            </div>
            
            <!-- Panel de detalles expandible -->
            <div id="place-details" class="place-details">
                <div class="place-details-header">
                    <div class="details-actions">
                        <button class="action-btn favorite-btn" title="Añadir a favoritos">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="action-btn review-btn" title="Escribir reseña">
                            <i class="fas fa-comment"></i>
                        </button>
                    </div>
                    <button class="close-details" onclick="closePlaceDetails()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="place-details-content">
                    <div id="place-details-image"></div>
                    <h3 id="place-details-title"></h3>
                    <div id="place-details-info"></div>
                    <div class="reviews-section" style="margin-top:4.5rem;padding-top:2.5rem;display:block;">
                        <h4>Reseñas</h4>
                        <div class="review-form" style="margin-bottom:1.5rem;width:100%;max-width:500px;display:flex;flex-direction:column;align-items:center;">
                            <textarea placeholder="Escribe tu reseña..."></textarea>
                            <button onclick="submitReview()">Publicar reseña</button>
                        </div>
                        <div id="reviews-list"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div id="map"></div>
    </div>

    <!-- Panel de detalles tipo bottom sheet (fuera del sidebar y map-container) -->
    <div id="bottom-sheet" class="bottom-sheet">
        <div class="sheet-header">
            <div class="sheet-actions" style="gap:0.5em;">
                <button class="action-btn favorite-btn" title="Añadir a favoritos" style="min-width:40px;min-height:40px;font-size:1.1em;padding:0.5em 0.7em;">
                    <i class="fas fa-heart"></i>
                </button>
                <button class="action-btn review-btn" title="Escribir reseña" style="min-width:40px;min-height:40px;font-size:1.1em;padding:0.5em 0.7em;">
                    <i class="fas fa-comment"></i>
                </button>
            </div>
            <button class="close-sheet" onclick="closeBottomSheet()" style="right:0.7em;top:0.7em;font-size:1.5em;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sheet-content">
            <div id="sheet-image"></div>
            <h3 id="sheet-title"></h3>
            <div id="sheet-info"></div>
            <div class="reviews-section">
                <h4>Reseñas</h4>
                <div class="review-form">
                    <textarea placeholder="Escribe tu reseña..."></textarea>
                    <button onclick="submitReview()">Publicar reseña</button>
                </div>
                <div id="mobile-reviews-list"></div>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda y filtros flotantes para móvil -->
    <div id="mobile-filters" class="mobile-filters">
        <div class="mobile-search-box">
            <i class="fas fa-search"></i>
            <input id="mobile-search-input" type="text" placeholder="¿Qué quieres hacer hoy?">
        </div>
        <div class="mobile-filter-icons">
            <button class="mobile-filter-btn" data-category="restaurantes" title="Restaurantes"><i class="fas fa-utensils"></i></button>
            <button class="mobile-filter-btn" data-category="monumentos" title="Monumentos"><i class="fas fa-landmark"></i></button>
            <button class="mobile-filter-btn" data-category="ocio" title="Ocio"><i class="fas fa-theater-masks"></i></button>
            <button class="mobile-filter-btn" data-category="rutas" title="Rutas"><i class="fas fa-route"></i></button>
            <!-- Botón ver favoritos para móvil -->
            <button class="mobile-filter-btn" id="mobile-favs-btn" title="Ver favoritos" onclick="toggleShowFavorites()"><i class="fas fa-heart"></i></button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script type="module">
        import { initSupabase } from './config/supabase.js';
        window.supabase = initSupabase();
        const supabaseClient = window.supabase;

        // --- CLAVES DE API ---
        const UNSPLASH_ACCESS_KEY = 'zb5MCLFg2Xd7-myqIYPBLbSwADz__4lb7JBXtOeXCKw'; // Unsplash Access Key
        const PIXABAY_API_KEY = 'TU_PIXABAY_API_KEY'; // Sustituye por tu clave de Pixabay cuando la tengas

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
                // Ajustar el zoom para móvil
                map.setZoom(13);
            } else {
                // En escritorio, el mapa ocupa toda la altura menos la navbar
                mapElement.style.height = 'calc(100vh - 56px)';
                sidebarElement.style.height = 'calc(100vh - 56px)';
                // Ajustar el zoom para escritorio
                map.setZoom(14);
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
        let markers = L.featureGroup().addTo(map);
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
        async function loadPlaces(category = null, search = null, onlyFavs = false) {
            try {
                console.log('Iniciando carga de lugares...');
                
                let query = supabaseClient
                    .from('places')
                    .select('*');

                if (category) {
                    query = query.eq('category', category);
                }

                if (search) {
                    query = query.or(`name.ilike.%${search}%,description.ilike.%${search}%`);
                }

                const { data, error } = await query;

                if (error) {
                    console.error('Error de Supabase:', error);
                    throw error;
                }

                markerMap.clear();
                markers.clearLayers();
                const placesList = document.getElementById('places-list');
                placesList.innerHTML = '';
                let listHTML = '';
                let favs = await getFavorites();
                let filteredData = data;
                if (onlyFavs) {
                    filteredData = data.filter(place => favs.includes(place.id));
                }
                if (filteredData && Array.isArray(filteredData)) {
                    console.log('Procesando lugares:', filteredData.length);
                    
                    for (const place of filteredData) {
                        const marker = createMarker(place);
                        markerMap.set(parseInt(place.id), marker);
                        listHTML += `
                        <div class="place-list-item" data-place-id="${place.id}" style="cursor:pointer;display:flex;align-items:center;gap:0.7rem;padding:0.7rem;border-bottom:1px solid var(--border-color);background:var(--bg-secondary);border-radius:8px;margin-bottom:0.5rem;">
                            ${place.images && place.images.length > 0 ? 
                                `<img src="${place.images[0]}" alt="${place.name}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">` : 
                                `<img id="external-img-${place.id}" src="img/default_place.jpg" alt="${place.name}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">`
                            }
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${place.name}</div>
                                <div style="font-size:0.95em;color:var(--secondary-text);">${place.category.charAt(0).toUpperCase() + place.category.slice(1)}</div>
                            </div>
                        </div>`;
                    }
                    
                    placesList.innerHTML = listHTML;
                    
                    if (markers.getLayers().length > 0) {
                        map.fitBounds(markers.getBounds(), { padding: [40, 40] });
                    }
                    
                    addListClickEvents(filteredData);
                } else {
                    placesList.innerHTML = '<div style="color:var(--secondary-text);padding:1rem;">No hay lugares para mostrar.</div>';
                }

                // Después de placesList.innerHTML = listHTML; añade:
                for (const place of filteredData) {
                    if (!place.images || place.images.length === 0) {
                        getExternalImage(place).then(url => {
                            const imgElem = document.getElementById(`external-img-${place.id}`);
                            let finalUrl = url && typeof url === 'string' && url.trim() !== '' ? url : 'img/default_place.jpg';
                            console.log('Asignando imagen a', place.name, ':', finalUrl, imgElem);
                            if (imgElem) {
                                imgElem.src = finalUrl;
                            }
                        });
                    }
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
            const marker = L.marker([place.latitude, place.longitude], { icon });
            marker.addTo(markers);
            marker.on('click', function() {
                if (isMobile()) {
                    openBottomSheet(place);
                } else {
                    showPlaceDetails(place);
                }
                map.setView([place.latitude, place.longitude], 16, { animate: true });
            });
            return marker;
        }

        // Función para mostrar los detalles del lugar
        function showPlaceDetails(place) {
            console.log('Mostrando detalles para:', place.name);
            const detailsPanel = document.getElementById('place-details');
            const sidebarContent = document.querySelector('.sidebar-content');
            const title = document.getElementById('place-details-title');
            const image = document.getElementById('place-details-image');
            const info = document.getElementById('place-details-info');
            
            // Ocultar el contenido del sidebar con animación
            sidebarContent.classList.add('hidden');
            
            // Actualizar el contenido
            title.textContent = place.name;
            
            // Imagen
            if (place.images && place.images.length > 0) {
                image.innerHTML = `<img src="${place.images[0]}" alt="${place.name}" loading="lazy">`;
            } else {
                image.innerHTML = `<img id="details-img-${place.id}" src="img/default_place.jpg" alt="${place.name}" loading="lazy">`;
                getExternalImage(place).then(url => {
                    if (url) {
                        const imgElem = document.getElementById(`details-img-${place.id}`);
                        if (imgElem) imgElem.src = url;
                    }
                });
            }
            
            // Información
            let infoHTML = '';
            if (place.description) {
                infoHTML += `<div class="description">${place.description}</div>`;
            }
            if (place.address) {
                infoHTML += `<div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${place.address}</span>
                </div>`;
            }
            if (place.phone) {
                infoHTML += `<div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span>${place.phone}</span>
                </div>`;
            }
            if (place.website) {
                infoHTML += `<a href="${place.website}" target="_blank" rel="noopener noreferrer" class="website-link">
                    <i class="fas fa-globe"></i>
                    <span>Visitar sitio web</span>
                </a>`;
            }
            
            info.innerHTML = infoHTML;
            
            // Mostrar el panel con animación
            detailsPanel.classList.add('expanded');
            
            // Inicializar botones y reseñas
            initializeDetailsButtons(place, false);
            detailsPanel.setAttribute('data-place-id', place.id);
        }

        // Función para cerrar los detalles
        function closePlaceDetails() {
            const detailsPanel = document.getElementById('place-details');
            const sidebarContent = document.querySelector('.sidebar-content');
            
            // Ocultar el panel de detalles con animación
            detailsPanel.classList.remove('expanded');
            
            // Mostrar el contenido del sidebar con animación
            setTimeout(() => {
                sidebarContent.classList.remove('hidden');
            }, 300);
        }

        // --- BOTTOM SHEET LOGIC ---
        function openBottomSheet(place) {
            // Rellenar contenido
            document.getElementById('sheet-title').textContent = place.name;
            // Mostrar imagen o placeholder
            if (place.images && place.images.length > 0) {
                document.getElementById('sheet-image').innerHTML = `<img src="${place.images[0]}" alt="${place.name}">`;
            } else {
                document.getElementById('sheet-image').innerHTML = `<img id="sheet-img-${place.id}" src="img/default_place.jpg" alt="${place.name}">`;
                getExternalImage(place).then(url => {
                    if (url) {
                        const imgElem = document.getElementById(`sheet-img-${place.id}`);
                        if (imgElem) imgElem.src = url;
                    }
                });
            }
            let infoHTML = '';
            if (place.description) infoHTML += `<div style='margin-bottom:1rem;'>${place.description}</div>`;
            if (place.address) infoHTML += `<div style='margin-bottom:0.5rem;'><i class='fas fa-map-marker-alt'></i> ${place.address}</div>`;
            if (place.phone) infoHTML += `<div style='margin-bottom:0.5rem;'><i class='fas fa-phone'></i> ${place.phone}</div>`;
            if (place.website) infoHTML += `<div style='margin-bottom:0.5rem;'><i class='fas fa-globe'></i> <a href='${place.website}' target='_blank'>Visitar sitio web</a></div>`;
            document.getElementById('sheet-info').innerHTML = infoHTML;
            const sheet = document.getElementById('bottom-sheet');
            sheet.classList.add('open');
            sheet.style.transform = '';
            sheet.style.top = 'auto';
            sheet.style.marginTop = '0';
            
            // Inicializar botones y reseñas
            initializeDetailsButtons(place, true);
            sheet.setAttribute('data-place-id', place.id);
        }
        function closeBottomSheet() {
            const sheet = document.getElementById('bottom-sheet');
            sheet.classList.remove('open');
            setTimeout(()=>{ sheet.style.transform = ''; }, 400);
        }

        // --- MODIFICAR COMPORTAMIENTO DE MARCADORES Y LISTA ---
        function isMobile() {
            return window.innerWidth <= 768;
        }
        // Modificar la lista para centrar el mapa y mostrar detalles
        function addListClickEvents(data) {
            document.querySelectorAll('.place-list-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const placeId = parseInt(this.getAttribute('data-place-id'));
                    const marker = markerMap.get(placeId);
                    const place = data.find(p => parseInt(p.id) === placeId);
                    if (!place) return;
                    if (isMobile()) {
                        openBottomSheet(place);
                    } else {
                        showPlaceDetails(place);
                    }
                    map.setView([place.latitude, place.longitude], 16, { animate: true });
                });
            });
        }

        // --- LÓGICA DE FILTROS Y BÚSQUEDA EN MÓVIL ---
        function setMobileFilters() {
            if (window.innerWidth > 768) return;
            let currentCategory = null;
            let searchTimeout;
            const searchInput = document.getElementById('mobile-search-input');
            const filterBtns = document.querySelectorAll('.mobile-filter-btn');
            // Búsqueda funcional
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const search = e.target.value.trim();
                    loadPlaces(currentCategory, search);
                }, 300);
            });
            // Filtros funcionales
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    if (currentCategory === btn.dataset.category) {
                        // Si ya está activa, desactiva filtro
                        currentCategory = null;
                        loadPlaces();
                    } else {
                        btn.classList.add('active');
                        currentCategory = btn.dataset.category;
                        loadPlaces(currentCategory, searchInput.value.trim());
                    }
                });
            });
        }
        document.addEventListener('DOMContentLoaded', setMobileFilters);

        // --- FAVORITOS ---
        async function checkAuth() {
            try {
                // Verificar sesión de PHP
                const phpSession = await fetch('check_session.php');
                const sessionData = await phpSession.json();
                
                // Obtener la sesión actual de Supabase
                const { data: { session }, error: sessionError } = await supabaseClient.auth.getSession();
                let user = null;
                if (session && session.user) {
                    user = session.user;
                } else {
                    const { data: { user: userObj }, error: userError } = await supabaseClient.auth.getUser();
                    if (!userError && userObj) user = userObj;
                }

                // Si el usuario está logueado en Supabase pero no en PHP, sincronizar la sesión
                if (user && !sessionData.loggedin) {
                    await fetch('sync_session.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            user_id: user.id,
                            username: user.user_metadata?.username || user.email
                        })
                    });
                    // Volver a comprobar la sesión PHP
                    const phpSession2 = await fetch('check_session.php');
                    const sessionData2 = await phpSession2.json();
                    if (!sessionData2.loggedin) return null;
                    return user;
                }

                if (!sessionData.loggedin) {
                    return null;
                }
                if (user) return user;
                return null;
            } catch (error) {
                console.error('Error verificando autenticación:', error);
                return null;
            }
        }

        async function getFavorites() {
            try {
                const user = await checkAuth();
                if (!user) return [];

                const { data: favorites, error } = await supabaseClient
                    .from('favorites')
                    .select('place_id')
                    .or(`user_id.eq.${user.id},profile_id.eq.${user.id}`);

                if (error) throw error;
                return favorites.map(fav => fav.place_id);
            } catch (error) {
                console.error('Error obteniendo favoritos:', error);
                return [];
            }
        }

        async function toggleFavorite(id) {
            try {
                const user = await checkAuth();
                if (!user) {
                    const shouldLogin = confirm('Debes iniciar sesión para añadir favoritos. ¿Deseas iniciar sesión ahora?');
                    if (shouldLogin) {
                        window.location.href = 'login.php';
                    }
                    return;
                }

                const favs = await getFavorites();
                const isFavorited = favs.includes(id);
                
                if (isFavorited) {
                    // Eliminar favorito
                    const { error } = await supabaseClient
                        .from('favorites')
                        .delete()
                        .match({
                            place_id: id,
                            user_id: user.id
                        });
                        
                    if (error) throw error;
                    
                    // Actualizar UI
                    document.querySelectorAll('.favorite-btn').forEach(btn => {
                        if (btn.closest('[data-place-id="' + id + '"]')) {
                            btn.classList.remove('favorited');
                        }
                    });
                } else {
                    // Añadir favorito
                    const { error } = await supabaseClient
                        .from('favorites')
                        .insert([{
                            place_id: id,
                            user_id: user.id,
                            created_at: new Date().toISOString()
                        }]);
                        
                    if (error) throw error;
                    
                    // Actualizar UI
                    document.querySelectorAll('.favorite-btn').forEach(btn => {
                        if (btn.closest('[data-place-id="' + id + '"]')) {
                            btn.classList.add('favorited');
                        }
                    });
                }
                
                // Recargar la lista de lugares
                loadPlaces(currentCategory, searchInput.value.trim(), showOnlyFavorites);
            } catch (error) {
                console.error('Error al gestionar favorito:', error);
                alert('Error al gestionar favorito. Por favor, intenta de nuevo.');
            }
        }
        let showOnlyFavorites = false;

        // Añadir la función para alternar favoritos
        function toggleShowFavorites() {
            showOnlyFavorites = !showOnlyFavorites;
            document.getElementById('show-favs-btn').classList.toggle('active', showOnlyFavorites);
            loadPlaces(currentCategory, searchInput.value.trim(), showOnlyFavorites);
        }

        // Añadir las funciones JavaScript para manejar favoritos y reseñas:
        async function initializeDetailsButtons(place, isMobileView = false) {
            const container = isMobileView ? document.getElementById('bottom-sheet') : document.getElementById('place-details');
            const favBtn = container.querySelector('.favorite-btn');
            const reviewBtn = container.querySelector('.review-btn');
            const reviewForm = container.querySelector('.review-form');
            const reviewsList = isMobileView ? document.getElementById('mobile-reviews-list') : document.getElementById('reviews-list');
            
            // Verificar autenticación
            const user = await checkAuth();
            
            // Inicializar estado de favorito
            const favs = await getFavorites();
            favBtn.classList.toggle('favorited', favs.includes(place.id));
            
            // Event listeners
            favBtn.onclick = () => {
                toggleFavorite(place.id);
                favBtn.classList.toggle('favorited');
            };
            
            reviewBtn.onclick = () => {
                if (!user) {
                    alert('Debes iniciar sesión para escribir reseñas');
                    window.location.href = 'login.php';
                    return;
                }
                reviewForm.classList.toggle('active');
            };
            
            // Cargar reseñas existentes
            loadReviews(place.id, reviewsList);
        }

        async function submitReview() {
            const container = isMobile() ? document.getElementById('bottom-sheet') : document.getElementById('place-details');
            const placeId = container.getAttribute('data-place-id');
            const textarea = container.querySelector('textarea');
            const text = textarea.value.trim();
            
            if (!text) {
                alert('Por favor, escribe una reseña antes de enviar.');
                return;
            }
            
            try {
                const user = await checkAuth();
                if (!user) {
                    const shouldLogin = confirm('Debes iniciar sesión para escribir reseñas. ¿Deseas iniciar sesión ahora?');
                    if (shouldLogin) {
                        window.location.href = 'login.php';
                    }
                    return;
                }
                
                // Verificar si el usuario ya ha escrito una reseña para este lugar
                const { data: existingReview, error: checkError } = await supabaseClient
                    .from('reviews')
                    .select('id')
                    .eq('place_id', placeId)
                    .eq('user_id', user.id)
                    .maybeSingle();

                if (checkError && checkError.code !== 'PGRST116') { // PGRST116 es el código para "no se encontró"
                    throw checkError;
                }

                if (existingReview) {
                    const shouldUpdate = confirm('Ya has escrito una reseña para este lugar. ¿Deseas actualizarla?');
                    if (!shouldUpdate) {
                        return;
                    }
                    
                    // Actualizar reseña existente
                    const { error: updateError } = await supabaseClient
                        .from('reviews')
                        .update({ 
                            text: text,
                            updated_at: new Date().toISOString()
                        })
                        .eq('id', existingReview.id);
                        
                    if (updateError) throw updateError;
                } else {
                    // Crear nueva reseña
                    const { error: insertError } = await supabaseClient
                        .from('reviews')
                        .insert([{
                            place_id: placeId,
                            user_id: user.id,
                            text: text,
                            created_at: new Date().toISOString()
                        }]);

                    if (insertError) throw insertError;
                }

                // Actualizar la lista de reseñas
                const reviewsList = isMobile() ? 
                    document.getElementById('mobile-reviews-list') : 
                    document.getElementById('reviews-list');
                
                await loadReviews(placeId, reviewsList);
                
                // Limpiar y ocultar el formulario
                textarea.value = '';
                container.querySelector('.review-form').classList.remove('active');
            } catch (error) {
                console.error('Error al guardar la reseña:', error);
                alert('Error al guardar la reseña. Por favor, intenta de nuevo.');
            }
        }

        async function loadReviews(placeId, container) {
            try {
                // Primero obtenemos las reseñas
                const { data: reviews, error: reviewsError } = await supabaseClient
                    .from('reviews')
                    .select('*')
                    .eq('place_id', placeId)
                    .order('created_at', { ascending: false });

                if (reviewsError) throw reviewsError;
                
                if (!reviews || reviews.length === 0) {
                    container.innerHTML = '<p style="color:var(--secondary-text);padding:1rem;">No hay reseñas todavía.</p>';
                    return;
                }

                // Luego obtenemos la información de los perfiles de usuario
                const userIds = reviews.map(review => review.user_id);
                const { data: profiles, error: profilesError } = await supabaseClient
                    .from('profiles')
                    .select('id, username, full_name, avatar_url')
                    .in('id', userIds);

                if (profilesError) throw profilesError;

                // Creamos un mapa de perfiles para acceso rápido
                const profilesMap = new Map(profiles.map(profile => [profile.id, profile]));
                
                container.innerHTML = reviews.map(review => {
                    const profile = profilesMap.get(review.user_id);
                    const userName = profile?.full_name || profile?.username || 'Usuario';
                    const avatarUrl = profile?.avatar_url || null;

                    return `
                        <div class="review-item" style="display:flex;align-items:flex-start;gap:0.8em;">
                            <div style="flex-shrink:0;">
                                ${avatarUrl ?
                                    `<img src="${avatarUrl}" alt="avatar" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">` :
                                    `<i class='fas fa-user-circle' style='font-size:2.2em;color:#bbb;'></i>`
                                }
                            </div>
                            <div style="flex:1;">
                                <div class="review-meta" style="display:flex;justify-content:space-between;align-items:center;">
                                    <span style="font-weight:600;">${userName}</span>
                                    <span style="font-size:0.95em;color:var(--secondary-text);">${new Date(review.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="review-text">${review.text}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error('Error al cargar las reseñas:', error);
                container.innerHTML = '<p style="color:var(--secondary-text);padding:1rem;">Error al cargar las reseñas.</p>';
            }
        }

        // --- FUNCIÓN PARA OBTENER IMAGEN EXTERNA ---
        async function getRandomPlaceholder() {
            try {
                const response = await fetch('list_place_images.php?nocache=' + Date.now());
                const images = await response.json();
                console.log('Imágenes encontradas en img/places/:', images);
                if (images.length > 0) {
                    const randomIndex = Math.floor(Math.random() * images.length);
                    console.log('Imagen aleatoria elegida:', images[randomIndex]);
                    return images[randomIndex];
                }
            } catch (e) {
                console.error('Error obteniendo placeholders:', e);
            }
            return 'img/default_place.jpg'; // fallback si no hay imágenes
        }

        async function getExternalImage(place) {
            // 1. Buscar por nombre + categoría
            let query = encodeURIComponent(`${place.name} ${place.category}`);
            let url, response, data;

            // Unsplash
            try {
                url = `https://api.unsplash.com/search/photos?query=${query}&client_id=${UNSPLASH_ACCESS_KEY}&per_page=1`;
                response = await fetch(url);
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    if (data.results && data.results.length > 0) {
                        return data.results[0].urls.regular;
                    }
                } else {
                    throw new Error('Respuesta no JSON de Unsplash');
                }
            } catch (e) { console.error('Error en Unsplash:', e); }

            // Pixabay
            try {
                if (PIXABAY_API_KEY && PIXABAY_API_KEY !== 'TU_PIXABAY_API_KEY') {
                    url = `https://pixabay.com/api/?key=${PIXABAY_API_KEY}&q=${query}&image_type=photo&per_page=3`;
                    response = await fetch(url);
                    data = await response.json();
                    if (data.hits && data.hits.length > 0) {
                        return data.hits[0].webformatURL;
                    }
                }
            } catch (e) { console.error('Error en Pixabay:', e); }

            // Wikimedia
            try {
                const wikiUrl = await getWikimediaImage(`${place.name} ${place.category}`);
                if (wikiUrl) return wikiUrl;
            } catch (e) { console.error('Error en Wikimedia:', e); }

            // 2. Buscar solo por categoría
            query = encodeURIComponent(place.category);
            // Unsplash
            try {
                url = `https://api.unsplash.com/search/photos?query=${query}&client_id=${UNSPLASH_ACCESS_KEY}&per_page=1`;
                response = await fetch(url);
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    if (data.results && data.results.length > 0) {
                        return data.results[0].urls.regular;
                    }
                } else {
                    throw new Error('Respuesta no JSON de Unsplash (cat)');
                }
            } catch (e) { console.error('Error en Unsplash (cat):', e); }

            // Pixabay
            try {
                if (PIXABAY_API_KEY && PIXABAY_API_KEY !== 'TU_PIXABAY_API_KEY') {
                    url = `https://pixabay.com/api/?key=${PIXABAY_API_KEY}&q=${query}&image_type=photo&per_page=3`;
                    response = await fetch(url);
                    data = await response.json();
                    if (data.hits && data.hits.length > 0) {
                        return data.hits[0].webformatURL;
                    }
                }
            } catch (e) { console.error('Error en Pixabay (cat):', e); }

            // Wikimedia
            try {
                const wikiUrl = await getWikimediaImage(place.category);
                if (wikiUrl) return wikiUrl;
            } catch (e) { console.error('Error en Wikimedia (cat):', e); }

            // 3. Imagen placeholder aleatoria de la carpeta img/places/
            return await getRandomPlaceholder();
        }

        // Al final del script type="module"
        window.closePlaceDetails = closePlaceDetails;
        window.closeBottomSheet = closeBottomSheet;
        window.submitReview = submitReview;
        window.toggleShowFavorites = toggleShowFavorites;
    </script>
    <script src="js/theme-handler.js"></script>
</body>
</html>
