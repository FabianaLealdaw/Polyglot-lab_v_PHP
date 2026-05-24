const negocio = [19.432608, -99.133209]; 

// Inicializar mapa centrado en el negocio
const map = L.map("map").setView(negocio, 14);

// Cargar mapa base
L.tileLayer("https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png", {
  attribution:
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
  subdomains: "abcd",
  maxZoom: 20,
}).addTo(map);

// Marcador del negocio
L.marker(negocio).addTo(map).bindPopup("Nuestro negocio").openPopup();

// Intentar obtener ubicación del cliente
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    (position) => {
      const cliente = [position.coords.latitude, position.coords.longitude];

      // Marcador del cliente
      L.marker(cliente, {
        icon: L.icon({
          iconUrl: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
          iconSize: [32, 32],
          iconAnchor: [16, 32],
        }),
      })
        .addTo(map)
        .bindPopup("Tu ubicación")
        .openPopup();

      // Calcular ruta cliente -> negocio
      L.Routing.control({
        waypoints: [
          L.latLng(cliente[0], cliente[1]),
          L.latLng(negocio[0], negocio[1]),
        ],
        routeWhileDragging: false,
        show: true,
      }).addTo(map);
    },
    () => {
      alert("No se pudo obtener tu ubicación.");
    },
  );
}

$(document).ready(function () {
  const $gallery = $("#gallery"); 

  $("#gallery").on("click", "img", function () {
    $("#modal-img").attr("src", $(this).attr("src"));
    $("#modal").fadeIn();
  });

  // Cerrar modal
  $("#modal").on("click", function () {
    $(this).fadeOut();
  });
});
