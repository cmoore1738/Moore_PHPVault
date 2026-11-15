
<?php
session_start();

$error = "";
$success = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($username === "" || $password === "" || $confirm === "") {
        $error = "Please fill out all fields.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 letters, numbers, or underscores.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {

        $usersFile =  "/home/cmoore134/phpvault_private/users.json";
        $users = [];

        if (file_exists($usersFile)) {
            $users = json_decode(
                file_get_contents($usersFile),
                true
            ) ?? [];
        }

        if (isset($users[$username])) {
            $error = "Username already exists.";
        } else {
            $users[$username] = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            if (file_put_contents(
                $usersFile,
                json_encode($users, JSON_PRETTY_PRINT),
                LOCK_EX
            ) === false) {
                $error = "Unable to save account.";
            } else {
                $success = "Account created! You can now log in.";
                $username = "";
            }
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
    <title>Register - The PHP Vault</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>The PHP Vault</h1>
    <h2>Create an Account</h2>

    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <p style="color: lightgreen;">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p><a href="login.php">Go to Login</a></p>
    <?php endif; ?>

    <form method="POST" action="register.php">

        <label for="username">Username:</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
               required>

        <label for="password">Password:</label>
        <input type="password" id="password"
               name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password"
               name="confirm_password" required>

        <button type="submit">Register</button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login here</a>
    </p>

    <p><a href="index.php">Back to Home</a></p>

</body>
</html>
