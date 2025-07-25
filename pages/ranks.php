<?php

if(!function_exists("Path")) {
    return;
}

/************/
/* Database */
/************/

$conn = require_once('imports/database.php');
if($conn != 1) {
    $documentError_Code = 'database';
    include_once 'errorpage.php';
    exit;
}

$state = $pdo->query("SELECT name, value, rank, kills, deaths, headshots, assists, round_win, round_lose FROM lvl_base ORDER BY rank ASC, value DESC");
$ranks = $state->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= GetPrefix(); ?>src/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= GetPrefix(); ?>css/main.css" type="text/css">
    <link rel="stylesheet" href="<?= GetPrefix(); ?>css/ranks.css" type="text/css">
    <title><?= $translations->website_name; ?> - <?= $translations->ranks->title ?? 'Ranks'; ?></title>
</head>
<body <?= $bodyStyle ?? "" ?>>

<div class="wrapper">
    <nav>
        <a class="main-btn" href="<?= GetPrefix(); ?>skins"><?= $translations->ranks->back ?? 'Back'; ?></a>
    </nav>
    <h2><?= $translations->ranks->header ?? 'Player Ranks'; ?></h2>
    <table>
        <thead>
            <tr>
                <th><?= $translations->ranks->name ?? 'Name'; ?></th>
                <th><?= $translations->ranks->value ?? 'Value'; ?></th>
                <th><?= $translations->ranks->rank ?? 'Rank'; ?></th>
                <th><?= $translations->ranks->kills ?? 'Kills'; ?></th>
                <th><?= $translations->ranks->deaths ?? 'Deaths'; ?></th>
                <th><?= $translations->ranks->headshots ?? 'Headshots'; ?></th>
                <th><?= $translations->ranks->assists ?? 'Assists'; ?></th>
                <th><?= $translations->ranks->round_win ?? 'Rounds Won'; ?></th>
                <th><?= $translations->ranks->round_lose ?? 'Rounds Lost'; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($ranks as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['name']); ?></td>
                <td><?= htmlspecialchars($r['value']); ?></td>
                <td><?= htmlspecialchars($r['rank']); ?></td>
                <td><?= htmlspecialchars($r['kills']); ?></td>
                <td><?= htmlspecialchars($r['deaths']); ?></td>
                <td><?= htmlspecialchars($r['headshots']); ?></td>
                <td><?= htmlspecialchars($r['assists']); ?></td>
                <td><?= htmlspecialchars($r['round_win']); ?></td>
                <td><?= htmlspecialchars($r['round_lose']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>