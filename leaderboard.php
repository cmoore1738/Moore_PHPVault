
<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$scores = $_SESSION['scores'] ?? [];

usort($scores, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});

$username = $_SESSION['user'] ?? 'Player';
$finished = $_SESSION['finished'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - The PHP Vault</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>The PHP Vault</h1>
    <h2>Leaderboard</h2>

    <?php if ($finished): ?>

        <h3>Congratulations! You Escaped the Vault!</h3>

        <p>
            Player:
            <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            Final Score:
            <?= (int) ($_SESSION['score'] ?? 0) ?>
        </p>

    <?php endif; ?>

    <?php if (empty($scores)): ?>

        <p>No scores yet. Complete the game to appear here!</p>

    <?php else: ?>

        <table>
            <tr>
                <th>Rank</th>
                <th>Player</th>
                <th>Score</th>
                <th>Time (seconds)</th>
            </tr>

            <?php foreach ($scores as $rank => $entry): ?>
                <tr>
                    <td><?= $rank + 1 ?></td>

                    <td>
                        <?= htmlspecialchars(
                            $entry['user'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td><?= (int) $entry['score'] ?></td>

                    <td><?= (int) $entry['time'] ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php endif; ?>

    <p><a href="restart.php">Play Again</a></p>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>
