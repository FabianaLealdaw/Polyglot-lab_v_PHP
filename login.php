<?php
require_once __DIR__ . "/includes/conexion.php";
require_once __DIR__ . "/includes/session.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$username = "";
$errors = [];
$registered_message = "";

if (isset($_GET["registered"]) && isset($_SESSION["register_success"])) {
    $registered_message = $_SESSION["register_success"];
    unset($_SESSION["register_success"]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $errors[] = "Please enter your username.";
    }

    if ($password === "") {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        $sql = "SELECT ul.id_login, ul.id_user, ul.username, ul.password, ul.role, ud.nombre, ud.apellidos, ud.email
                FROM users_login ul
                INNER JOIN users_data ud ON ul.id_user = ud.id_user
                WHERE ul.username = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id_user"];
                $_SESSION["login_id"] = $user["id_login"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["nombre"] = $user["nombre"];
                $_SESSION["apellidos"] = $user["apellidos"];
                $_SESSION["email"] = $user["email"];

                header("Location: index.php");
                exit;
            }

            $errors[] = "Incorrect username or password.";
        } else {
            $errors[] = "Login is not available right now.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Polyglot Lab</title>

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
    $current_page = "login";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form id="login-form" class="formulario" action="login.php" method="POST" novalidate>
        <h2>Log in</h2>

        <?php if ($registered_message !== "") : ?>
          <div class="form-message form-message-success">
            <p><?php echo htmlspecialchars($registered_message); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
          <div class="form-message form-message-error">
            <?php foreach ($errors as $error) : ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Enter your username"
          required
          value="<?php echo htmlspecialchars($username); ?>"
        >

        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Enter your password"
          required
        >

        <button type="submit">Log in</button>

        <div class="form-message-inline">
          <p>Don't have an account yet? <a class="form-message-link" href="register.php">Register here</a></p>
        </div>
      </form>
    </main>

    <?php include("includes/footer.php"); ?>
  </body>
</html>
