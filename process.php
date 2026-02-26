<?php
require_once 'db_config.php';

function s($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = trim($_POST['jmeno'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $druh = $_POST['druh'] ?? '';
    $zkusenosti = $_POST['zkusenosti'] ?? '';
    $poznamka = trim($_POST['poznamka'] ?? '');
    $souhlas = isset($_POST['souhlas']);
    $zvireId = (int)($_POST['zvire_id'] ?? 0);

    // --- SERVEROVÁ VALIDACE ---
    $chyby = [];

    if (empty($jmeno)) $chyby[] = "Jméno a příjmení musí být vyplněno.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $chyby[] = "Zadaný e-mail nemá správný formát.";
    if (empty($druh)) $chyby[] = "Musíte si vybrat, o jaké zvířátko máte zájem.";
    if (!$souhlas) $chyby[] = "Musíte souhlasit s podmínkami adopce.";

    if (!empty($chyby)) {
        display_message(false, "Něco se nepovedlo", $chyby);
        exit;
    }

    // --- ULOŽENÍ DO DATABÁZE ---
    try {
        $sql = "INSERT INTO adopce (jmeno, email, druh_zvirate, zkusenosti, poznamka, zvire_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $jmeno, $email, $druh, $zkusenosti, $poznamka, $zvireId);

        if ($stmt->execute()) {
            // Načtení jména zvířete pro hezčí zprávu
            $zvireJmeno = '';
            if ($zvireId > 0) {
                $stmtZ = $conn->prepare("SELECT jmeno FROM zvirata WHERE id = ?");
                $stmtZ->bind_param("i", $zvireId);
                $stmtZ->execute();
                $resZ = $stmtZ->get_result();
                $rowZ = $resZ->fetch_assoc();
                if ($rowZ) {
                    $zvireJmeno = $rowZ['jmeno'];
                }
                $stmtZ->close();
            }

            $msg = "Děkujeme, " . s($jmeno) . "!";
            if ($zvireJmeno) {
                $msg .= " Vaše žádost o adopci zvířátka <strong>" . s($zvireJmeno) . "</strong> byla přijata.";
            }
            $msg .= " Ozveme se vám na <strong>" . s($email) . "</strong>.";

            display_message(true, "Žádost odeslána! 🎉", [$msg]);
        } else {
            throw new Exception($conn->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        display_message(false, "Chyba databáze", ["Omlouváme se, ale data se nepodařilo uložit: " . $e->getMessage()]);
    }

    $conn->close();
}

function display_message($success, $title, $messages) {
    $icon = $success ? 'fa-circle-check' : 'fa-circle-exclamation';
    $iconColor = $success ? '#22c55e' : '#ef4444';
    ?>
    <!DOCTYPE html>
    <html lang="cs">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PawAdopt | <?= s($title) ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="background-blob"></div>
    <main class="container">
        <div class="form-card result-card">
            <i class="fa-solid <?= $icon ?>" style="font-size: 4rem; color: <?= $iconColor ?>; margin-bottom: 20px;"></i>
            <h1 style="font-size: 2rem; margin-bottom: 15px;"><?= $title ?></h1>
            <?php foreach ($messages as $msg): ?>
                <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 10px;"><?= $msg ?></p>
            <?php endforeach; ?>
            <a href="index.php" class="btn-submit" style="display: inline-flex; margin-top: 30px; text-decoration: none; max-width: 350px;">
                <i class="fa-solid fa-paw"></i>
                <span>Zpět na přehled zvířátek</span>
            </a>
        </div>
    </main>
    </body>
    </html>
    <?php
}