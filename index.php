<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <title>Polyglot Lab</title>

    <!-- style.css: estilos generales -->
    <link rel="stylesheet" href="CSS/style.css" >

    <!-- Librería Font Awesome para iconos (redes sociales, etc.) -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    >

    <!-- Optimización y carga de la fuente desde Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" >
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin >
    <link
      href="https://fonts.googleapis.com/css2?family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap"
      rel="stylesheet"
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
    <script
      src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      crossorigin=""
    ></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
  </head>

  <body>

    <?php include("includes/navbar.php"); ?>

    <!-- Contenido principal de la página -->
    <main id="inicio">
      <!-- Sección hero -->
      <section class="hero">
        <h1>One Lab. Many languages. One world.</h1>
        <p><i>Languages aren’t meant to stay in books.</i></p>
        <p>
          At Polyglot Lab, we turn learning into real communication through
          practice, culture, and a supportive environment where making mistakes
          is part of the process.
        </p>
        <p>Experiment. Practice. Speak.</p>
      </section>

      <!-- Sección de noticias -->
      <section class="news-section">
        <div class="news-wrapper">
          <h2 class="section-title">Latest news</h2>
          <p class="section-subtitle">
            Updates and announcements from Polyglot Lab
          </p>

          <div id="news-container" class="news-grid">
            <!-- Las noticias se cargaran aqui -->
          </div>
        </div>
      </section>

      <!-- Sección de galería -->
      <section class="gallery-section">
        <h2 class="section-title">Gallery</h2>
        <p class="gallery-subtitle">
          A glimpse of our learning spaces and activities
        </p>

        <div id="gallery" class="gallery">
          <figure>
            <img
              src="./assets/images/gallery/clase1.jpg"
              alt="Language class"
            >
          </figure>
          <figure>
            <img
              src="./assets/images/gallery/clase2.jpg"
              alt="Student studying"
            >
          </figure>
          <figure>
            <img
              src="./assets/images/gallery/clase3.jpg"
              alt="Kids learning together"
            >
          </figure>
          <figure>
            <img
              src="./assets/images/gallery/clase4.jpg"
              alt="Private lesson"
            >
          </figure>
          <figure>
            <img src="./assets/images/gallery/clase5.jpg" alt="Language lab" >
          </figure>
          <figure>
            <img src="./assets/images/gallery/adults.jpg" alt="Adult class" >
          </figure>
        </div>

        <!-- Modal -->
        <div id="modal">
          <img id="modal-img" src="./assets/images/gallery/clase1.jpg" alt="Enlarged gallery image" >
        </div>
        
      </section>
      <!-- Sección de cursos -->
      <section class="courses" id="courses">
        <h2 class="courses-title">Our languages</h2>
        <p class="courses-subtitle">
          We offer comprehensive programs in the most in-demand languages,
          taught by native and certified teachers.
        </p>

        <!-- Contenedor de las tarjetas de cursos -->
        <div class="courses-wrap">
          <!-- Tarjeta de curso: English -->
          <article class="course-card">
            <img
              width="100"
              height="100"
              class="course-img"
              src="./assets/images/english.svg"
              alt="English course"
            >
            <h3 class="course-name">English</h3>

            <!-- Información desplegable del curso -->
            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Build confidence in speaking, improve listening, and master
                everyday grammar with real-life practice.
              </p>
              <a class="course-cta" href="./views/get_a_quote.php"
                >Start now</a
              >
            </details>
          </article>

          <!-- Tarjeta de curso: Spanish -->
          <article class="course-card">
            <img
              width="100"
              height="100"
              class="course-img"
              src="./assets/images/spanish.svg"
              alt="Spanish course"
            >
            <h3 class="course-name">Spanish</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Learn practical Spanish for travel, work, and daily life with
                interactive conversation-based lessons.
              </p>
              <a class="course-cta" href="./views/get_a_quote.php"
                >Start now</a
              >
            </details>
          </article>

          <!-- Tarjeta de curso: Italian -->
          <article class="course-card">
            <img
              width="100"
              height="100"
              class="course-img"
              src="./assets/images/italian.svg"
              alt="Italian course"
            >
            <h3 class="course-name">Italian</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                A friendly approach to Italian with pronunciation, essentials,
                and culture so you can speak from day one.
              </p>
              <a class="course-cta" href="./views/get_a_quote.php"
                >Start now</a
              >
            </details>
          </article>

          <!-- Tarjeta de curso: French -->
          <article class="course-card">
            <img
              width="100"
              height="100"
              class="course-img"
              src="./assets/images/french.svg"
              alt="French course"
            >
            <h3 class="course-name">French</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Improve your pronunciation, build essential vocabulary, and
                start having real conversations from the very first lessons.
              </p>
              <a class="course-cta" href="./views/get_a_quote.php"
                >Start now</a
              >
            </details>
          </article>

          <!-- Tarjeta de curso: Deutsch -->
          <article class="course-card">
            <img
              width="100"
              height="100"
              class="course-img"
              src="./assets/images/deutsch.svg"
              alt="German course"
            >
            <h3 class="course-name">Deutsch</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Learn practical German with clear structure: grammar made
                simple, useful phrases, and speaking practice for everyday
                situations.
              </p>
              <a class="course-cta" href="./views/get_a_quote.php"
                >Start now</a
              >
            </details>
          </article>

          <!-- Tarjeta de curso: Korean -->
          <article class="course-card">
            <img
              width="100"
              height="100"
              class="course-img"
              src="./assets/images/korean.svg"
              alt="Korean course"
            >
            <h3 class="course-name">Korean</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Master Hangul quickly, improve pronunciation, and build solid
                speaking skills with fun, step-by-step lessons and real-life
                dialogues.
              </p>
              <a class="course-cta" href="./views/get_a_quote.php"
                >Start now</a
              >
            </details>
          </article>
        </div>
      </section>

      <!-- Sección de mapa / localización -->
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
      </section>

      <!-- Sección del formulario -->
      <section>
        <!-- Formulario para pedir presupuesto -->
        <form id="formulario-web" class="formulario" name="webForm">
          <h2>Get a Quote!</h2>

          <!-- =====================
              CONTACT INFORMATION
              ===================== -->

          <!-- Campo: nombre -->
          <label for="nombre">Name</label>
          <input
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Your Name"
            required
            pattern="[A-Za-zÀ-ÿ\s]{1,15}"
          >

          <!-- Campo: apellidos -->
          <label for="apellidos">Surname</label>
          <input
            type="text"
            id="apellidos"
            name="apellidos"
            placeholder="Your Surname"
            required
            pattern="[A-Za-zÀ-ÿ\s]{1,40}$"
          >

          <!-- Campo: email -->
          <label for="correo">Email</label>
          <input
            type="email"
            id="correo"
            name="correo"
            placeholder="your@email.com"
            required
          >

          <!-- Campo: teléfono -->
          <label for="telefono">Phone number</label>
          <input
            type="tel"
            id="telefono"
            name="telefono"
            placeholder="123456789"
            required
            pattern="[0-9]{9}"
          >

          <!-- Campo: descripción -->
          <label for="descripcion">Description</label>
          <textarea
            id="descripcion"
            name="descripcion"
            placeholder="Tell us more about you..."
            rows="5"
            title="Description must be between 10 and 300 characters"
          ></textarea>

          <!-- =====================
              COURSE SELECTION
              ===================== -->

          <label for="curso">Choose a course</label>
          <select id="curso" name="curso">
            <option value="300">General English – 300€</option>
            <option value="450">Intensive Course – 450€</option>
            <option value="250">Online Course – 250€</option>
          </select>

          <label for="meses">Number of months</label>
          <input
            type="number"
            id="meses"
            name="meses"
            min="1"
            max="12"
            value="1"
          >
          <label for="plazo">Delivery time (days):</label>
          <input 
            type="number" 
            id="plazo" 
            min="1" 
            required
          >

          <!-- =====================
              EXTRAS
              ===================== -->

          <fieldset class="extras">
            <legend>Extras</legend>

            <label>
              <input
                type="checkbox"
                class="extra"
                name="extra-workshops"
                value="50"
              >
              <span>Conversation workshops (+50€)</span>
            </label>

            <label>
              <input
                type="checkbox"
                class="extra"
                name="extra-materials"
                value="30"
              >
              <span>Learning materials (+30€)</span>
            </label>

            <label>
              <input
                type="checkbox"
                class="extra"
                name="extra-exam"
                value="70"
              >
              <span>Exam preparation (+70€)</span>
            </label>
          </fieldset>
          
          <!-- =====================
              TOTAL PRICE
              ===================== -->
          <p id="total-price">Total: €0</p>

          <!-- Aceptación de política de privacidad -->
          <div class="privacy">
            <input type="checkbox" id="privacy" name="privacy" required >
            <label for="privacy">
              I accept the
              <a href="./views/privacy.php" target="_blank">Privacy Policy</a>
            </label>
          </div>

          <!-- Botón de envío -->
          <button type="submit">Send</button>
          <button type="reset">Reset</button>
        </form>
      </section>
      <section id="news-detail" class="news-detail-section">
        <div class="news-wrapper">
          <h2 class="section-title">News</h2>
          <p class="section-subtitle">
            Detailed information about our latest updates
          </p>

          <div id="news-detail-container">
            <!-- Full news content will be loaded here -->
          </div>
        </div>
      </section>
    </main>

    <!-- Footer de la web -->
    <?php include("includes/footer.php"); ?>

    <!-- Librería jQuery para funcionalidades interactivas -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- News (solo en index) -->
    <script src="js/news.js"></script>

    <!-- Gallery modal -->
    <script src="./js/gallery.js"></script>

    <!-- presupuesto -->
    <script src="./js/budget.js"></script>

    <!-- mapa -->
    <script src="./js/map.js"></script>
  </body>
</html>
