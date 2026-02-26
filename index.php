<?php
require_once 'db_config.php';

// Filtr podle druhu zvířete
$filtr = $_GET['filtr'] ?? 'vse';
$allowedFilters = ['vse', 'pes', 'kocka', 'kralik', 'exot'];
if (!in_array($filtr, $allowedFilters)) {
    $filtr = 'vse';
}

// Načtení zvířat z DB
if ($filtr === 'vse') {
    $sql = "SELECT * FROM zvirata WHERE dostupne = 1 ORDER BY pridano DESC";
    $stmt = $conn->prepare($sql);
} elseif ($filtr === 'exot') {
    $sql = "SELECT * FROM zvirata WHERE dostupne = 1 AND druh NOT IN ('pes', 'kocka', 'kralik') ORDER BY pridano DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM zvirata WHERE dostupne = 1 AND druh = ? ORDER BY pridano DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filtr);
}
$stmt->execute();
$result = $stmt->get_result();
$zvirata = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Mapování druhů na české názvy a ikony
$druhyMap = [
        'pes' => ['název' => 'Pes', 'ikona' => 'fa-dog'],
        'kocka' => ['název' => 'Kočka', 'ikona' => 'fa-cat'],
        'kralik' => ['název' => 'Králík', 'ikona' => 'fa-rabbit'],
        'zelva' => ['název' => 'Želva', 'ikona' => 'fa-turtle'],
        'papousek' => ['název' => 'Papoušek', 'ikona' => 'fa-dove'],
        'morcata' => ['název' => 'Morče', 'ikona' => 'fa-paw'],
        'krecek' => ['název' => 'Křeček', 'ikona' => 'fa-paw'],
        'ryba' => ['název' => 'Ryba', 'ikona' => 'fa-fish'],
];

function s($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function getDruhLabel($druh, $map) {
    return $map[$druh]['název'] ?? ucfirst($druh);
}

function getDruhIcon($druh, $map) {
    return $map[$druh]['ikona'] ?? 'fa-paw';
}
?>
    <!DOCTYPE html>
    <html lang="cs">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PawAdopt | Najděte svého parťáka</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

    <div class="background-blob"></div>
    <div class="background-blob blob-2"></div>

    <main class="container">
        <!-- Hero sekce -->
        <section class="hero">
            <h1><i class="fa-solid fa-paw"></i> Darujte <span class="highlight">nový domov</span></h1>
            <p>Prohlédněte si naše zvířátka a najděte svého nejlepšího kamaráda. Každé z nich si zaslouží milující rodinu.</p>
        </section>

        <!-- Filtrování -->
        <section class="filter-bar">
            <a href="?filtr=vse" class="filter-btn <?= $filtr === 'vse' ? 'active' : '' ?>">
                <i class="fa-solid fa-border-all"></i> Všechna
            </a>
            <a href="?filtr=pes" class="filter-btn <?= $filtr === 'pes' ? 'active' : '' ?>">
                <i class="fa-solid fa-dog"></i> Psi
            </a>
            <a href="?filtr=kocka" class="filter-btn <?= $filtr === 'kocka' ? 'active' : '' ?>">
                <i class="fa-solid fa-cat"></i> Kočky
            </a>
            <a href="?filtr=kralik" class="filter-btn <?= $filtr === 'kralik' ? 'active' : '' ?>">
                <i class="fa-solid fa-rabbit"></i> Králíci
            </a>
            <a href="?filtr=exot" class="filter-btn <?= $filtr === 'exot' ? 'active' : '' ?>">
                <i class="fa-solid fa-fish"></i> Exotická
            </a>
        </section>

        <!-- Mřížka zvířat -->
        <section class="animals-grid">
            <?php if (empty($zvirata)): ?>
                <div class="no-results">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <h2>Žádná zvířátka nenalezena</h2>
                    <p>Zkuste změnit filtr nebo se vraťte později.</p>
                </div>
            <?php else: ?>
                <?php foreach ($zvirata as $zvire): ?>
                    <div class="animal-card">
                        <div class="card-image">
                            <img src="<?= s($zvire['foto_url']) ?>" alt="<?= s($zvire['jmeno']) ?>" loading="lazy">
                            <span class="card-badge">
                            <i class="fa-solid <?= getDruhIcon($zvire['druh'], $druhyMap) ?>"></i>
                            <?= s(getDruhLabel($zvire['druh'], $druhyMap)) ?>
                        </span>
                            <span class="card-gender <?= $zvire['pohlavi'] === 'samec' ? 'male' : 'female' ?>">
                            <i class="fa-solid <?= $zvire['pohlavi'] === 'samec' ? 'fa-mars' : 'fa-venus' ?>"></i>
                        </span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-name"><?= s($zvire['jmeno']) ?></h3>
                            <p class="card-breed"><?= s($zvire['plemeno'] ?? '') ?></p>
                            <div class="card-details">
                                <span><i class="fa-regular fa-calendar"></i> <?= s($zvire['vek']) ?></span>
                                <span><i class="fa-solid fa-weight-scale"></i> <?= s($zvire['vaha'] ?? '?') ?></span>
                            </div>
                            <p class="card-desc"><?= s($zvire['popis'] ?? '') ?></p>
                            <a href="adopt.php?id=<?= (int)$zvire['id'] ?>" class="btn-adopt">
                                <i class="fa-solid fa-heart"></i> Chci adoptovat
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Statistiky -->
        <section class="stats-bar">
            <div class="stat-item">
                <i class="fa-solid fa-heart"></i>
                <span class="stat-number"><?= count($zvirata) ?></span>
                <span class="stat-label">Zvířátek čeká</span>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-house"></i>
                <span class="stat-number">150+</span>
                <span class="stat-label">Úspěšných adopcí</span>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-face-smile"></i>
                <span class="stat-number">98%</span>
                <span class="stat-label">Spokojených rodin</span>
            </div>
        </section>
    </main>

    </body>
    </html>
<?php $conn->close(); ?>