<?php
require_once __DIR__ . "/session.php";

$base_path = $base_path ?? "";
$current_page = $current_page ?? "";

$is_logged_in = isset($_SESSION["user_id"]);
$is_admin = $is_logged_in && (($_SESSION["role"] ?? "") === "admin");

$home_href = $current_page === "home" ? "#inicio" : $base_path . "index.php";
$logo_src = $base_path . "assets/images/Logo_PL.png";
?>
<header>
  <nav class="navbar" aria-label="Primary navigation">
    <a href="<?php echo $home_href; ?>" class="<?php echo $current_page === "home" ? "active" : ""; ?>">Home</a>
    <a href="<?php echo $base_path; ?>noticias.php" class="<?php echo $current_page === "noticias" ? "active" : ""; ?>">News</a>
    <a href="<?php echo $base_path; ?>views/courses.php" class="<?php echo $current_page === "courses" ? "active" : ""; ?>">Courses</a>
    <a href="<?php echo $base_path; ?>views/gallery.php" class="<?php echo $current_page === "gallery" ? "active" : ""; ?>">Gallery</a>
    <a href="<?php echo $base_path; ?>views/contact.php" class="<?php echo $current_page === "contact" ? "active" : ""; ?>">Contact</a>
    <a href="<?php echo $base_path; ?>views/get_a_quote.php" class="<?php echo $current_page === "quote" ? "active" : ""; ?>">Get a Quote</a>

    <?php if (!$is_logged_in) : ?>
      <a href="<?php echo $base_path; ?>login.php" class="<?php echo $current_page === "login" ? "active" : ""; ?>">Login</a>
    <?php else : ?>
      <?php if ($is_admin) : ?>
        <a href="<?php echo $base_path; ?>admin_noticias.php" class="<?php echo $current_page === "admin_noticias" ? "active" : ""; ?>">Admin news</a>
        <a href="<?php echo $base_path; ?>admin_citas.php" class="<?php echo $current_page === "admin_citas" ? "active" : ""; ?>">Admin appointments</a>
        <a href="<?php echo $base_path; ?>admin_users.php" class="<?php echo $current_page === "admin_users" ? "active" : ""; ?>">Admin users</a>
      <?php endif; ?>
      <a href="<?php echo $base_path; ?>citas.php" class="<?php echo $current_page === "citas" ? "active" : ""; ?>">Appointments</a>
      <a href="<?php echo $base_path; ?>profile.php" class="<?php echo $current_page === "profile" ? "active" : ""; ?>">Profile</a>
    <?php endif; ?>

    <a class="nav-logo" href="<?php echo $home_href; ?>">
      <img src="<?php echo $logo_src; ?>" alt="Polyglot Lab">
    </a>

    <?php if (!$is_logged_in) : ?>
      <a href="<?php echo $base_path; ?>register.php" class="nav-cta">Start now</a>
    <?php else : ?>
      <a href="<?php echo $base_path; ?>logout.php" class="nav-cta">Log out</a>
    <?php endif; ?>
  </nav>
</header>
<script>
  (function () {
    const root = document.documentElement;
    const header = document.querySelector("header");
    const navbar = header ? header.querySelector(".navbar") : null;

    if (!root || !header || !navbar) return;

    const updateHeaderHeight = () => {
      const headerStyles = window.getComputedStyle(header);
      const paddingTop = parseFloat(headerStyles.paddingTop) || 0;
      const paddingBottom = parseFloat(headerStyles.paddingBottom) || 0;
      const realHeight = Math.ceil(navbar.scrollHeight + paddingTop + paddingBottom);

      root.style.setProperty("--header-height", `${realHeight}px`);
    };

    updateHeaderHeight();
    window.addEventListener("load", updateHeaderHeight);
    window.addEventListener("resize", updateHeaderHeight);
  })();
</script>
