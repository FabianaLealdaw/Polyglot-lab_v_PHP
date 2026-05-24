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

$titulo = "";
$resumen = "";
$contenido = "";
$imagen = "";
$fecha_publicacion = date("Y-m-d");
$edit_id = 0;
$autor_nombre = "";
$id_user_autor = (int) ($_SESSION["user_id"] ?? 0);

function getNewsById(mysqli $conn, int $id_noticia): ?array
{
    $sql = "SELECT * FROM noticias WHERE id_noticia = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_noticia);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $news = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $news ?: null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_type = $_POST["form_type"] ?? "create";

    if ($form_type === "delete") {
        $delete_id = (int) ($_POST["id_noticia"] ?? 0);
        $sql_delete = "DELETE FROM noticias WHERE id_noticia = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);

        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "i", $delete_id);

            if (mysqli_stmt_execute($stmt_delete)) {
                $success_message = "News item deleted successfully.";
            } else {
                $errors[] = "Could not delete the news item.";
            }

            mysqli_stmt_close($stmt_delete);
        } else {
            $errors[] = "Could not prepare the delete query.";
        }
    }

    if ($form_type === "create" || $form_type === "update") {
        $titulo = trim($_POST["titulo"] ?? "");
        $resumen = trim($_POST["resumen"] ?? "");
        $contenido = trim($_POST["contenido"] ?? "");
        $imagen = trim($_POST["imagen"] ?? "");
        $fecha_publicacion = $_POST["fecha_publicacion"] ?? date("Y-m-d");
        $edit_id = (int) ($_POST["id_noticia"] ?? 0);

        if ($titulo === "" || mb_strlen($titulo) > 150) {
            $errors[] = "Please enter a valid title.";
        }

        if ($resumen === "") {
            $errors[] = "Please enter a summary.";
        }

        if ($contenido === "") {
            $errors[] = "Please enter the full content.";
        }

        if ($imagen === "") {
            $errors[] = "Please enter an image path or URL.";
        }

        if ($fecha_publicacion === "") {
            $errors[] = "Please select a publication date.";
        }

        if (empty($errors)) {
            $sql_duplicate = "SELECT id_noticia FROM noticias WHERE titulo = ? AND id_noticia != ?";
            $stmt_duplicate = mysqli_prepare($conn, $sql_duplicate);

            if ($stmt_duplicate) {
                mysqli_stmt_bind_param($stmt_duplicate, "si", $titulo, $edit_id);
                mysqli_stmt_execute($stmt_duplicate);
                $duplicate_result = mysqli_stmt_get_result($stmt_duplicate);

                if (mysqli_fetch_assoc($duplicate_result)) {
                    $errors[] = "A news item with that title already exists.";
                }

                mysqli_stmt_close($stmt_duplicate);
            }
        }

        if (empty($errors) && $form_type === "create") {
            $autor_nombre = trim(($_SESSION["nombre"] ?? "") . " " . ($_SESSION["apellidos"] ?? ""));

            if ($autor_nombre === "") {
                $autor_nombre = $_SESSION["username"] ?? "Polyglot Lab Admin";
            }

            $sql_insert = "INSERT INTO noticias (id_user, titulo, resumen, contenido, imagen, autor_nombre, fecha_publicacion)
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);

            if ($stmt_insert) {
                mysqli_stmt_bind_param($stmt_insert, "issssss", $id_user_autor, $titulo, $resumen, $contenido, $imagen, $autor_nombre, $fecha_publicacion);

                if (mysqli_stmt_execute($stmt_insert)) {
                    $success_message = "News item created successfully.";
                    $titulo = "";
                    $resumen = "";
                    $contenido = "";
                    $imagen = "";
                    $fecha_publicacion = date("Y-m-d");
                } else {
                    $errors[] = "Could not create the news item.";
                }

                mysqli_stmt_close($stmt_insert);
            } else {
                $errors[] = "Could not prepare the insert query.";
            }
        }

        if (empty($errors) && $form_type === "update") {
            $sql_update = "UPDATE noticias
                           SET titulo = ?, resumen = ?, contenido = ?, imagen = ?, autor_nombre = ?, fecha_publicacion = ?
                           WHERE id_noticia = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);

            if ($stmt_update) {
                $autor_nombre = trim(($_SESSION["nombre"] ?? "") . " " . ($_SESSION["apellidos"] ?? ""));

                if ($autor_nombre === "") {
                    $autor_nombre = $_SESSION["username"] ?? "Polyglot Lab Admin";
                }

                mysqli_stmt_bind_param($stmt_update, "ssssssi", $titulo, $resumen, $contenido, $imagen, $autor_nombre, $fecha_publicacion, $edit_id);

                if (mysqli_stmt_execute($stmt_update)) {
                    $success_message = "News item updated successfully.";
                    $titulo = "";
                    $resumen = "";
                    $contenido = "";
                    $imagen = "";
                    $fecha_publicacion = date("Y-m-d");
                    $edit_id = 0;
$autor_nombre = "";
                } else {
                    $errors[] = "Could not update the news item.";
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
    $news_item = getNewsById($conn, $edit_id);

    if ($news_item) {
        $titulo = $news_item["titulo"];
        $resumen = $news_item["resumen"];
        $contenido = $news_item["contenido"];
        $imagen = $news_item["imagen"] ?? "";
        $autor_nombre = $news_item["autor_nombre"] ?? "Polyglot Lab Admin";
        $fecha_publicacion = $news_item["fecha_publicacion"];
    } else {
        $errors[] = "News item not found.";
        $edit_id = 0;
$autor_nombre = "";
    }
}

$news_list = [];
$sql_list = "SELECT n.*, COALESCE(NULLIF(n.autor_nombre, ''), TRIM(CONCAT(ud.nombre, ' ', ud.apellidos)), 'Polyglot Lab Admin') AS autor_mostrar
             FROM noticias n
             LEFT JOIN users_data ud ON ud.id_user = n.id_user
             ORDER BY n.fecha_publicacion DESC, n.id_noticia DESC";
$result_list = mysqli_query($conn, $sql_list);

if ($result_list) {
    while ($row = mysqli_fetch_assoc($result_list)) {
        $news_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin News | Polyglot Lab</title>

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
    $current_page = "admin_noticias";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main>
      <form class="formulario" action="admin_noticias.php" method="POST" novalidate>
        <h2><?php echo $edit_id > 0 ? "Edit news" : "Admin news"; ?></h2>

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
        <input type="hidden" name="id_noticia" value="<?php echo (int) $edit_id; ?>">

        <label for="titulo">Title</label>
        <input
          type="text"
          id="titulo"
          name="titulo"
          placeholder="Write the news title"
          required
          value="<?php echo htmlspecialchars($titulo); ?>"
        >

        <label for="resumen">Summary</label>
        <textarea
          id="resumen"
          name="resumen"
          rows="4"
          placeholder="Add a short summary for the news card"
          required
        ><?php echo htmlspecialchars($resumen); ?></textarea>

        <label for="contenido">Content</label>
        <textarea
          id="contenido"
          name="contenido"
          rows="7"
          placeholder="Write the full news content"
          required
        ><?php echo htmlspecialchars($contenido); ?></textarea>

        <label for="imagen">Image path or URL</label>
        <input
          type="text"
          id="imagen"
          name="imagen"
          placeholder="./assets/images/gallery/clase1.jpg or https://..."
          required
          value="<?php echo htmlspecialchars($imagen); ?>"
        >

        <label for="fecha_publicacion">Publication date</label>
        <input
          type="date"
          id="fecha_publicacion"
          name="fecha_publicacion"
          required
          value="<?php echo htmlspecialchars($fecha_publicacion); ?>"
        >

        <button type="submit"><?php echo $edit_id > 0 ? "Update news" : "Create news"; ?></button>
      </form>

      <section class="appointments-section">
        <div class="appointments-card">
          <h2>News list</h2>

          <?php if (empty($news_list)) : ?>
            <p class="appointments-empty">There are no news items yet.</p>
          <?php else : ?>
            <div class="appointments-table-wrap">
              <table class="appointments-table">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Summary</th>
                    <th>Author</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($news_list as $news_item) : ?>
                    <tr>
                      <td><?php echo htmlspecialchars($news_item["titulo"]); ?></td>
                      <td><?php echo htmlspecialchars($news_item["fecha_publicacion"]); ?></td>
                      <td><?php echo htmlspecialchars($news_item["resumen"]); ?></td>
                      <td><?php echo htmlspecialchars($news_item["autor_mostrar"] ?? "Polyglot Lab Admin"); ?></td>
                      <td class="appointments-actions">
                        <a href="admin_noticias.php?edit=<?php echo (int) $news_item["id_noticia"]; ?>" class="appointments-link">Edit</a>
                        <form action="admin_noticias.php" method="POST" class="appointments-inline-form">
                          <input type="hidden" name="form_type" value="delete">
                          <input type="hidden" name="id_noticia" value="<?php echo (int) $news_item["id_noticia"]; ?>">
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
