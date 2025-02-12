// Función para buscar coordenadas de una dirección e insertarlas en la base de datos
function geocodeAddress(address) {
    var geocodeUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${address}`;

    fetch(geocodeUrl)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                var lat = data[0].lat;
                var lon = data[0].lon;
                console.log(`Dirección: ${address}`);
                console.log(`Latitud: ${lat}, Longitud: ${lon}`);

                // Enviar las coordenadas y el nombre del lugar al servidor para almacenarlas
                saveLocationToDatabase(address, lat, lon);
            } else {
                alert("No se encontraron coordenadas para esta dirección.");
            }
        })
        .catch(error => {
            console.error('Error al obtener las coordenadas:', error);
        });
}

// Función para guardar los datos en la base de datos
function saveLocationToDatabase(nombre, lat, lon) {
    var formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('lat', lat);
    formData.append('lon', lon);

    // Enviar las coordenadas y nombre del lugar al servidor con AJAX
    fetch('guardar_lugar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Lugar guardado correctamente.");
        } else {
            alert("Hubo un error al guardar el lugar.");
        }
    })
    .catch(error => {
        console.error('Error al enviar los datos al servidor:', error);
    });
}
