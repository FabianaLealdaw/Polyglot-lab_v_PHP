<?php
require_once __DIR__ . "/includes/conexion.php";
require_once __DIR__ . "/includes/session.php";

$nombre = "";
$apellidos = "";
$email = "";
$telefono = "";
$fecha_nacimiento = "";
$direccion = "";
$sexo = "prefer_not_to_say";
$username = "";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";
    $direccion = trim($_POST["direccion"] ?? "");
    $sexo = $_POST["sexo"] ?? "prefer_not_to_say";
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $privacy = $_POST["privacy"] ?? "";

    if ($nombre === "" || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,50}$/", $nombre)) {
        $errors[] = "Please enter a valid name.";
    }

    if ($apellidos === "" || !preg_match("/^[A-Za-zÀ-ÿ ]{1,100}$/", $apellidos)) {
        $errors[] = "Please enter a valid surname.";
    }

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($telefono === "" || !preg_match("/^[0-9]{9}$/", $telefono)) {
        $errors[] = "Phone number must contain 9 digits.";
    }

    if ($fecha_nacimiento === "") {
        $errors[] = "Please select your birth date.";
    }

    if ($direccion === "" || mb_strlen($direccion) > 150) {
        $errors[] = "Please enter a valid address.";
    }

    if (!in_array($sexo, ["female", "male", "other", "prefer_not_to_say"], true)) {
        $errors[] = "Please select a valid sex value.";
    }

    if ($username === "" || !preg_match("/^[A-Za-z0-9_.]{4,20}$/", $username)) {
        $errors[] = "Username must have 4 to 20 characters and only letters, numbers, underscores or dots.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if ($privacy !== "on") {
        $errors[] = "You must accept the Privacy Policy.";
    }

    if (empty($errors)) {
        $sql_email = "SELECT id_user FROM users_data WHERE email = ?";
        $stmt_email = mysqli_prepare($conn, $sql_email);

        if ($stmt_email) {
            mysqli_stmt_bind_param($stmt_email, "s", $email);
            mysqli_stmt_execute($stmt_email);
            mysqli_stmt_store_result($stmt_email);

            if (mysqli_stmt_num_rows($stmt_email) > 0) {
                $errors[] = "This email is already registered.";
            }

            mysqli_stmt_close($stmt_email);
        } else {
            $errors[] = "Could not validate email right now.";
        }
    }

    if (empty($errors)) {
        $sql_username = "SELECT id_login FROM users_login WHERE username = ?";
        $stmt_username = mysqli_prepare($conn, $sql_username);

        if ($stmt_username) {
            mysqli_stmt_bind_param($stmt_username, "s", $username);
            mysqli_stmt_execute($stmt_username);
            mysqli_stmt_store_result($stmt_username);

            if (mysqli_stmt_num_rows($stmt_username) > 0) {
                $errors[] = "This username is already in use.";
            }

            mysqli_stmt_close($stmt_username);
        } else {
            $errors[] = "Could not validate username right now.";
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        mysqli_begin_transaction($conn);

        try {
            $sql_user_data = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_user_data = mysqli_prepare($conn, $sql_user_data);

            if (!$stmt_user_data) {
                throw new Exception("Could not prepare personal data query.");
            }

            mysqli_stmt_bind_param($stmt_user_data, "sssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo);

            if (!mysqli_stmt_execute($stmt_user_data)) {
                throw new Exception("Could not save personal data.");
            }

            $id_user = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt_user_data);

            $role = "user";
            $sql_user_login = "INSERT INTO users_login (id_user, username, usuario, password, role) VALUES (?, ?, ?, ?, ?)";
            $stmt_user_login = mysqli_prepare($conn, $sql_user_login);

            if (!$stmt_user_login) {
                throw new Exception("Could not prepare login data query.");
            }

            mysqli_stmt_bind_param($stmt_user_login, "issss", $id_user, $username, $username, $password_hash, $role);

            if (!mysqli_stmt_execute($stmt_user_login)) {
                throw new Exception("Could not save login data.");
            }

            mysqli_stmt_close($stmt_user_login);
            mysqli_commit($conn);

            $_SESSION["register_success"] = "Registration completed successfully. You can now log in.";
            header("Location: login.php?registered=1");
            exit;
        } catch (Exception $exception) {
            mysqli_rollback($conn);
            $errors[] = $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Polyglot Lab</title>

    <link rel="stylesheet" href="CSS/style.css">

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
    $base_path = "";
    $current_page = "register";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form id="register-form" class="formulario" action="register.php" method="POST" novalidate>
        <h2>Create your account</h2>

        <?php if (!empty($errors)) : ?>
          <div class="form-message form-message-error">
            <?php foreach ($errors as $error) : ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <label for="nombre">Name</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          placeholder="Your Name"
          required
          value="<?php echo htmlspecialchars($nombre); ?>"
        >

        <label for="apellidos">Surname</label>
        <input
          type="text"
          id="apellidos"
          name="apellidos"
          placeholder="Your Surname"
          required
          value="<?php echo htmlspecialchars($apellidos); ?>"
        >

        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="your@email.com"
          required
          value="<?php echo htmlspecialchars($email); ?>"
        >

        <label for="telefono">Phone number</label>
        <input
          type="tel"
          id="telefono"
          name="telefono"
          placeholder="123456789"
          required
          value="<?php echo htmlspecialchars($telefono); ?>"
        >

        <label for="fecha_nacimiento">Birth date</label>
        <input
          type="date"
          id="fecha_nacimiento"
          name="fecha_nacimiento"
          required
          value="<?php echo htmlspecialchars($fecha_nacimiento); ?>"
        >

        <label for="direccion">Address</label>
        <input
          type="text"
          id="direccion"
          name="direccion"
          placeholder="Your address"
          required
          value="<?php echo htmlspecialchars($direccion); ?>"
        >

        <label for="sexo">Sex</label>
        <select id="sexo" name="sexo" required>
          <option value="female" <?php echo $sexo === "female" ? "selected" : ""; ?>>Female</option>
          <option value="male" <?php echo $sexo === "male" ? "selected" : ""; ?>>Male</option>
          <option value="other" <?php echo $sexo === "other" ? "selected" : ""; ?>>Other</option>
          <option value="prefer_not_to_say" <?php echo $sexo === "prefer_not_to_say" ? "selected" : ""; ?>>Prefer not to say</option>
        </select>

        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Choose a username"
          required
          value="<?php echo htmlspecialchars($username); ?>"
        >

        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Create a password"
          required
        >

        <label for="confirm_password">Confirm password</label>
        <input
          type="password"
          id="confirm_password"
          name="confirm_password"
          placeholder="Repeat your password"
          required
        >

        <div class="privacy checkbox-group">
          <input type="checkbox" id="privacy" name="privacy" required>
          <label for="privacy">I accept the <a href="views/privacy.php">Privacy Policy</a></label>
        </div>

        <button type="submit">Create account</button>

        <div class="form-message-inline">
          <p>Already have an account? <a class="form-message-link" href="login.php">Log in here</a></p>
        </div>
      </form>
    </main>

    <?php include("includes/footer.php"); ?>
  </body>
</html>
