
<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['room'])) {
    $_SESSION['room'] = 1;
    $_SESSION['score'] = 1000;
    $_SESSION['wrong_attempts'] = [];
    $_SESSION['hint_ptr'] = [];
    $_SESSION['start_time'] = time();
}

if ($_SESSION['room'] > 3) {
    header("Location: leaderboard.php");
    exit;
}

$rooms = [
    1 => [
        'title' => 'Room 1: Math Lock',
        'question' => 'What is 12 + 8 × 2?',
        'answer' => '28'
    ],
    2 => [
        'title' => 'Room 2: Secret Code',
        'question' => 'Unscramble the word: T L U A V',
        'answer' => 'VAULT'
    ],
    3 => [
        'title' => 'Room 3: Final Door',
        'question' => 'What has keys but cannot open locks?',
        'answer' => 'PIANO'
    ]
];

$message = "";
$room = $_SESSION['room'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $answer = strtoupper(trim($_POST['answer'] ?? ''));

    if ($answer === '') {
        $message = "Please enter an answer.";
    } elseif ($answer === $rooms[$room]['answer']) {

        $_SESSION['room']++;

        if ($_SESSION['room'] > 3) {

            $_SESSION['finished'] = true;

            $_SESSION['scores'][] = [
                'user' => $_SESSION['user'],
                'score' => $_SESSION['score'],
                'time' => time() - $_SESSION['start_time']
            ];

            header("Location: leaderboard.php");
            exit;
        }

        header("Location: game.php");
        exit;

    } else {

        $_SESSION['wrong_attempts'][$room] =
            ($_SESSION['wrong_attempts'][$room] ?? 0) + 1;

        $_SESSION['score'] = max(
            0,
            $_SESSION['score'] - 50
        );

        $message = "Incorrect answer. Try again! (-50 points)";
    }
}

$room = $_SESSION['room'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>The PHP Vault - Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>The PHP Vault</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8') ?>!
    </p>

    <h2>
        <?= htmlspecialchars($rooms[$room]['title'], ENT_QUOTES, 'UTF-8') ?>
    </h2>

    <p><strong>Score:</strong> <?= (int) $_SESSION['score'] ?></p>
    <p><strong>Room:</strong> <?= $room ?> of 3</p>

    <h3>
        <?= htmlspecialchars($rooms[$room]['question'], ENT_QUOTES, 'UTF-8') ?>
    </h3>

    <?php if ($message !== ''): ?>
        <p style="color: #ff7777;">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="game.php">
        <label for="answer">Your Answer:</label>

        <input type="text" id="answer"
               name="answer" required>

        <button type="submit">Submit Answer</button>
    </form>

    <p><a href="hint.php">Need a Hint?</a></p>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>
