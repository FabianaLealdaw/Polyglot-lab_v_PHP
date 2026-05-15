<?php
require_once __DIR__ . "/includes/conexion.php";
require_once __DIR__ . "/includes/session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: index.php");
    exit;
}

$errors = [];
$success_message = "";

$edit_user_id = 0;
$nombre = "";
$apellidos = "";
$email = "";
$telefono = "";
$username = "";
$role = "user";
$password = "";
$confirm_password = "";

function getAdminUserById(mysqli $conn, int $id_user): ?array
{
    $sql = "SELECT ud.id_user, ud.nombre, ud.apellidos, ud.email, ud.telefono, ul.username, ul.role
            FROM users_data ud
            INNER JOIN users_login ul ON ud.id_user = ul.id_user
            WHERE ud.id_user = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $user ?: null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_type = $_POST["form_type"] ?? "create";

    if ($form_type === "delete") {
        $delete_id = (int) ($_POST["id_user"] ?? 0);

        if ($delete_id === (int) $_SESSION["user_id"]) {
            $errors[] = "You cannot delete the account currently in use.";
        } else {
            $sql_delete = "DELETE FROM users_data WHERE id_user = ?";
            $stmt_delete = mysqli_prepare($conn, $sql_delete);

            if ($stmt_delete) {
                mysqli_stmt_bind_param($stmt_delete, "i", $delete_id);

                if (mysqli_stmt_execute($stmt_delete)) {
                    $success_message = "User deleted successfully.";
                } else {
                    $errors[] = "Could not delete the user.";
                }

                mysqli_stmt_close($stmt_delete);
            } else {
                $errors[] = "Could not prepare the delete query.";
            }
        }
    }

    if ($form_type === "create" || $form_type === "update") {
        $edit_user_id = (int) ($_POST["id_user"] ?? 0);
        $nombre = trim($_POST["nombre"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $role = $_POST["role"] ?? "user";
        $password = $_POST["password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";

        if ($nombre === "" || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,50}$/", $nombre)) {
            $errors[] = "Please enter a valid name.";
        }

        if ($apellidos === "" || !preg_match("/^[A-Za-zÀ-ÿ ]{1,100}$/", $apellidos)) {
            $errors[] = "Please enter a valid surname.";
        }

        if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        if ($telefono !== "" && !preg_match("/^[0-9]{9}$/", $telefono)) {
            $errors[] = "Phone number must contain 9 digits.";
        }

        if ($username === "" || !preg_match("/^[A-Za-z0-9_.]{4,20}$/", $username)) {
            $errors[] = "Username must have 4 to 20 characters and only letters, numbers, underscores or dots.";
        }

        if (!in_array($role, ["admin", "user"], true)) {
            $errors[] = "Please select a valid role.";
        }

        if ($form_type === "create") {
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long.";
            }

            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            }
        }

        if ($form_type === "update" && ($password !== "" || $confirm_password !== "")) {
            if (strlen($password) < 6) {
                $errors[] = "New password must be at least 6 characters long.";
            }

            if ($password !== $confirm_password) {
                $errors[] = "New passwords do not match.";
            }
        }

        if (empty($errors)) {
            $sql_email = "SELECT id_user FROM users_data WHERE email = ? AND id_user != ?";
            $stmt_email = mysqli_prepare($conn, $sql_email);

            if ($stmt_email) {
                mysqli_stmt_bind_param($stmt_email, "si", $email, $edit_user_id);
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
            $sql_username = "SELECT id_user FROM users_login WHERE username = ? AND id_user != ?";
            $stmt_username = mysqli_prepare($conn, $sql_username);

            if ($stmt_username) {
                mysqli_stmt_bind_param($stmt_username, "si", $username, $edit_user_id);
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

        if (empty($errors) && $form_type === "create") {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_begin_transaction($conn);

            try {
                $sql_user_data = "INSERT INTO users_data (nombre, apellidos, email, telefono) VALUES (?, ?, ?, ?)";
                $stmt_user_data = mysqli_prepare($conn, $sql_user_data);

                if (!$stmt_user_data) {
                    throw new Exception("Could not prepare personal data query.");
                }

                mysqli_stmt_bind_param($stmt_user_data, "ssss", $nombre, $apellidos, $email, $telefono);

                if (!mysqli_stmt_execute($stmt_user_data)) {
                    throw new Exception("Could not save personal data.");
                }

                $new_user_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt_user_data);

                $sql_user_login = "INSERT INTO users_login (id_user, username, password, role) VALUES (?, ?, ?, ?)";
                $stmt_user_login = mysqli_prepare($conn, $sql_user_login);

                if (!$stmt_user_login) {
                    throw new Exception("Could not prepare login data query.");
                }

                mysqli_stmt_bind_param($stmt_user_login, "isss", $new_user_id, $username, $password_hash, $role);

                if (!mysqli_stmt_execute($stmt_user_login)) {
                    throw new Exception("Could not save login data.");
                }

                mysqli_stmt_close($stmt_user_login);
                mysqli_commit($conn);

                $success_message = "User created successfully.";
                $nombre = "";
                $apellidos = "";
                $email = "";
                $telefono = "";
                $username = "";
                $role = "user";
                $edit_user_id = 0;
            } catch (Exception $exception) {
                mysqli_rollback($conn);
                $errors[] = $exception->getMessage();
            }
        }

        if (empty($errors) && $form_type === "update") {
            mysqli_begin_transaction($conn);

            try {
                $sql_update_data = "UPDATE users_data SET nombre = ?, apellidos = ?, email = ?, telefono = ? WHERE id_user = ?";
                $stmt_update_data = mysqli_prepare($conn, $sql_update_data);

                if (!$stmt_update_data) {
                    throw new Exception("Could not prepare personal data update query.");
                }

                mysqli_stmt_bind_param($stmt_update_data, "ssssi", $nombre, $apellidos, $email, $telefono, $edit_user_id);

                if (!mysqli_stmt_execute($stmt_update_data)) {
                    throw new Exception("Could not update personal data.");
                }

                mysqli_stmt_close($stmt_update_data);

                if ($password !== "") {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql_update_login = "UPDATE users_login SET username = ?, role = ?, password = ? WHERE id_user = ?";
                    $stmt_update_login = mysqli_prepare($conn, $sql_update_login);

                    if (!$stmt_update_login) {
                        throw new Exception("Could not prepare login update query.");
                    }

                    mysqli_stmt_bind_param($stmt_update_login, "sssi", $username, $role, $password_hash, $edit_user_id);
                } else {
                    $sql_update_login = "UPDATE users_login SET username = ?, role = ? WHERE id_user = ?";
                    $stmt_update_login = mysqli_prepare($conn, $sql_update_login);

                    if (!$stmt_update_login) {
                        throw new Exception("Could not prepare login update query.");
                    }

                    mysqli_stmt_bind_param($stmt_update_login, "ssi", $username, $role, $edit_user_id);
                }

                if (!mysqli_stmt_execute($stmt_update_login)) {
                    throw new Exception("Could not update login data.");
                }

                mysqli_stmt_close($stmt_update_login);
                mysqli_commit($conn);

                if ($edit_user_id === (int) $_SESSION["user_id"]) {
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $role;
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["apellidos"] = $apellidos;
                    $_SESSION["email"] = $email;
                }

                $success_message = "User updated successfully.";
                $nombre = "";
                $apellidos = "";
                $email = "";
                $telefono = "";
                $username = "";
                $role = "user";
                $edit_user_id = 0;
            } catch (Exception $exception) {
                mysqli_rollback($conn);
                $errors[] = $exception->getMessage();
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["edit"])) {
    $edit_user_id = (int) $_GET["edit"];
    $user = getAdminUserById($conn, $edit_user_id);

    if ($user) {
        $nombre = $user["nombre"];
        $apellidos = $user["apellidos"];
        $email = $user["email"];
        $telefono = $user["telefono"] ?? "";
        $username = $user["username"];
        $role = $user["role"];
    } else {
        $errors[] = "User not found.";
        $edit_user_id = 0;
    }
}

$users = [];
$sql_list = "SELECT ud.id_user, ud.nombre, ud.apellidos, ud.email, ud.telefono, ul.username, ul.role
             FROM users_data ud
             INNER JOIN users_login ul ON ud.id_user = ul.id_user
             ORDER BY ud.created_at DESC, ud.id_user DESC";
$result_list = mysqli_query($conn, $sql_list);

if ($result_list) {
    while ($row = mysqli_fetch_assoc($result_list)) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users | Polyglot Lab</title>

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
    $current_page = "admin_users";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form class="formulario" action="admin_users.php" method="POST" novalidate>
        <h2><?php echo $edit_user_id > 0 ? "Edit user" : "Admin users"; ?></h2>

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

        <input type="hidden" name="form_type" value="<?php echo $edit_user_id > 0 ? "update" : "create"; ?>">
        <input type="hidden" name="id_user" value="<?php echo (int) $edit_user_id; ?>">

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
          value="<?php echo htmlspecialchars($telefono); ?>"
        >

        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          required
          value="<?php echo htmlspecialchars($username); ?>"
        >

        <label for="role">Role</label>
        <select id="role" name="role">
          <option value="user" <?php echo $role === "user" ? "selected" : ""; ?>>User</option>
          <option value="admin" <?php echo $role === "admin" ? "selected" : ""; ?>>Admin</option>
        </select>

        <label for="password"><?php echo $edit_user_id > 0 ? "New password (optional)" : "Password"; ?></label>
        <input
          type="password"
          id="password"
          name="password"
          <?php echo $edit_user_id > 0 ? "" : "required"; ?>
        >

        <label for="confirm_password"><?php echo $edit_user_id > 0 ? "Confirm new password" : "Confirm password"; ?></label>
        <input
          type="password"
          id="confirm_password"
          name="confirm_password"
          <?php echo $edit_user_id > 0 ? "" : "required"; ?>
        >

        <button type="submit"><?php echo $edit_user_id > 0 ? "Update user" : "Create user"; ?></button>
      </form>

      <section class="appointments-section">
        <div class="appointments-card">
          <h2>User list</h2>

          <?php if (empty($users)) : ?>
            <p class="appointments-empty">There are no users yet.</p>
          <?php else : ?>
            <div class="appointments-table-wrap">
              <table class="appointments-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user) : ?>
                    <tr>
                      <td><?php echo htmlspecialchars($user["nombre"] . " " . $user["apellidos"]); ?></td>
                      <td><?php echo htmlspecialchars($user["email"]); ?></td>
                      <td><?php echo htmlspecialchars($user["telefono"] ?: "-"); ?></td>
                      <td><?php echo htmlspecialchars($user["username"]); ?></td>
                      <td><?php echo htmlspecialchars($user["role"]); ?></td>
                      <td class="appointments-actions">
                        <a href="admin_users.php?edit=<?php echo (int) $user["id_user"]; ?>" class="appointments-link">Edit</a>
                        <?php if ((int) $user["id_user"] === (int) $_SESSION["user_id"]) : ?>
                          <span class="appointments-locked">Current account</span>
                        <?php else : ?>
                          <form action="admin_users.php" method="POST" class="appointments-inline-form">
                            <input type="hidden" name="form_type" value="delete">
                            <input type="hidden" name="id_user" value="<?php echo (int) $user["id_user"]; ?>">
                            <button type="submit" class="appointments-delete">Delete</button>
                          </form>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </main>

    <?php include("includes/footer.php"); ?>
  </body>
</html>
