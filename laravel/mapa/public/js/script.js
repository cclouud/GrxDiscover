document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript cargado");

    // Inicializar el mapa centrado en Granada
    var map = L.map('map').setView([37.1773, -3.5986], 13);

    // Cargar OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Definir la función buscarLugar
    function buscarLugar() {
        let nombre = document.getElementById("buscador").value.trim();

        // Verificar si el usuario no ingresó nada
        if (nombre === "") {
            alert("Por favor, introduce un nombre.");
            return;
        }

        let url = "http://localhost/GrxDiscover/buscar_lugar.php?nombre=" + encodeURIComponent(nombre);

        console.log("Buscando con URL:", url); // Verifica la URL de la búsqueda

        // Realizar la solicitud al servidor
        fetch(url)
            .then(response => response.json())  // Procesar la respuesta como JSON
            .then(data => {
                console.log("Datos recibidos:", data); // Verifica qué datos llegan

                if (data.error) {
                    alert("Error: " + data.error);
                } else {
                    // Limpiar los marcadores previos en el mapa
                    map.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            map.removeLayer(layer);
                        }
                    });

                    // Mostrar los resultados en el mapa
                    data.lugares.forEach(lugar => {
                        L.marker([lugar.latitud, lugar.longitud])
                            .addTo(map)
                            .bindPopup(`<b>${lugar.nombre}</b><br>Lat: ${lugar.latitud}, Lng: ${lugar.longitud}`);
                    });

                    // Si se encontró un solo lugar, centrar el mapa en ese lugar
                    if (data.lugares.length === 1) {
                        map.setView([data.lugares[0].latitud, data.lugares[0].longitud], 15);
                    }
                }
            })
            .catch(error => console.error("Error en la búsqueda:", error));
    }

    // Añadir el evento de clic al botón de búsqueda
    document.getElementById("buscarBtn").addEventListener("click", buscarLugar);
});