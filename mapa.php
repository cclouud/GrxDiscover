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
                <a class="nav-link" href="<?php session_start(); echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ? 'php/profile.php' : 'php/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']); ?>" title="Perfil">
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
    <script src="js/server.js"></script>
    <script src="js/theme-handler.js"></script>
</body>
</html>
