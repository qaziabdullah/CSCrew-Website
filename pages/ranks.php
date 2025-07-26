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

$state = $pdo->query(
    "SELECT name, value, rank, kills, deaths, headshots, assists, " .
    "shoots, hits, round_win, round_lose, playtime " .
    "FROM lvl_base ORDER BY rank DESC, value DESC"
);
$ranks = $state->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= GetPrefix(); ?>src/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= GetPrefix(); ?>css/main.css" type="text/css">
    <link rel="stylesheet" href="<?= GetPrefix(); ?>css/rank.css" type="text/css">
    <title><?= $translations->website_name; ?> - <?= $translations->ranks->title ?? 'Ranks'; ?></title>
</head>
<body <?= $bodyStyle ?? "" ?>>

<div class="wrapper">
    <nav>
        <a class="main-btn" href="<?= GetPrefix(); ?>skins"><?= $translations->ranks->back ?? 'Back'; ?></a>
    </nav>
    <h2><?= $translations->ranks->header ?? 'Player Ranks'; ?></h2>
    <input type="text" id="search" class="search-input" placeholder="Search player">
    <table>
        <thead>
            <tr>
                <th data-column="name" class="sortable"><?= $translations->ranks->name ?? 'Name'; ?></th>
                <th data-column="value" class="sortable"><?= $translations->ranks->value ?? 'Rating'; ?></th>
                <th data-column="rank" class="sortable"><?= $translations->ranks->rank ?? 'Rank'; ?></th>
                <th data-column="kills" class="sortable"><?= $translations->ranks->kills ?? 'Kills'; ?></th>
                <th data-column="deaths" class="sortable"><?= $translations->ranks->deaths ?? 'Deaths'; ?></th>
                <th data-column="kd" class="sortable">K/D</th>
            </tr>
        </thead>
        <tbody id="rank-body"></tbody>
    </table>
    <div class="pagination">
        <button id="prevPage">&laquo; Prev</button>
        <span id="pageInfo"></span>
        <button id="nextPage">Next &raquo;</button>
    </div>
</div>

<script>
const players = <?= json_encode($ranks); ?>;
</script>
<script src="<?= GetPrefix(); ?>js/ranks.js"></script>

</body>
</html>