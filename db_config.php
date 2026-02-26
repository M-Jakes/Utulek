<?php
$host = "localhost";
$db_user = "root"; // Výchozí v XAMPP
$db_pass = "";     // Výchozí v XAMPP je prázdné
$db_name = "utulek";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}
?>