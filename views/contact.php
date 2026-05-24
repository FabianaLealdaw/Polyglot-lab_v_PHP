<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>

    <!-- CSS general del proyecto -->
    <link rel="stylesheet" href="../CSS/style.css">

    <!-- Fuente utilizada en el proyecto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap"
      rel="stylesheet"
    >

    <!-- Librería de iconos -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      crossorigin=""
    >
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css"
    >
  </head>

  <body>
    <?php
    $base_path = "../";
    $current_page = "contact";
    ?>
    <?php include("../includes/navbar.php"); ?>

    <!-- Contenido principal de la página -->
    <main class="contact-page">

      <!-- Sección del mapa/ubicación -->
      <section class="contact-section">
        <h2 class="contact-title">Contact</h2>

       <p class="contact-subtitle">Find us here or get in touch with us</p>

        <div class="map-wrapper">
          <div
            id="map"
            class="map-frame"
            role="application"
            aria-label="Polyglot Lab map and route"
          >
          </div>
        </div>
      </section>

        <div class="map-button-wrapper">
          <a 
            id="directions-link"
            class="map-button"
            href="https://www.google.com/maps/dir/?api=1&destination=41.38535,2.14672"
            target="_blank"
            rel="noopener noreferrer"
          >
            Get directions
          </a>
       </div>

        <div>
        <section class="contact-info">
        <h2>Contact information</h2>

        <p><strong>Polyglot Lab</strong></p>
        <p>Learning Street 123</p>
        <p>08001 Barcelona, Spain</p>

        <p>
          <i class="fa-solid fa-phone"></i>
          <a href="tel:+34123456789">+34 123 456 789</a>
        </p>

        <p>
          <i class="fa-solid fa-envelope"></i>
          <a href="mailto:info@polyglotlab.com">info@polyglotlab.com</a>
        </p>
      </section>
        </div>
     
  
    </main>

    <!-- Footer -->
    <footer>
      <div>
        <div class="vision">
          <img src="../assets/images/Logo_PL.png" alt="Logo" width="200">
          <p>
            Your language lab, where the passion for learning meets academic
            excellence in a warm and welcoming environment.
          </p>
        </div>

        <!-- Enlaces rápidos del footer -->
        <div class="quick_links">
          <h3>Quick Links</h3>
          <nav>
            <a href="../index.php">Home</a>
            <a href="./courses.php">Courses</a>
            <a href="./gallery.php">Gallery</a>
            <a href="./contact.php">Contact</a>
            <a href="./get_a_quote.php">Get a Quote</a>
            <a href="./legal_notice.php">Legal Notice</a>
          </nav>
        </div>

        <!-- Información de contacto -->
        <div class="contact">
          <h3>Contact</h3>
          <address>
            <ul>
              <li>info@polyglotlab.com</li>
              <li>+34 123 456 789</li>
              <li>Learning Street 123 Barcelona, Spain</li>
            </ul>
          </address>
        </div>

        <!-- Redes sociales -->
        <div class="social_media">
          <h3>Social media</h3>

          <ul>
            <li>
              <a href="https://www.linkedin.com/company/polyglot-lab">
                <i class="fa-brands fa-linkedin fa-2xl"></i>
              </a>
            </li>
            <li>
              <a href="https://www.instagram.com/polyglotlab/">
                <i class="fa-brands fa-instagram fa-2xl"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="divide"></div>
      <p>Copyright 2025</p>
    </footer>
    <script
      src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      crossorigin=""
    ></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="../js/map.js?v=2"></script>

  </body>
</html>
