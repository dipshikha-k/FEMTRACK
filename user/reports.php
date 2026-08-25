<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS symptom_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symptom_date DATE NOT NULL,
    symptoms VARCHAR(255) NOT NULL,
    severity VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS total_periods, COALESCE(SUM(DATEDIFF(end_date, start_date) + 1), 0) AS total_days FROM period_logs WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$periodStats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS total_symptoms FROM symptom_logs WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$symptomStats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt = mysqli_prepare($conn, 'SELECT start_date, end_date, flow_intensity FROM period_logs WHERE user_id = ? ORDER BY start_date DESC LIMIT 6');
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$periods = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - FemTrack</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="app-shell">
        <nav class="topbar">
            <a class="logo" href="dashboard.php"><img class="logo-mark" src="../assets/femtrack-mark.jpeg" alt="FemTrack logo">Fem<span>Track</span></a>
            <div class="nav"><a href="../index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="track-symptoms.php">Track Symptoms</a>
            <a class="active" href="reports.php">Reports</a>
            <a href="about.php">About Us</a>
            <a class="logout" href="../logout.php">Logout</a>
        </div>
        </nav>
        <section class="hero"><div><div class="eyebrow">Your summary</div><h1>Reports</h1><p class="lead">A simple overview of the records you have saved.</p></div></section>
        <section class="dashboard-grid">
            <article class="card"><div class="eyebrow">Period records</div><h2><?php echo (int) $periodStats['total_periods']; ?></h2><p class="muted">periods logged</p></article>
            <article class="card"><div class="eyebrow">Tracked days</div><h2><?php echo (int) $periodStats['total_days']; ?></h2><p class="muted">total period days</p></article>
            <article class="card"><div class="eyebrow">Symptom entries</div><h2><?php echo (int) $symptomStats['total_symptoms']; ?></h2><p class="muted">check-ins saved</p></article>
        </section>
        <section class="card table-card" style="margin-top: 28px;"><div style="padding: 24px 28px 0;"><h2>Recent period records</h2></div>
            <?php if (mysqli_num_rows($periods) === 0): ?><p class="empty">No period records yet. <a href="period-log.php">Log your first period.</a></p>
            <?php else: ?><div class="table-wrap"><table><thead><tr><th>Start date</th><th>End date</th><th>Flow</th></tr></thead><tbody><?php while ($period = mysqli_fetch_assoc($periods)): ?><tr><td><?php echo htmlspecialchars($period['start_date']); ?></td><td><?php echo htmlspecialchars($period['end_date']); ?></td><td><span class="tag"><?php echo htmlspecialchars($period['flow_intensity']); ?></span></td></tr><?php endwhile; ?></tbody></table></div><?php endif; ?>
        </section>
    </main>
</body>
</html>
