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
        const map = L.map('map').setView([37.1882, -3.6067], 13); // Coordenadas de Granada

        // Añadir el tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Objeto para almacenar los marcadores
        let markers = L.layerGroup().addTo(map);
        let currentCategory = null;

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

                // Limpiar marcadores existentes
                markers.clearLayers();

                // Añadir nuevos marcadores
                if (data && Array.isArray(data)) {
                    data.forEach(place => {
                        const marker = L.marker([place.latitude, place.longitude])
                            .bindPopup(`
                                <h3>${place.name}</h3>
                                <p>${place.description || ''}</p>
                                ${place.rating ? `<p>⭐ ${place.rating}</p>` : ''}
                                ${place.address ? `<p>📍 ${place.address}</p>` : ''}
                                ${place.phone ? `<p>📞 ${place.phone}</p>` : ''}
                                ${place.website ? `<p><a href="${place.website}" target="_blank">🌐 Visitar web</a></p>` : ''}
                            `);
                        markers.addLayer(marker);
                    });
                } else {
                    console.warn('No se recibieron datos válidos');
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
    </script>
    <script src="js/theme-handler.js"></script>
</body>
</html>
