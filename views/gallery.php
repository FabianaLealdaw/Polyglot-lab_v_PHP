<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <title>Gallery</title>

    <!-- style.css: estilos generales -->
    <link rel="stylesheet" href="../CSS/style.css" >

    <!-- Fuente del proyecto (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" >
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin >
    <link
      href="https://fonts.googleapis.com/css2?family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap"
      rel="stylesheet"
    >

    <!-- Librería de iconos -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    >
  </head>

  <body>
    <?php
    $base_path = "../";
    $current_page = "gallery";
    ?>
    <?php include("../includes/navbar.php"); ?>
    <main>
      <section class="gallery-section">
        <h2 class="section-title">Gallery</h2>
        <p class="gallery-subtitle">
          A glimpse of our learning spaces and activities
        </p>

        <div id="gallery" class="gallery">
          <figure>
            <img src="../assets/images/gallery/clase1.jpg" alt="Language class" >
          </figure>
          <figure>
            <img src="../assets/images/gallery/clase2.jpg" alt="Student studying">
          </figure>
          <figure>
            <img src="../assets/images/gallery/clase3.jpg" alt="Kids learning together">
          </figure>
          <figure>
            <img src="../assets/images/gallery/clase4.jpg" alt="Private lesson" >
          </figure>
          <figure>
            <img src="../assets/images/gallery/clase5.jpg" alt="Language lab" >
          </figure>
          <figure>
            <img src="../assets/images/gallery/adults.jpg" alt="Adult class" >
          </figure>
        </div>

        <!-- Modal -->
        <div id="modal">
          <img id="modal-img" src="../assets/images/gallery/clase1.jpg" alt="Enlarged gallery image" >
        </div>
      </section>
    </main>
    <footer>
      <div>
        <div class="vision">
          <img src="../assets/images/Logo_PL.png" alt="Logo" width="200" >
          <p>
            Your language lab, where the passion for learning meets academic
            excellence in a warm and welcoming environment.
          </p>
        </div>

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

    <!-- Librería jQuery para funcionalidades interactivas -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Script específico para la galería -->
    <script src="../js/gallery.js"></script>
  </body>
</html>
