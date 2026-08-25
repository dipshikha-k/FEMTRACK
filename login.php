<?php
session_start();

require_once __DIR__ . '/config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $message = "Please enter your email and password.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, password FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];

           $redirect = $_SESSION["redirect_after_login"] ?? "index.php";
            unset($_SESSION["redirect_after_login"]);

           header("Location: index.php");
            exit;
        } else {
            $message = "Invalid email or password.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FemTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <div class="brand">FemTrack</div>
        <h1>Welcome back</h1>
        <p class="lead">Log in to keep your cycle information private, organized, and easy to follow.</p>

        <?php if (!empty($message)): ?>
            <p class="notice"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" class="form-stack">
            <label>Email
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>
            <label>Password
                <input type="password" name="password" placeholder="Your password" required>
            </label>
            <button class="button" type="submit">Log in</button>
        </form>

        <p class="text-center muted">New to FemTrack? <a href="register.php">Create an account</a></p>
    </main>

</body>
</html>
