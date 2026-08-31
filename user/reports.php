
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS symptom_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symptom_date DATE NOT NULL,
    symptoms VARCHAR(255) NOT NULL,
    severity VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) total_periods,
COALESCE(SUM(DATEDIFF(end_date,start_date)+1),0) total_days
FROM period_logs WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$periodStats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) total_symptoms
FROM symptom_logs WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$symptomStats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt = mysqli_prepare($conn, "SELECT start_date,end_date,flow_intensity
FROM period_logs WHERE user_id=? ORDER BY start_date DESC LIMIT 6");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$periods = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports | FemTrack</title>

<link rel="stylesheet" href="../css/style.css">

<style>

body {
    background: #fff9fb;
    color: #573044;
}

/* HERO */

.hero {
    margin: 40px 6% 30px;
    padding: 50px;
    border-radius: 35px;
    background: #f9e1eb;
    position: relative;
    overflow: hidden;
}

.hero::after {
    
    position: absolute;
    right: 60px;
    bottom: 20px;
    font-size: 75px;
    opacity: .65;
}

.eyebrow {
    color: #bd668b;
    letter-spacing: 2px;
    font-size: 11px;
    font-weight: bold;
}

.hero h1 {
    font-family: Georgia, serif;
    font-size: 45px;
    font-weight: normal;
    color: #5b2942;
    margin: 8px 0;
}

.lead {
    color: #896878;
}


/* STATS */

.dashboard-grid {
    margin: 0 6%;
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
}

.stat {
    background: white;
    padding: 28px;
    border-radius: 25px;
    text-align: center;
    border: 1px solid #f1dce6;
    box-shadow: 0 10px 25px rgba(120,50,80,.06);
}

.stat-icon {
    font-size: 25px;
    color: #d2769c;
}

.stat h2 {
    font-family: Georgia, serif;
    font-size: 38px;
    font-weight: normal;
    color: #b84e7b;
    margin: 8px 0;
}

.stat p {
    color: #967382;
    margin: 0;
}


/* RECORDS */

.report-card {
    margin: 30px 6% 60px;
    background: white;
    border: 1px solid #f1dce6;
    border-radius: 28px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(120,50,80,.06);
}

.report-title {
    text-align: center;
    margin-bottom: 25px;
}

.report-title h2 {
    font-family: Georgia, serif;
    font-weight: normal;
    color: #5b2942;
    margin: 7px 0;
}

.report-title p {
    color: #9a7785;
    font-size: 14px;
}


/* TABLE */

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    padding: 15px;
    text-align: left;
    background: #fff1f6;
    color: #a94b73;
    font-size: 13px;
}

td {
    padding: 16px 15px;
    border-bottom: 1px solid #f6e5ec;
    color: #765767;
}

tr:hover td {
    background: #fff9fb;
}

.tag {
    background: #f8dce8;
    color: #ad4774;
    padding: 6px 13px;
    border-radius: 20px;
    font-size: 12px;
}


/* EMPTY */

.empty {
    text-align: center;
    padding: 35px;
    color: #947383;
}

.empty-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.empty a {
    color: #bd527e;
    text-decoration: none;
    font-weight: bold;
}


/* MOBILE */

@media(max-width:800px) {

    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .hero {
        margin: 25px 4%;
        padding: 35px 25px;
    }

    .hero h1 {
        font-size: 35px;
    }

    .hero::after {
        right: 20px;
        font-size: 50px;
    }

    .report-card {
        margin: 25px 4% 40px;
        padding: 20px;
    }

}

</style>

</head>


<body>

<main class="app-shell">


<!-- NAVBAR -->

<nav class="topbar">

<a class="logo" href="dashboard.php">
<img class="logo-mark" src="../assets/femtrack-mark.jpeg">
Fem<span>Track</span>
</a>

<div class="nav">

<a href="../index.php">Home</a>
<a href="dashboard.php">Dashboard</a>
<a href="track-symptoms.php">Track Symptoms</a>
<a class="active" href="reports.php">Reports</a>
<a href="../about.php">About Us</a>
<a class="logout" href="../logout.php">Logout</a>

</div>

</nav>


<!-- HERO -->

<section class="hero">

<div>

<div class="eyebrow">♡ YOUR PERSONAL INSIGHTS</div>

<h1>Your cycle story</h1>

<p class="lead">
A gentle look at the patterns you've recorded with FemTrack.
</p>

</div>

</section>


<!-- STATISTICS -->

<section class="dashboard-grid">

<article class="stat">

<div class="stat-icon">♡</div>

<div class="eyebrow">PERIODS</div>

<h2>
<?php echo (int)$periodStats['total_periods']; ?>
</h2>

<p>cycles recorded</p>

</article>


<article class="stat">

<div class="stat-icon">✿</div>

<div class="eyebrow">PERIOD DAYS</div>

<h2>
<?php echo (int)$periodStats['total_days']; ?>
</h2>

<p>days recorded</p>

</article>


<article class="stat">

<div class="stat-icon">🌸</div>

<div class="eyebrow">SYMPTOMS</div>

<h2>
<?php echo (int)$symptomStats['total_symptoms']; ?>
</h2>

<p>check-ins recorded</p>

</article>

</section>


<!-- PERIOD HISTORY -->

<section class="report-card">

<div class="report-title">

<div class="eyebrow">
✦ YOUR RECENT HISTORY ✦
</div>

<h2>Period records</h2>

<p>
A little collection of the cycles you've logged.
</p>

</div>


<?php if (mysqli_num_rows($periods) === 0): ?>

<div class="empty">

<div class="empty-icon">🌷</div>

<p>
Your story hasn't started here yet.
</p>

<a href="period-log.php">
Log your first period →
</a>

</div>

<?php else: ?>

<div class="table-wrap">

<table>

<thead>

<tr>
<th>Start date</th>
<th>End date</th>
<th>Flow</th>
</tr>

</thead>

<tbody>

<?php while ($period = mysqli_fetch_assoc($periods)): ?>

<tr>

<td>
<?php echo htmlspecialchars($period['start_date']); ?>
</td>

<td>
<?php echo htmlspecialchars($period['end_date']); ?>
</td>

<td>
<span class="tag">
<?php echo htmlspecialchars($period['flow_intensity']); ?>
</span>
</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php endif; ?>

</section>


</main>

</body>
</html>

