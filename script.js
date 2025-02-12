document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript cargado");

    // Inicializar el mapa centrado en Granada
    var map = L.map('map').setView([37.1773, -3.5986], 13);

    // Cargar OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker;

    // Función para buscar en la base de datos
    window.buscarLugar = function () {
        let nombre = document.getElementById("busqueda").value.trim();

        if (nombre === "") {
            alert("Por favor, introduce un nombre válido.");
            return;
        }

        console.log("Buscando:", nombre); // Verifica que el nombre se obtiene bien

        // Cambiar URL a la del servidor XAMPP
        fetch("http://localhost/GrxDiscover/buscar_lugar.php?nombre=" + encodeURIComponent(nombre))
            .then(response => response.json())  // Procesar la respuesta como JSON
            .then(data => {
                console.log("Datos recibidos:", data); // Verifica qué datos llegan

                if (data.error) {
                    alert("Error: " + data.error);
                } else {
                    console.log("Coordenadas encontradas:", data);

                    // Mover el mapa a la nueva ubicación
                    map.setView([data.latitud, data.longitud], 15);

                    // Si ya hay un marcador, eliminarlo
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    // Agregar un nuevo marcador
                    marker = L.marker([data.latitud, data.longitud]).addTo(map)
                        .bindPopup(`<b>${nombre}</b><br>Lat: ${data.latitud}, Lng: ${data.longitud}`)
                        .openPopup();
                }
            })
            .catch(error => console.error("Error en la búsqueda:", error));
    };
});
