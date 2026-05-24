<?php
require_once __DIR__ . "/../includes/conexion.php";

$nombre = "";
$apellidos = "";
$correo = "";
$telefono = "";
$descripcion = "";
$curso = "300";
$meses = 1;
$plazo = 1;
$selected_extras = [];

$errors = [];
$success_message = "";
$quote_summary = null;

$course_options = [
    "300" => "General English - 300€",
    "450" => "Intensive Course - 450€",
    "250" => "Online Course - 250€",
];

$extra_options = [
    "extra-workshops" => ["label" => "Conversation workshops", "value" => 50],
    "extra-materials" => ["label" => "Learning materials", "value" => 30],
    "extra-exam" => ["label" => "Exam preparation", "value" => 70],
];

function calculateQuoteTotal(int $course_price, int $months, int $plazo, array $extras, array $extra_options): float
{
    $total = $course_price * $months;

    if ($months >= 3 && $months <= 5) {
        $total *= 0.95;
    } elseif ($months >= 6) {
        $total *= 0.9;
    }

    foreach ($extras as $extra_key) {
        if (isset($extra_options[$extra_key])) {
            $total += $extra_options[$extra_key]["value"];
        }
    }

    if ($plazo <= 7) {
        $discount = 0;
    } elseif ($plazo <= 30) {
        $discount = 0.05;
    } elseif ($plazo <= 90) {
        $discount = 0.10;
    } else {
        $discount = 0.15;
    }

    return round($total - ($total * $discount), 2);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $curso = $_POST["curso"] ?? "300";
    $meses = (int) ($_POST["meses"] ?? 1);
    $plazo = (int) ($_POST["plazo"] ?? 0);
    $privacy = $_POST["privacy"] ?? "";

    foreach (array_keys($extra_options) as $extra_key) {
        if (isset($_POST[$extra_key])) {
            $selected_extras[] = $extra_key;
        }
    }

    if ($nombre === "" || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,15}$/", $nombre)) {
        $errors[] = "Please enter a valid name.";
    }

    if ($apellidos === "" || !preg_match("/^[A-Za-zÀ-ÿ\s]{1,40}$/", $apellidos)) {
        $errors[] = "Please enter a valid surname.";
    }

    if ($correo === "" || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (!preg_match("/^[0-9]{9}$/", $telefono)) {
        $errors[] = "Phone number must contain 9 digits.";
    }

    if (mb_strlen($descripcion) < 10 || mb_strlen($descripcion) > 300) {
        $errors[] = "Description must be between 10 and 300 characters.";
    }

    if (!isset($course_options[$curso])) {
        $errors[] = "Please select a valid course.";
    }

    if ($meses < 1 || $meses > 12) {
        $errors[] = "Number of months must be between 1 and 12.";
    }

    if ($plazo < 1) {
        $errors[] = "Please enter a valid delivery time.";
    }

    if ($privacy !== "on") {
        $errors[] = "You must accept the Privacy Policy.";
    }

    if (empty($errors)) {
        $course_price = (int) $curso;
        $course_label = $course_options[$curso];
        $extras_labels = [];

        foreach ($selected_extras as $extra_key) {
            if (isset($extra_options[$extra_key])) {
                $extras_labels[] = $extra_options[$extra_key]["label"];
            }
        }

        $total_price = calculateQuoteTotal($course_price, $meses, $plazo, $selected_extras, $extra_options);
        $extras_text = empty($extras_labels) ? "No extras selected" : implode(", ", $extras_labels);

        $sql_insert = "INSERT INTO quote_requests (nombre, apellidos, email, telefono, descripcion, curso, meses, plazo, extras, total_price)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);

        if ($stmt_insert) {
            mysqli_stmt_bind_param(
                $stmt_insert,
                "ssssssissd",
                $nombre,
                $apellidos,
                $correo,
                $telefono,
                $descripcion,
                $course_label,
                $meses,
                $plazo,
                $extras_text,
                $total_price
            );

            if (mysqli_stmt_execute($stmt_insert)) {
                $success_message = "Your quote request has been sent successfully.";
                $quote_summary = [
                    "course" => $course_label,
                    "months" => $meses,
                    "extras" => $extras_labels,
                    "total" => number_format($total_price, 2),
                ];

                $nombre = "";
                $apellidos = "";
                $correo = "";
                $telefono = "";
                $descripcion = "";
                $curso = "300";
                $meses = 1;
                $plazo = 1;
                $selected_extras = [];
            } else {
                $errors[] = "Could not save your quote request.";
            }

            mysqli_stmt_close($stmt_insert);
        } else {
            $errors[] = "Could not prepare the quote request query.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get a Quote</title>

    <link rel="stylesheet" href="../CSS/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap"
      rel="stylesheet"
    >

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
      <form id="formulario-web" class="formulario" name="webForm" action="get_a_quote.php" method="POST" novalidate>
        <h2>Get a Quote!</h2>

        <?php if (!empty($errors)) : ?>
          <div class="form-message form-message-error">
            <?php foreach ($errors as $error) : ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($success_message !== "") : ?>
          <div class="form-message form-message-success">
            <p><?php echo htmlspecialchars($success_message); ?></p>
          </div>
        <?php endif; ?>

        <label for="nombre">Name</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          placeholder="Your Name"
          required
          pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,15}$"
          title="Name must contain only letters and spaces (max 15 characters)"
          value="<?php echo htmlspecialchars($nombre); ?>"
        >

        <label for="apellidos">Surname</label>
        <input
          type="text"
          id="apellidos"
          name="apellidos"
          placeholder="Your Surname"
          required
          pattern="[A-Za-zÀ-ÿ\s]{1,40}"
          title="Surname must contain only letters and spaces (max 40 characters)"
          value="<?php echo htmlspecialchars($apellidos); ?>"
        >

        <label for="correo">Mail</label>
        <input
          type="email"
          id="correo"
          name="correo"
          placeholder="your@email.com"
          required
          pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
          title="Please enter a valid email address"
          value="<?php echo htmlspecialchars($correo); ?>"
        >

        <label for="telefono">Phone number</label>
        <input
          type="tel"
          id="telefono"
          name="telefono"
          placeholder="123 456 789"
          required
          pattern="^[0-9]{9}$"
          title="Enter a valid phone number"
          value="<?php echo htmlspecialchars($telefono); ?>"
        >

        <label for="descripcion">Description</label>
        <textarea
          id="descripcion"
          name="descripcion"
          placeholder="Tell us more about you..."
          rows="5"
          title="Description must be between 10 and 300 characters"
        ><?php echo htmlspecialchars($descripcion); ?></textarea>

        <label for="curso">Choose a course</label>
        <select id="curso" name="curso">
          <?php foreach ($course_options as $value => $label) : ?>
            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $curso === $value ? "selected" : ""; ?>><?php echo htmlspecialchars($label); ?></option>
          <?php endforeach; ?>
        </select>

        <label for="meses">Number of months</label>
        <input
          type="number"
          id="meses"
          name="meses"
          min="1"
          max="12"
          value="<?php echo (int) $meses; ?>"
        >

        <label for="plazo">Delivery time (days):</label>
        <input
          type="number"
          id="plazo"
          name="plazo"
          min="1"
          required
          value="<?php echo (int) $plazo; ?>"
        >

        <p id="resultado"></p>

        <fieldset class="extras">
          <legend>Extras</legend>

          <?php foreach ($extra_options as $extra_key => $extra_data) : ?>
            <label>
              <input type="checkbox" class="extra" name="<?php echo htmlspecialchars($extra_key); ?>" value="<?php echo (int) $extra_data['value']; ?>" <?php echo in_array($extra_key, $selected_extras, true) ? "checked" : ""; ?>>
              <span><?php echo htmlspecialchars($extra_data['label']); ?> (+<?php echo (int) $extra_data['value']; ?>€)</span>
            </label>
          <?php endforeach; ?>
        </fieldset>

        <p id="total-price">Total: €0</p>

        <div class="privacy">
          <input type="checkbox" id="privacy" name="privacy" required>
          <label for="privacy">
            I accept the
            <a href="./privacy.php" target="_blank">Privacy Policy</a>
          </label>
        </div>

        <button type="submit">Send</button>
        <button type="reset">Reset</button>
      </form>

      <section id="quote-result" class="quote-result" <?php echo $quote_summary ? "" : "hidden"; ?>>
        <h3>Your quote summary</h3>

        <?php if ($quote_summary) : ?>
          <p><strong>Course:</strong> <?php echo htmlspecialchars($quote_summary['course']); ?></p>
          <p><strong>Months:</strong> <?php echo htmlspecialchars((string) $quote_summary['months']); ?></p>
          <p><strong>Extras:</strong></p>
          <ul id="summary-extras">
            <?php if (empty($quote_summary['extras'])) : ?>
              <li>No extras selected</li>
            <?php else : ?>
              <?php foreach ($quote_summary['extras'] as $extra_label) : ?>
                <li><?php echo htmlspecialchars($extra_label); ?></li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>

          <p class="final-price">Total price: €<?php echo htmlspecialchars($quote_summary['total']); ?></p>
          <a href="./get_a_quote.php" class="edit-quote">Edit quote</a>
        <?php else : ?>
          <p><strong>Course:</strong> <span id="summary-course"></span></p>
          <p><strong>Months:</strong> <span id="summary-months"></span></p>
          <p><strong>Extras:</strong></p>
          <ul id="summary-extras"></ul>
          <p class="final-price">Total price: <span id="summary-total"></span></p>
          <a href="./get_a_quote.php" class="edit-quote">Edit quote</a>
        <?php endif; ?>
      </section>
    </main>

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
