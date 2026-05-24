<?php
require_once __DIR__ . "/includes/conexion.php";
require_once __DIR__ . "/includes/session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$errors = [];
$success_message = "";

$titulo = "";
$descripcion = "";
$fecha_cita = "";
$hora_cita = "";
$edit_id = 0;

function isFutureAppointment(string $date, string $time): bool
{
    $appointment_timestamp = strtotime($date . " " . $time);
    return $appointment_timestamp !== false && $appointment_timestamp > time();
}

function getAppointmentById(mysqli $conn, int $id_cita, int $user_id): ?array
{
    $sql = "SELECT * FROM citas WHERE id_cita = ? AND id_user = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "ii", $id_cita, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $appointment = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $appointment ?: null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_type = $_POST["form_type"] ?? "create";

    if ($form_type === "delete") {
        $delete_id = (int) ($_POST["id_cita"] ?? 0);
        $appointment = getAppointmentById($conn, $delete_id, $user_id);

        if (!$appointment) {
            $errors[] = "Appointment not found.";
        } elseif (!isFutureAppointment($appointment["fecha_cita"], $appointment["hora_cita"])) {
            $errors[] = "You can only delete future appointments.";
        } else {
            $sql_delete = "DELETE FROM citas WHERE id_cita = ? AND id_user = ?";
            $stmt_delete = mysqli_prepare($conn, $sql_delete);

            if ($stmt_delete) {
                mysqli_stmt_bind_param($stmt_delete, "ii", $delete_id, $user_id);

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
    }

    if ($form_type === "create" || $form_type === "update") {
        $titulo = trim($_POST["titulo"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $fecha_cita = $_POST["fecha_cita"] ?? "";
        $hora_cita = $_POST["hora_cita"] ?? "";
        $edit_id = (int) ($_POST["id_cita"] ?? 0);

        if ($titulo === "" || mb_strlen($titulo) > 100) {
            $errors[] = "Please enter a valid title.";
        }

        if (mb_strlen($descripcion) > 1000) {
            $errors[] = "Description is too long.";
        }

        if ($fecha_cita === "" || $hora_cita === "") {
            $errors[] = "Please select a valid date and time.";
        } elseif (!isFutureAppointment($fecha_cita, $hora_cita)) {
            $errors[] = "Appointments must be scheduled for a future date and time.";
        }

        if ($form_type === "update" && empty($errors)) {
            $appointment = getAppointmentById($conn, $edit_id, $user_id);

            if (!$appointment) {
                $errors[] = "Appointment not found.";
            } elseif (!isFutureAppointment($appointment["fecha_cita"], $appointment["hora_cita"])) {
                $errors[] = "You can only edit future appointments.";
            }
        }

        if (empty($errors) && $form_type === "create") {
            $estado = "pendiente";
            $motivo_cita = $descripcion !== "" ? $descripcion : $titulo;
            $sql_insert = "INSERT INTO citas (id_user, titulo, descripcion, motivo_cita, fecha_cita, hora_cita, estado)
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);

            if ($stmt_insert) {
                mysqli_stmt_bind_param($stmt_insert, "issssss", $user_id, $titulo, $descripcion, $motivo_cita, $fecha_cita, $hora_cita, $estado);

                if (mysqli_stmt_execute($stmt_insert)) {
                    $success_message = "Appointment created successfully.";
                    $titulo = "";
                    $descripcion = "";
                    $fecha_cita = "";
                    $hora_cita = "";
                } else {
                    $errors[] = "Could not create the appointment.";
                }

                mysqli_stmt_close($stmt_insert);
            } else {
                $errors[] = "Could not prepare the insert query.";
            }
        }

        if (empty($errors) && $form_type === "update") {
            $appointment_status = $appointment["estado"] ?? "pendiente";
            $motivo_cita = $descripcion !== "" ? $descripcion : $titulo;
            $sql_update = "UPDATE citas
                           SET titulo = ?, descripcion = ?, motivo_cita = ?, fecha_cita = ?, hora_cita = ?, estado = ?
                           WHERE id_cita = ? AND id_user = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);

            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "ssssssii", $titulo, $descripcion, $motivo_cita, $fecha_cita, $hora_cita, $appointment_status, $edit_id, $user_id);

                if (mysqli_stmt_execute($stmt_update)) {
                    $success_message = "Appointment updated successfully.";
                    $titulo = "";
                    $descripcion = "";
                    $fecha_cita = "";
                    $hora_cita = "";
                    $edit_id = 0;
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
    $edit_id = (int) $_GET["edit"];
    $appointment = getAppointmentById($conn, $edit_id, $user_id);

    if (!$appointment) {
        $errors[] = "Appointment not found.";
        $edit_id = 0;
    } elseif (!isFutureAppointment($appointment["fecha_cita"], $appointment["hora_cita"])) {
        $errors[] = "You can only edit future appointments.";
        $edit_id = 0;
    } else {
        $titulo = $appointment["titulo"];
        $descripcion = $appointment["descripcion"] ?? "";
        $fecha_cita = $appointment["fecha_cita"];
        $hora_cita = substr($appointment["hora_cita"], 0, 5);
    }
}

$appointments = [];
$sql_list = "SELECT * FROM citas WHERE id_user = ? ORDER BY fecha_cita ASC, hora_cita ASC";
$stmt_list = mysqli_prepare($conn, $sql_list);

if ($stmt_list) {
    mysqli_stmt_bind_param($stmt_list, "i", $user_id);
    mysqli_stmt_execute($stmt_list);
    $result_list = mysqli_stmt_get_result($stmt_list);

    while ($row = mysqli_fetch_assoc($result_list)) {
        $appointments[] = $row;
    }

    mysqli_stmt_close($stmt_list);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments | Polyglot Lab</title>

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
    $current_page = "citas";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form class="formulario" action="citas.php" method="POST" novalidate>
        <h2><?php echo $edit_id > 0 ? "Edit appointment" : "My appointments"; ?></h2>

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

        <input type="hidden" name="form_type" value="<?php echo $edit_id > 0 ? "update" : "create"; ?>">
        <input type="hidden" name="id_cita" value="<?php echo (int) $edit_id; ?>">

        <label for="titulo">Title</label>
        <input
          type="text"
          id="titulo"
          name="titulo"
          placeholder="Type the appointment title"
          required
          value="<?php echo htmlspecialchars($titulo); ?>"
        >

        <label for="descripcion">Description</label>
        <textarea
          id="descripcion"
          name="descripcion"
          rows="5"
          placeholder="Add details about the appointment"
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

        <button type="submit"><?php echo $edit_id > 0 ? "Update appointment" : "Create appointment"; ?></button>
      </form>

      <section class="appointments-section">
        <div class="appointments-card">
          <h2>Appointment list</h2>

          <?php if (empty($appointments)) : ?>
            <p class="appointments-empty">You do not have appointments yet.</p>
          <?php else : ?>
            <div class="appointments-table-wrap">
              <table class="appointments-table">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $appointment) : ?>
                    <?php $can_manage = isFutureAppointment($appointment["fecha_cita"], $appointment["hora_cita"]); ?>
                    <tr>
                      <td><?php echo htmlspecialchars($appointment["titulo"]); ?></td>
                      <td><?php echo htmlspecialchars($appointment["fecha_cita"]); ?></td>
                      <td><?php echo htmlspecialchars(substr($appointment["hora_cita"], 0, 5)); ?></td>
                      <td><?php echo htmlspecialchars($appointment["estado"]); ?></td>
                      <td class="appointments-actions">
                        <?php if ($can_manage) : ?>
                          <a href="citas.php?edit=<?php echo (int) $appointment["id_cita"]; ?>" class="appointments-link">Edit</a>
                          <form action="citas.php" method="POST" class="appointments-inline-form">
                            <input type="hidden" name="form_type" value="delete">
                            <input type="hidden" name="id_cita" value="<?php echo (int) $appointment["id_cita"]; ?>">
                            <button type="submit" class="appointments-delete">Delete</button>
                          </form>
                        <?php else : ?>
                          <span class="appointments-locked">Past appointment</span>
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
