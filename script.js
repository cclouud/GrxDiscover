fetch(url)
    .then(response => response.text())  // Primero, recibe la respuesta como texto
    .then(text => {
        console.log("Respuesta del servidor:", text); // Verifica la respuesta antes de convertirla en JSON
        
        try {
            // Intenta convertir el texto a JSON
            const data = JSON.parse(text);
            console.log("Datos recibidos:", data);  // Verifica los datos después de convertirlos

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
        } catch (error) {
            console.error("Error al procesar la respuesta JSON:", error);
            alert("Error al procesar los datos. Verifica la respuesta del servidor.");
        }
    })
    .catch(error => console.error("Error en la búsqueda:", error));
