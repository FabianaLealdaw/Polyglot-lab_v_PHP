<?php
require_once __DIR__ . "/includes/conexion.php";

$news_items = [];
$sql_news = "SELECT n.id_noticia, n.titulo, n.contenido, n.imagen, n.fecha_publicacion,
                    COALESCE(NULLIF(n.autor_nombre, ''), TRIM(CONCAT(ud.nombre, ' ', ud.apellidos)), 'Polyglot Lab Admin') AS autor_mostrar
             FROM noticias n
             LEFT JOIN users_data ud ON ud.id_user = n.id_user
             ORDER BY n.fecha_publicacion DESC, n.id_noticia DESC";
$result_news = mysqli_query($conn, $sql_news);

if ($result_news) {
    while ($row = mysqli_fetch_assoc($result_news)) {
        $news_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias | Polyglot Lab</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  </head>
  <body>
    <?php
    $base_path = "";
    $current_page = "noticias";
    ?>
    <?php include("includes/navbar.php"); ?>

    <main class="news-page">
      <section class="news-list-section">
        <div class="news-list-wrapper">
          <h1 class="section-title">News</h1>
          <p class="section-subtitle">All news from the Polyglot Lab database</p>

          <div class="news-full-grid">
            <?php foreach ($news_items as $item) : ?>
              <?php
              $formatted_news_date = $item["fecha_publicacion"];
              $news_timestamp = strtotime($item["fecha_publicacion"]);
              if ($news_timestamp !== false) {
                  $formatted_news_date = date("d F Y", $news_timestamp);
              }

              $image_path = trim((string) ($item["imagen"] ?? ""));
              if ($image_path === "") {
                  $image_path = "./assets/images/gallery/clase1.jpg";
              }

              $author_name = trim((string) ($item["autor_mostrar"] ?? ""));
              if ($author_name === "") {
                  $author_name = "Polyglot Lab Admin";
              }
              ?>
              <article class="news-full-card">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($item["titulo"]); ?>" class="news-full-image">
                <div class="news-full-content">
                  <h2><?php echo htmlspecialchars($item["titulo"]); ?></h2>
                  <div class="news-full-meta">
                    <p class="news-detail-date"><?php echo htmlspecialchars($formatted_news_date); ?></p>
                    <p class="news-full-author">Created by: <?php echo htmlspecialchars($author_name); ?></p>
                  </div>
                  <p><?php echo nl2br(htmlspecialchars($item["contenido"])); ?></p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>

    <?php include("includes/footer.php"); ?>
  </body>
</html>
