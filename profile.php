<?php
require_once __DIR__ . "/includes/conexion.php";
require_once __DIR__ . "/includes/session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$username = $_SESSION["username"] ?? "";

$errors = [];
$success_message = "";
$password_errors = [];
$password_success_message = "";

$nombre = "";
$apellidos = "";
$email = "";
$telefono = "";
$fecha_nacimiento = "";
$direccion = "";
$sexo = "prefer_not_to_say";

function loadUserData(mysqli $conn, int $user_id): ?array
{
    $sql = "SELECT nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo FROM users_data WHERE id_user = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $user_data ?: null;
}

$user_data = loadUserData($conn, $user_id);

if ($user_data === null) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$nombre = $user_data["nombre"];
$apellidos = $user_data["apellidos"];
$email = $user_data["email"];
$telefono = $user_data["telefono"] ?? "";
$fecha_nacimiento = $user_data["fecha_nacimiento"] ?? "";
$direccion = $user_data["direccion"] ?? "";
$sexo = $user_data["sexo"] ?? "prefer_not_to_say";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_type = $_POST["form_type"] ?? "profile";

    if ($form_type === "profile") {
        $nombre = trim($_POST["nombre"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";
        $direccion = trim($_POST["direccion"] ?? "");
        $sexo = $_POST["sexo"] ?? "prefer_not_to_say";

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

        if (empty($errors)) {
            $sql_email = "SELECT id_user FROM users_data WHERE email = ? AND id_user != ?";
            $stmt_email = mysqli_prepare($conn, $sql_email);

            if ($stmt_email) {
                mysqli_stmt_bind_param($stmt_email, "si", $email, $user_id);
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
            $sql_update = "UPDATE users_data SET nombre = ?, apellidos = ?, email = ?, telefono = ?, fecha_nacimiento = ?, direccion = ?, sexo = ? WHERE id_user = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);

            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "sssssssi", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo, $user_id);

                if (mysqli_stmt_execute($stmt_update)) {
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["apellidos"] = $apellidos;
                    $_SESSION["email"] = $email;
                    $success_message = "Profile updated successfully.";
                } else {
                    $errors[] = "Could not update the profile.";
                }

                mysqli_stmt_close($stmt_update);
            } else {
                $errors[] = "Could not prepare the update query.";
            }
        }
    }

    if ($form_type === "password") {
        $current_password = $_POST["current_password"] ?? "";
        $new_password = $_POST["new_password"] ?? "";
        $confirm_new_password = $_POST["confirm_new_password"] ?? "";

        if ($current_password === "") {
            $password_errors[] = "Please enter your current password.";
        }

        if (strlen($new_password) < 6) {
            $password_errors[] = "New password must be at least 6 characters long.";
        }

        if ($new_password !== $confirm_new_password) {
            $password_errors[] = "New passwords do not match.";
        }

        if (empty($password_errors)) {
            $sql_login = "SELECT password FROM users_login WHERE id_user = ?";
            $stmt_login = mysqli_prepare($conn, $sql_login);

            if ($stmt_login) {
                mysqli_stmt_bind_param($stmt_login, "i", $user_id);
                mysqli_stmt_execute($stmt_login);
                $result_login = mysqli_stmt_get_result($stmt_login);
                $login_data = mysqli_fetch_assoc($result_login);
                mysqli_stmt_close($stmt_login);

                if (!$login_data || !password_verify($current_password, $login_data["password"])) {
                    $password_errors[] = "Current password is incorrect.";
                }
            } else {
                $password_errors[] = "Could not validate the current password.";
            }
        }

        if (empty($password_errors)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $sql_password = "UPDATE users_login SET password = ? WHERE id_user = ?";
            $stmt_password = mysqli_prepare($conn, $sql_password);

            if ($stmt_password) {
                mysqli_stmt_bind_param($stmt_password, "si", $new_password_hash, $user_id);

                if (mysqli_stmt_execute($stmt_password)) {
                    $password_success_message = "Password updated successfully.";
                } else {
                    $password_errors[] = "Could not update the password.";
                }

                mysqli_stmt_close($stmt_password);
            } else {
                $password_errors[] = "Could not prepare the password update query.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Polyglot Lab</title>

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
    $current_page = "profile";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form id="profile-form" class="formulario" action="profile.php" method="POST" novalidate>
        <h2>My profile</h2>
        <input type="hidden" name="form_type" value="profile">

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

        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          value="<?php echo htmlspecialchars($username); ?>"
          readonly
        >

        <label for="nombre">Name</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          required
          value="<?php echo htmlspecialchars($nombre); ?>"
        >

        <label for="apellidos">Surname</label>
        <input
          type="text"
          id="apellidos"
          name="apellidos"
          required
          value="<?php echo htmlspecialchars($apellidos); ?>"
        >

        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
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

        <button type="submit">Save changes</button>
      </form>

      <form id="password-form" class="formulario" action="profile.php" method="POST" novalidate>
        <h2>Change password</h2>
        <input type="hidden" name="form_type" value="password">

        <?php if (!empty($password_errors)) : ?>
          <div class="form-message form-message-error">
            <?php foreach ($password_errors as $error) : ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($password_success_message !== "") : ?>
          <div class="form-message form-message-success">
            <p><?php echo htmlspecialchars($password_success_message); ?></p>
          </div>
        <?php endif; ?>

        <label for="current_password">Current password</label>
        <input
          type="password"
          id="current_password"
          name="current_password"
          placeholder="Enter your current password"
          required
        >

        <label for="new_password">New password</label>
        <input
          type="password"
          id="new_password"
          name="new_password"
          placeholder="Enter your new password"
          required
        >

        <label for="confirm_new_password">Confirm new password</label>
        <input
          type="password"
          id="confirm_new_password"
          name="confirm_new_password"
          placeholder="Repeat your new password"
          required
        >

        <button type="submit">Update password</button>
      </form>
    </main>

    <?php include("includes/footer.php"); ?>
  </body>
</html>
