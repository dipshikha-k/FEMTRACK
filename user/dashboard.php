<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email FROM users WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$user) {
    session_unset();
    session_destroy();

    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FemTrack</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="app-shell">
        <nav class="topbar">
            <a class="logo" href="dashboard.php"><img class="logo-mark" src="../assets/femtrack-mark.jpeg" alt="FemTrack logo">Fem<span>Track</span></a>
            <div class="nav">
                <a href="../index.php">Home</a>
                <a class="active" href="dashboard.php">Dashboard</a>
                <a href="track-symptoms.php">Track Symptoms</a>
                <a href="reports.php">Reports</a>
                <a href="about.php">About Us</a>
                <a class="logout" href="../logout.php">Logout</a>
            </div>
        </nav>

        <section class="hero">
            <div>
                <div class="eyebrow">Your personal dashboard</div>
                <h1>Welcome back, <?php echo htmlspecialchars($user["name"]); ?>.</h1>
                <p class="lead">Keep your cycle records clear and in one calm place.</p>
            </div>
        </section>

        <section class="dashboard-grid">
            <article class="card welcome-card">
                <div class="eyebrow">Today with FemTrack</div>
                <h2>Your wellbeing,<br>on your terms.</h2>
                <p class="muted">Record your period when you are ready, then look back at your history anytime.</p>
            </article>

            <article class="card">
                <h2>Quick actions</h2>
                <div class="quick-links" style="margin-top: 18px;">
                    <a class="quick-link" href="period-log.php"><strong>+ Log a period</strong><span>Add a new cycle record</span></a>
                    <a class="quick-link" href="period-history.php"><strong>View history</strong><span>Review your saved records</span></a>
                </div>
            </article>

            <article class="card">
                <h2>Your account</h2>
                <dl class="details" style="margin-top: 20px;">
                    <div><dt>Email</dt><dd><?php echo htmlspecialchars($user["email"]); ?></dd></div>
                    <div><dt>Account type</dt><dd>Member</dd></div>
                </dl>
            </article>
        </section>
    </main>

</body>
</html>
