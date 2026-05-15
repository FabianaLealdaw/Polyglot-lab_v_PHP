<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get a Quote</title>

    <!-- CSS general del proyecto-->
    <link rel="stylesheet" href="../CSS/style.css">

    <!-- Fuente del proyecto (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap"
      rel="stylesheet"
    >

    <!-- Librería de iconos (Font Awesome) -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    >
  </head>

  <body>
    <?php
    $base_path = "../";
    $current_page = "quote";
    ?>
    <?php include("../includes/navbar.php"); ?>

    <main>
    
      <!-- Formulario para pedir presupuesto -->
      <form
        id="formulario-web"
        class="formulario"
        name="webForm"
        action="#"
      >
        <h2>Get a Quote!</h2>

        <!-- Campo: nombre -->
        <label for="nombre">Name</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          placeholder="Your Name"
          required
          pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,15}$"
          title="Name must contain only letters and spaces (max 15 characters)"
        >

        <!-- Campo: apellidos -->
          <label for="apellidos">Surname</label>
          <input
          type="text"
          id="apellidos"
          name="apellidos"
          placeholder="Your Surname"
          required
          pattern="[A-Za-zÀ-ÿ\s]{1,40}"
          title="Surname must contain only letters and spaces (max 40 characters)"
          >

        <!-- Campo: email -->
        <label for="correo">Mail</label>
        <input
          type="email"
          id="correo"
          name="correo"
          placeholder="your@email.com"
          required
          pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
          title="Please enter a valid email address">
       

        <!-- Campo: teléfono -->
        <label for="telefono">Phone number</label>
        <input
          type="tel"
          id="telefono"
          name="telefono"
          placeholder="123 456 789"
          required
          pattern="^[0-9]{9}$"
          title="Enter a valid phone number">
  

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
            <option value="300">General English - 300€</option>
            <option value="450">Intensive Course - 450€</option>
            <option value="250">Online Course - 250€</option>
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
           
          <p id="resultado"></p>

          <!-- =====================
              EXTRAS
              ===================== -->

          <fieldset class="extras">
            <legend>Extras</legend>

            <label>
              <input type="checkbox" class="extra" name="extra-workshops" value="50" >
              <span>Conversation workshops (+50€)</span>
            </label>

            <label>
              <input type="checkbox" class="extra" name="extra-materials" value="30" >
              <span>Learning materials (+30€)</span>
            </label>

            <label>
              <input type="checkbox" class="extra" name="extra-exam" value="70" >
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
              <a href="./privacy.php" target="_blank">Privacy Policy</a>
            </label>
          </div>

          <!-- Botón de envío -->
          <button type="submit">Send</button>
          <button type="reset">Reset</button>
          
        </form>
          <!-- Resumen del presupuesto generado por JavaScript -->
        <section id="quote-result" class="quote-result" hidden>
          <h3>Your quote summary</h3>

          <p><strong>Course:</strong> <span id="summary-course"></span></p>
          <p><strong>Months:</strong> <span id="summary-months"></span></p>
          <p><strong>Extras:</strong></p>
          <ul id="summary-extras"></ul>

          <p class="final-price">
            Total price: <span id="summary-total"></span>
          </p>

          <a href="./get_a_quote.php" class="edit-quote">Edit quote</a>
        </section>

    </main>

    <!-- Footer con info de la marca, links y redes -->
    <footer>
      <div>
        <div class="vision">
          <img src="../assets/images/Logo_PL.png" alt="Logo" width="200">
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
              <li><a href="mailto:info@polyglotlab.com">info@polyglotlab.com</a></li>
              <li><a href="tel:+34123456789">+34 123 456 789</a></li>
              <li>Learning Street 123, Barcelona, Spain</li>
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
    <script src="../js/budget.js"></script>

  </body>
</html>
