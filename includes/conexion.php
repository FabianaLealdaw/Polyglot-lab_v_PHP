<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "root";
$db_name = "polyglot_lab";
$db_port = 8889;

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
