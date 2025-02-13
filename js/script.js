
    
        // Definir la función buscarLugar
        console.log("JavaScript cargado");
        function buscarLugar() {
            let nombre = document.getElementById("buscador").value.trim();
            let categoria = document.getElementById("categoria") ? document.getElementById("categoria").value : ""; // Obtener categoría seleccionada
    
            // Verificar si el usuario no ingresó nada
            if (nombre === "" && categoria === "") {
                alert("Por favor, introduce un nombre o selecciona una categoría.");
                return;
            }
    
            let url = "http://localhost/GrxDiscover/buscar_lugar.php?";
    
            // Añadir el nombre o la categoría a la URL de la solicitud
            if (nombre) url += `nombre=${encodeURIComponent(nombre)}`;
            if (categoria) url += (nombre ? "&" : "") + `categoria=${encodeURIComponent(categoria)}`;
    
            console.log("Buscando con URL:", url); // Verifica la URL de la búsqueda
    
            // Realizar la solicitud al servidor
            fetch("http://localhost/GrxDiscover/buscar_lugar.php?nombre=" + encodeURIComponent(nombre))
                .then(response => response.json())  // Procesar la respuesta como JSON
                .then(data => {

                    console.log("Datos recibidos:", data); // Verifica qué datos llegan

                    if (data.error) {
                        alert("Error: " + data.error);
                    } else {
                        // Limpiar los marcadores previos en el mapa
                        if (marker) map.removeLayer(marker);
    
                        // Mostrar los resultados en el mapa
                        data.lugares.forEach(lugar => {
                            L.marker([lugar.latitud, lugar.longitud])
                                .addTo(map)
                                .bindPopup(`<b>${lugar.nombre}</b><br>Categoria: ${lugar.categoria}<br>Lat: ${lugar.latitud}, Lng: ${lugar.longitud}`);
                        });
    
                        // Si se encontró un solo lugar, centrar el mapa en ese lugar
                        if (data.lugares.length === 1) {
                            map.setView([data.lugares[0].latitud, data.lugares[0].longitud], 15);
                        }
                    }
                })
                .catch(error => console.error("Error en la búsqueda:", error));
        }
    

    
    document.addEventListener("DOMContentLoaded", function () {
        console.log("JavaScript cargado");
    
        // Inicializar el mapa centrado en Granada
        var map = L.map('map').setView([37.1773, -3.5986], 13);
    
        // Cargar OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        var marker;


});
