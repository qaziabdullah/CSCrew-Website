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
    "FROM lvl_base ORDER BY rank ASC, value DESC"
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
    <table>
        <thead>
            <tr>
                <th><?= $translations->ranks->name ?? 'Name'; ?></th>
                <th><?= $translations->ranks->value ?? 'Value'; ?></th>
                <th><?= $translations->ranks->rank ?? 'Rank'; ?></th>
                <th><?= $translations->ranks->kills ?? 'Kills'; ?></th>
                <th><?= $translations->ranks->deaths ?? 'Deaths'; ?></th>
                <th>K/D</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($ranks as $r):
            $kd = $r['deaths'] > 0 ? $r['kills'] / $r['deaths'] : $r['kills'];
            $accuracy = $r['shoots'] > 0 ? ($r['hits'] / $r['shoots']) * 100 : 0;
            $hsPercent = $r['kills'] > 0 ? ($r['headshots'] / $r['kills']) * 100 : 0;
            $totalRounds = $r['round_win'] + $r['round_lose'];
            $hoursPlayed = $r['playtime'] / 3600;
        ?>
            <tr class="player-summary">
                <td><?= htmlspecialchars($r['name']); ?></td>
                <td><?= htmlspecialchars($r['value']); ?></td>
                <td><img class="rank-img" src="/src/ranks/<?= htmlspecialchars($r['rank']); ?>.png" alt="rank"></td>
                <td><?= htmlspecialchars($r['kills']); ?></td>
                <td><?= htmlspecialchars($r['deaths']); ?></td>
                <td><?= number_format($kd, 2); ?></td>
            </tr>
            <tr class="player-details">
                <td colspan="6">
                    <div class="details">
                        <span class="badge">Shots Fired: <?= htmlspecialchars($r['shoots']); ?></span>
                        <span class="badge">Shots Hit: <?= htmlspecialchars($r['hits']); ?></span>
                        <span class="badge">Accuracy: <?= number_format($accuracy, 2); ?>%</span>
                        <span class="badge">Headshots: <?= htmlspecialchars($r['headshots']); ?> (<?= number_format($hsPercent, 2); ?>%)</span>
                        <span class="badge">Assists: <?= htmlspecialchars($r['assists']); ?></span>
                        <span class="badge">Rounds Won: <?= htmlspecialchars($r['round_win']); ?></span>
                        <span class="badge">Rounds Lost: <?= htmlspecialchars($r['round_lose']); ?></span>
                        <span class="badge">Total Rounds: <?= $totalRounds; ?></span>
                        <span class="badge">Hours Played: <?= number_format($hoursPlayed, 1); ?></span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.player-summary').forEach(function(row){
    row.addEventListener('click', function(){
        var next = row.nextElementSibling;
        if(next && next.classList.contains('player-details')){
            next.classList.toggle('open');
        }
    });
});
</script>

</body>
</html>