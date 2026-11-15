
<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['room']) || $_SESSION['room'] > 3) {
    header("Location: game.php");
    exit;
}

$room = $_SESSION['room'];

$_SESSION['hint_ptr'] ??= [];
$_SESSION['wrong_attempts'] ??= [];
$_SESSION['lockout_until'] ??= [];

$hints = [
    1 => [
        "Remember the order of operations.",
        "Multiplication comes before addition.",
        "Multiply 8 by 2 first, then add 12."
    ],
    2 => [
        "The answer is related to the game.",
        "It is something you are trying to escape.",
        "The word starts with V and ends with T."
    ],
    3 => [
        "Think about something that makes music.",
        "It is a musical instrument.",
        "It has black and white keys."
    ]
];

$message = "";
$hint = "";
$now = time();

if (
    ($_SESSION['wrong_attempts'][$room] ?? 0) >= 3 &&
    !isset($_SESSION['lockout_until'][$room])
) {
    $_SESSION['lockout_until'][$room] = $now + 30;
}

$remaining = max(
    0,
    ($_SESSION['lockout_until'][$room] ?? 0) - $now
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($remaining > 0) {
        $message = "Security alert! Try again in $remaining seconds.";
    } else {

        $index = $_SESSION['hint_ptr'][$room] ?? 0;

        if ($index >= count($hints[$room])) {
            $message = "You have used all the hints for this room.";
        } else {

            $hint = $hints[$room][$index];

            $_SESSION['hint_ptr'][$room] = $index + 1;

            $_SESSION['score'] = max(
                0,
                ($_SESSION['score'] ?? 1000) - 100
            );

            $message = "Hint unlocked! (-100 points)";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Security System - The PHP Vault</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>The PHP Vault</h1>
    <h2>AI Security System</h2>

    <p>Room <?= $room ?> of 3</p>
    <p>Current Score: <?= (int) ($_SESSION['score'] ?? 1000) ?></p>

    <?php if ($remaining > 0): ?>
        <p style="color: #ff7777;">
            Suspicious Activity Detected!
            Hint system temporarily locked.
            Try again in <?= $remaining ?> seconds.
        </p>
    <?php endif; ?>

    <?php if ($message !== ""): ?>
        <p>
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($hint !== ""): ?>
        <h3>Your Hint:</h3>
        <p>
            <?= htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if (
        $remaining === 0 &&
        ($_SESSION['hint_ptr'][$room] ?? 0) < 3
    ): ?>
        <form method="POST" action="hint.php">
            <button type="submit">
                Get Hint (-100 Points)
            </button>
        </form>
    <?php endif; ?>

    <p><a href="game.php">Back to Puzzle</a></p>

</body>
</html>
