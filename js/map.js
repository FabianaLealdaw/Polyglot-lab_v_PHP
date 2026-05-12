document.addEventListener("DOMContentLoaded", () => {
  const mapElement = document.getElementById("map");
  const directionsLink = document.getElementById("directions-link");

  if (!mapElement || typeof L === "undefined") return;

  const business = [41.38535, 2.14672];
  const map = L.map(mapElement).setView(business, 13);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  const businessMarker = L.marker(business).addTo(map);
  businessMarker.bindPopup("Polyglot Lab").openPopup();

  // Recalculate
  requestAnimationFrame(() => {
    map.invalidateSize();
  });

  if (directionsLink) {
    directionsLink.href =
      "https://www.google.com/maps/dir/?api=1&destination=41.38535,2.14672";
  }

  if (!navigator.geolocation || !L.Routing) return;

  navigator.geolocation.getCurrentPosition(
    ({ coords }) => {
      const client = [coords.latitude, coords.longitude];

      if (directionsLink) {
        directionsLink.href =
          `https://www.google.com/maps/dir/?api=1&origin=${client[0]},${client[1]}&destination=41.38535,2.14672`;
      }

      L.Routing.control({
        waypoints: [L.latLng(client[0], client[1]), L.latLng(...business)],
        addWaypoints: false,
        draggableWaypoints: false,
        fitSelectedRoutes: true,
        show: false,
        lineOptions: {
          styles: [{ color: "#e86b63", weight: 5, opacity: 0.85 }],
        },
        createMarker(index, waypoint) {
          const label = index === 0 ? "Your location" : "Polyglot Lab";
          return L.marker(waypoint.latLng).bindPopup(label);
        },
      }).addTo(map);

      requestAnimationFrame(() => {
        map.invalidateSize();
      });
    },
    () => {
      map.setView(business, 14);
      requestAnimationFrame(() => {
        map.invalidateSize();
      });
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
    },
  );
});
