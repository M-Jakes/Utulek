<?php
$host = "sql102.infinityfree.com";
$db_user = "if0_41258267"; // Výchozí v XAMPP
$db_pass = "Komarno39";     // Výchozí v XAMPP je prázdné
$db_name = "if0_41258267_XXX";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}
?>