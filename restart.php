
<?php
session_start();

// Make sure the player is logged in.
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

// Reset the game but keep the player logged in.
$_SESSION['room'] = 1;
$_SESSION['score'] = 1000;
$_SESSION['wrong_attempts'] = [];
$_SESSION['hint_ptr'] = [];
$_SESSION['lockout_until'] = [];
$_SESSION['start_time'] = time();
$_SESSION['finished'] = false;

// Keep the leaderboard scores.
header("Location: game.php");
exit;
