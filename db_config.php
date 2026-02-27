<?php
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "utulek";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}
?>