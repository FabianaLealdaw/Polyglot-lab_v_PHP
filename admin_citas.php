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

$id_cita = 0;
$titulo = "";
$descripcion = "";
$fecha_cita = "";
$hora_cita = "";
$estado = "pendiente";

function getAdminAppointmentById(mysqli $conn, int $id_cita): ?array
{
    $sql = "SELECT c.*, ul.username, ud.nombre, ud.apellidos
            FROM citas c
            INNER JOIN users_data ud ON c.id_user = ud.id_user
            INNER JOIN users_login ul ON ud.id_user = ul.id_user
            WHERE c.id_cita = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_cita);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $appointment = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $appointment ?: null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_type = $_POST["form_type"] ?? "update";

    if ($form_type === "delete") {
        $delete_id = (int) ($_POST["id_cita"] ?? 0);
        $sql_delete = "DELETE FROM citas WHERE id_cita = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);

        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "i", $delete_id);

            if (mysqli_stmt_execute($stmt_delete)) {
                $success_message = "Appointment deleted successfully.";
            } else {
                $errors[] = "Could not delete the appointment.";
            }

            mysqli_stmt_close($stmt_delete);
        } else {
            $errors[] = "Could not prepare the delete query.";
        }
    }

    if ($form_type === "update") {
        $id_cita = (int) ($_POST["id_cita"] ?? 0);
        $titulo = trim($_POST["titulo"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $fecha_cita = $_POST["fecha_cita"] ?? "";
        $hora_cita = $_POST["hora_cita"] ?? "";
        $estado = $_POST["estado"] ?? "pendiente";

        if ($titulo === "" || mb_strlen($titulo) > 100) {
            $errors[] = "Please enter a valid title.";
        }

        if (mb_strlen($descripcion) > 1000) {
            $errors[] = "Description is too long.";
        }

        if ($fecha_cita === "" || $hora_cita === "") {
            $errors[] = "Please select a valid date and time.";
        }

        if (!in_array($estado, ["pendiente", "confirmada", "cancelada"], true)) {
            $errors[] = "Please select a valid status.";
        }

        if (empty($errors)) {
            $sql_update = "UPDATE citas
                           SET titulo = ?, descripcion = ?, fecha_cita = ?, hora_cita = ?, estado = ?
                           WHERE id_cita = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);

            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "sssssi", $titulo, $descripcion, $fecha_cita, $hora_cita, $estado, $id_cita);

                if (mysqli_stmt_execute($stmt_update)) {
                    $success_message = "Appointment updated successfully.";
                    $id_cita = 0;
                    $titulo = "";
                    $descripcion = "";
                    $fecha_cita = "";
                    $hora_cita = "";
                    $estado = "pendiente";
                } else {
                    $errors[] = "Could not update the appointment.";
                }

                mysqli_stmt_close($stmt_update);
            } else {
                $errors[] = "Could not prepare the update query.";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["edit"])) {
    $id_cita = (int) $_GET["edit"];
    $appointment = getAdminAppointmentById($conn, $id_cita);

    if ($appointment) {
        $titulo = $appointment["titulo"];
        $descripcion = $appointment["descripcion"] ?? "";
        $fecha_cita = $appointment["fecha_cita"];
        $hora_cita = substr($appointment["hora_cita"], 0, 5);
        $estado = $appointment["estado"];
    } else {
        $errors[] = "Appointment not found.";
        $id_cita = 0;
    }
}

$appointments = [];
$sql_list = "SELECT c.*, ul.username, ud.nombre, ud.apellidos
             FROM citas c
             INNER JOIN users_data ud ON c.id_user = ud.id_user
             INNER JOIN users_login ul ON ud.id_user = ul.id_user
             ORDER BY c.fecha_cita ASC, c.hora_cita ASC";
$result_list = mysqli_query($conn, $sql_list);

if ($result_list) {
    while ($row = mysqli_fetch_assoc($result_list)) {
        $appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Appointments | Polyglot Lab</title>

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
    $current_page = "admin_citas";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form class="formulario" action="admin_citas.php" method="POST" novalidate>
        <h2><?php echo $id_cita > 0 ? "Edit appointment" : "Admin appointments"; ?></h2>

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

        <input type="hidden" name="form_type" value="update">
        <input type="hidden" name="id_cita" value="<?php echo (int) $id_cita; ?>">

        <label for="titulo">Title</label>
        <input
          type="text"
          id="titulo"
          name="titulo"
          required
          value="<?php echo htmlspecialchars($titulo); ?>"
        >

        <label for="descripcion">Description</label>
        <textarea
          id="descripcion"
          name="descripcion"
          rows="5"
        ><?php echo htmlspecialchars($descripcion); ?></textarea>

        <label for="fecha_cita">Date</label>
        <input
          type="date"
          id="fecha_cita"
          name="fecha_cita"
          required
          value="<?php echo htmlspecialchars($fecha_cita); ?>"
        >

        <label for="hora_cita">Time</label>
        <input
          type="time"
          id="hora_cita"
          name="hora_cita"
          required
          value="<?php echo htmlspecialchars($hora_cita); ?>"
        >

        <label for="estado">Status</label>
        <select id="estado" name="estado">
          <option value="pendiente" <?php echo $estado === "pendiente" ? "selected" : ""; ?>>Pending</option>
          <option value="confirmada" <?php echo $estado === "confirmada" ? "selected" : ""; ?>>Confirmed</option>
          <option value="cancelada" <?php echo $estado === "cancelada" ? "selected" : ""; ?>>Cancelled</option>
        </select>

        <button type="submit"><?php echo $id_cita > 0 ? "Update appointment" : "Select one appointment to edit"; ?></button>
      </form>

      <section class="appointments-section">
        <div class="appointments-card">
          <h2>All appointments</h2>

          <?php if (empty($appointments)) : ?>
            <p class="appointments-empty">There are no appointments yet.</p>
          <?php else : ?>
            <div class="appointments-table-wrap">
              <table class="appointments-table">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Username</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $appointment) : ?>
                    <tr>
                      <td><?php echo htmlspecialchars($appointment["nombre"] . " " . $appointment["apellidos"]); ?></td>
                      <td><?php echo htmlspecialchars($appointment["username"]); ?></td>
                      <td><?php echo htmlspecialchars($appointment["titulo"]); ?></td>
                      <td><?php echo htmlspecialchars($appointment["fecha_cita"]); ?></td>
                      <td><?php echo htmlspecialchars(substr($appointment["hora_cita"], 0, 5)); ?></td>
                      <td><?php echo htmlspecialchars($appointment["estado"]); ?></td>
                      <td class="appointments-actions">
                        <a href="admin_citas.php?edit=<?php echo (int) $appointment["id_cita"]; ?>" class="appointments-link">Edit</a>
                        <form action="admin_citas.php" method="POST" class="appointments-inline-form">
                          <input type="hidden" name="form_type" value="delete">
                          <input type="hidden" name="id_cita" value="<?php echo (int) $appointment["id_cita"]; ?>">
                          <button type="submit" class="appointments-delete">Delete</button>
                        </form>
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
