
var map = L.map('map').setView([37.1773, -3.5986], 13); // [Latitud, Longitud], Zoom

        // Cargar los tiles de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Agregar un marcador en Granada
        var marker = L.marker([37.1773, -3.5986]).addTo(map)
            .bindPopup("<b>Granada, España</b><br>Una ciudad hermosa.").openPopup();
