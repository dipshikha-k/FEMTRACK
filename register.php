<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "This email is already registered.";

        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed. Please try again.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - FemTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-page">
    <main class="auth-card">
        <div class="brand">FemTrack</div>
        <h1>Create your space</h1>
        <p class="lead">A simple, private place for your cycle records.</p>

        <?php if (!empty($message)): ?>
            <p class="notice"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" class="form-stack">
            <label>Name
                <input type="text" name="name" placeholder="Your name" required>
            </label>
            <label>Email
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>
            <label>Password
                <input type="password" name="password" placeholder="Create a password" required>
            </label>
            <label>Confirm password
                <input type="password" name="confirm_password" placeholder="Repeat your password" required>
            </label>
            <button class="button" type="submit">Create account</button>
        </form>

        <p class="text-center muted">Already have an account? <a href="login.php">Log in</a></p>
    </main>

</body>

</html>
