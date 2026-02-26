<?php
require_once 'db_config.php';

function s($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Načtení zvířete
$zvireId = (int)($_GET['id'] ?? 0);
$zvire = null;

if ($zvireId > 0) {
    $stmt = $conn->prepare("SELECT * FROM zvirata WHERE id = ? AND dostupne = 1");
    $stmt->bind_param("i", $zvireId);
    $stmt->execute();
    $result = $stmt->get_result();
    $zvire = $result->fetch_assoc();
    $stmt->close();
}

if (!$zvire) {
    header("Location: index.php");
    exit;
}

$druhyMap = [
    'pes' => 'Pes', 'kocka' => 'Kočka', 'kralik' => 'Králík',
    'zelva' => 'Želva', 'papousek' => 'Papoušek', 'morcata' => 'Morče',
    'krecek' => 'Křeček', 'ryba' => 'Ryba',
];
?>
    <!DOCTYPE html>
    <html lang="cs">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PawAdopt | Adopce – <?= s($zvire['jmeno']) ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

    <div class="background-blob"></div>

    <main class="container">
        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Zpět na přehled</a>

        <!-- Detail zvířete -->
        <div class="adopt-detail">
            <div class="adopt-image">
                <img src="<?= s($zvire['foto_url']) ?>" alt="<?= s($zvire['jmeno']) ?>">
            </div>
            <div class="adopt-info">
                <h1><?= s($zvire['jmeno']) ?></h1>
                <span class="adopt-breed"><?= s($zvire['plemeno'] ?? '') ?></span>
                <div class="adopt-meta">
                    <span><i class="fa-solid fa-paw"></i> <?= s($druhyMap[$zvire['druh']] ?? $zvire['druh']) ?></span>
                    <span><i class="fa-regular fa-calendar"></i> <?= s($zvire['vek']) ?></span>
                    <span><i class="fa-solid fa-weight-scale"></i> <?= s($zvire['vaha'] ?? '?') ?></span>
                    <span><i class="fa-solid <?= $zvire['pohlavi'] === 'samec' ? 'fa-mars' : 'fa-venus' ?>"></i> <?= $zvire['pohlavi'] === 'samec' ? 'Samec' : 'Samice' ?></span>
                </div>
                <p class="adopt-desc"><?= s($zvire['popis'] ?? '') ?></p>
            </div>
        </div>

        <!-- Formulář -->
        <div class="form-card">
            <h2 class="form-title"><i class="fa-solid fa-heart"></i> Žádost o adopci – <?= s($zvire['jmeno']) ?></h2>
            <form action="process.php" method="POST" id="adoptionForm">
                <input type="hidden" name="zvire_id" value="<?= (int)$zvire['id'] ?>">
                <input type="hidden" name="druh" value="<?= s($zvire['druh']) ?>">

                <div class="form-grid">
                    <div class="input-group">
                        <label for="jmeno"><i class="fa-solid fa-user"></i> Celé jméno</label>
                        <input type="text" id="jmeno" name="jmeno" required placeholder="Např. Marek Svoboda">
                        <span class="error-msg">Prosím zadejte jméno</span>
                    </div>

                    <div class="input-group">
                        <label for="email"><i class="fa-solid fa-envelope"></i> E-mailová adresa</label>
                        <input type="email" id="email" name="email" required placeholder="marek@priklad.cz">
                        <span class="error-msg">Zadejte platný e-mail</span>
                    </div>

                    <div class="input-group full-width">
                        <label><i class="fa-solid fa-graduation-cap"></i> Vaše zkušenosti se zvířaty</label>
                        <div class="radio-cards">
                            <label class="radio-card">
                                <input type="radio" name="zkusenosti" value="zacatecnik" checked>
                                <div class="card-content">
                                    <i class="fa-solid fa-seedling"></i>
                                    <span>Začátečník</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="zkusenosti" value="pokrocily">
                                <div class="card-content">
                                    <i class="fa-solid fa-medal"></i>
                                    <span>Pokročilý</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="zkusenosti" value="expert">
                                <div class="card-content">
                                    <i class="fa-solid fa-trophy"></i>
                                    <span>Expert</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="input-group full-width">
                        <label for="poznamka"><i class="fa-solid fa-comment-dots"></i> Proč právě vy?</label>
                        <textarea id="poznamka" name="poznamka" rows="4" placeholder="Napište nám něco o sobě a vašem domově..."></textarea>
                    </div>

                    <div class="input-group full-width checkbox-group">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="souhlas" required>
                            <span class="checkmark"></span>
                            Souhlasím se zpracováním osobních údajů a podmínkami adopce.
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Odeslat žádost</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </main>

    <script src="formValidation.js"></script>
    </body>
    </html>
<?php $conn->close(); ?>