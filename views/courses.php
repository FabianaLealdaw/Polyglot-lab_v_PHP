<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>

    <link rel="stylesheet" href="../CSS/style.css">

    <!-- Fuente utilizada en todo el proyecto -->
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
  </head>

  <body>
    <?php
    $base_path = "../";
    $current_page = "courses";
    ?>
    <?php include("../includes/navbar.php"); ?>

    <!-- Contenido principal -->
    <main>
      <!-- Sección de cursos -->
      <section class="courses" id="courses">
        <h2 class="courses-title">Our languages</h2>
        <p class="courses-subtitle">
          We offer comprehensive programs in the most in-demand languages,
          taught by native and certified teachers.
        </p>

        <!-- Contenedor de las tarjetas de cursos -->
        <div class="courses-wrap">

          <!-- Curso: English -->
          <article class="course-card">
            <img width="100" height="100"
              class="course-img"
              src="../assets/images/english.svg"
              alt="English course"
            >
            <h3 class="course-name">English</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Build confidence in speaking, improve listening, and master
                everyday grammar with real-life practice.
              </p>
              <a class="course-cta" href="./get_a_quote.php">Start now</a>
            </details>
          </article>

          <!-- Curso: Spanish -->
          <article class="course-card">
            <img width="100" height="100"
              class="course-img"
              src="../assets/images/spanish.svg"
              alt="Spanish course"
            >
            <h3 class="course-name">Spanish</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Learn practical Spanish for travel, work, and daily life with
                interactive conversation-based lessons.
              </p>
              <a class="course-cta" href="./get_a_quote.php">Start now</a>
            </details>
          </article>

          <!-- Curso: Italian -->
          <article class="course-card">
            <img width="100" height="100"
              class="course-img"
              src="../assets/images/italian.svg"
              alt="Italian course"
            >
            <h3 class="course-name">Italian</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                A friendly approach to Italian with pronunciation, essentials,
                and culture so you can speak from day one.
              </p>
              <a class="course-cta" href="./get_a_quote.php">Start now</a>
            </details>
          </article>

          <!-- Curso: French -->
          <article class="course-card">
            <img width="100" height="100"
              class="course-img"
              src="../assets/images/french.svg"
              alt="French course"
            >
            <h3 class="course-name">French</h3>

            <details class="course-details">
              <summary class="course-more">Ver más</summary>
              <p class="course-desc">
                Improve your pronunciation, build essential vocabulary, and
                start having real conversations from the very first lessons.
              </p>
              <a class="course-cta" href="./get_a_quote.php">Start now</a>
            </details>
          </article>

          <!-- Curso: Deutsch -->
          <article class="course-card">
            <img width="100" height="100"
              class="course-img"
              src="../assets/images/deutsch.svg"
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
              <a class="course-cta" href="./get_a_quote.php">Start now</a>
            </details>
          </article>

          <!-- Curso: Korean -->
          <article class="course-card">
            <img width="100" height="100"
              class="course-img"
              src="../assets/images/korean.svg"
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
              <a class="course-cta" href="./get_a_quote.php">Start now</a>
            </details>
          </article>

        </div>
      </section>
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
          <ul>
            <li>info@polyglotlab.com</li>
            <li>+34 123 456 789</li>
            <li>Learning Street 123 Barcelona, Spain</li>
          </ul>
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
  </body>
</html>
