    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('map').setView([37.1773, -3.5986], 13); // Centrado en Granada

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    });
