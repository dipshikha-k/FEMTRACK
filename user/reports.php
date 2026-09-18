<?php

session_start();

require_once __DIR__ . '/../config/database.php';


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| CREATE SYMPTOM LOG TABLE
|--------------------------------------------------------------------------
*/

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS symptom_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symptom_date DATE NOT NULL,
    symptoms VARCHAR(255) NOT NULL,
    severity VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");



/*
|--------------------------------------------------------------------------
| TOTAL CYCLES
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total_cycles
     FROM period_logs
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$totalCycles = (int)($row['total_cycles'] ?? 0);



/*
|--------------------------------------------------------------------------
| AVERAGE PERIOD LENGTH
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT AVG(DATEDIFF(end_date, start_date) + 1) AS average_period
     FROM period_logs
     WHERE user_id = ?
     AND start_date IS NOT NULL
     AND end_date IS NOT NULL"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$averagePeriodLength = !empty($row['average_period'])
    ? round($row['average_period'])
    : 0;



/*
|--------------------------------------------------------------------------
| AVERAGE CYCLE LENGTH
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT start_date
     FROM period_logs
     WHERE user_id = ?
     ORDER BY start_date ASC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$startDates = [];

while ($row = mysqli_fetch_assoc($result)) {
    $startDates[] = $row['start_date'];
}

$cycleLengths = [];

for ($i = 1; $i < count($startDates); $i++) {

    $previous = new DateTime($startDates[$i - 1]);
    $current = new DateTime($startDates[$i]);

    $days = $previous->diff($current)->days;

    if ($days > 0) {
        $cycleLengths[] = $days;
    }
}

$averageCycleLength = count($cycleLengths) > 0
    ? round(array_sum($cycleLengths) / count($cycleLengths))
    : 0;



/*
|--------------------------------------------------------------------------
| TOTAL SYMPTOMS
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total_symptoms
     FROM symptom_logs
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$totalSymptoms = (int)($row['total_symptoms'] ?? 0);



/*
|--------------------------------------------------------------------------
| SYMPTOM SUMMARY
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT symptoms, severity
     FROM symptom_logs
     WHERE user_id = ?
     ORDER BY symptom_date DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$symptomCounts = [];
$symptomSeverities = [];

while ($row = mysqli_fetch_assoc($result)) {

    $symptoms = explode(',', $row['symptoms']);

    foreach ($symptoms as $symptom) {

        $symptom = trim($symptom);

        if ($symptom === '') {
            continue;
        }

        if (!isset($symptomCounts[$symptom])) {
            $symptomCounts[$symptom] = 0;
            $symptomSeverities[$symptom] = [];
        }

        $symptomCounts[$symptom]++;

        $symptomSeverities[$symptom][] = $row['severity'];
    }
}

arsort($symptomCounts);

$topSymptoms = array_slice($symptomCounts, 0, 4, true);

$mostCommonSymptom = !empty($symptomCounts)
    ? array_key_first($symptomCounts)
    : null;

$mostCommonCount = $mostCommonSymptom
    ? $symptomCounts[$mostCommonSymptom]
    : 0;



/*
|--------------------------------------------------------------------------
| RECENT PERIODS
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT start_date, end_date, flow_intensity
     FROM period_logs
     WHERE user_id = ?
     ORDER BY start_date DESC
     LIMIT 3"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$periods = mysqli_stmt_get_result($stmt);



/*
|--------------------------------------------------------------------------
| CHECK IF USER HAS ANY DATA
|--------------------------------------------------------------------------
*/

$hasData = ($totalCycles > 0 || $totalSymptoms > 0);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Reports | FemTrack</title>

<link rel="stylesheet" href="../css/style.css">


<style>

/* =========================================================
   PAGE
========================================================= */

body {
    background: #fff9fb;
    color: #573044;
}


/* =========================================================
   REPORT HEADER
========================================================= */

.report-header {
    margin: 38px 6% 28px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;
}

.report-heading {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.report-heading-icon {
    width: 46px;
    height: 46px;

    border-radius: 14px;

    background: #fde2ed;
    color: #d14f87;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 22px;
}

.report-header h1 {
    margin: 0;

    font-family: Georgia, serif;
    font-size: 36px;
    font-weight: normal;

    color: #642b48;
}

.report-header p {
    margin: 6px 0 0;

    color: #917080;
    font-size: 14px;
}


/* FILTER */

.period-filter {
    background: white;

    border: 1px solid #f0d8e4;
    border-radius: 14px;

    padding: 11px 17px;

    color: #75425d;
    font-size: 13px;
}


/* =========================================================
   SUMMARY CARDS
========================================================= */

.summary-grid {
    margin: 0 6%;

    display: grid;
    grid-template-columns: repeat(4, 1fr);

    gap: 18px;
}

.summary-card {
    background: white;

    border: 1px solid #f0dce5;
    border-radius: 22px;

    padding: 23px;

    min-height: 140px;

    box-shadow:
        0 8px 22px rgba(120,50,80,.045);
}

.summary-top {
    display: flex;
    align-items: center;
    gap: 12px;
}

.summary-icon {
    width: 42px;
    height: 42px;

    border-radius: 50%;

    background: #fde2ed;
    color: #d04f87;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 20px;
}

.summary-label {
    color: #936779;

    font-size: 12px;
    font-weight: 600;
}

.summary-value {
    margin: 16px 0 4px;

    font-family: Georgia, serif;
    font-size: 28px;

    color: #b63e73;
}

.summary-value small {
    font-family: Arial, sans-serif;

    font-size: 14px;

    color: #b65b82;
}

.summary-note {
    margin: 0;

    color: #9a7b89;
    font-size: 12px;
}


/* =========================================================
   WELCOME / EMPTY REPORT
========================================================= */

.welcome-card {
    margin: 25px 6% 55px;

    background: white;

    border: 1px solid #f0dce5;

    border-radius: 26px;

    padding: 45px 30px;

    text-align: center;

    box-shadow:
        0 8px 22px rgba(120,50,80,.045);
}

.welcome-icon {
    width: 68px;
    height: 68px;

    margin: 0 auto 18px;

    border-radius: 50%;

    background: #fde3ed;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 30px;
}

.welcome-card h2 {
    margin: 0 0 8px;

    font-family: Georgia, serif;
    font-weight: normal;

    font-size: 25px;

    color: #642b48;
}

.welcome-card p {
    max-width: 520px;

    margin: 0 auto 25px;

    color: #927282;

    font-size: 14px;
    line-height: 1.7;
}

.welcome-buttons {
    display: flex;

    justify-content: center;

    gap: 12px;

    flex-wrap: wrap;
}

.welcome-btn {
    display: inline-block;

    padding: 11px 20px;

    border-radius: 12px;

    text-decoration: none;

    font-size: 13px;

    font-weight: 600;

    transition: .2s;
}

.primary-btn {
    background: #c94f80;
    color: white;
}

.primary-btn:hover {
    background: #b94070;
}

.secondary-btn {
    background: #fde5ef;
    color: #b74776;
}

.secondary-btn:hover {
    background: #f9d8e6;
}


/* =========================================================
   REPORT GRID
========================================================= */

.report-grid {
    margin: 24px 6% 0;

    display: grid;

    grid-template-columns: 1.35fr 1fr;

    gap: 20px;
}

.report-card {
    background: white;

    border: 1px solid #f0dce5;

    border-radius: 24px;

    padding: 25px;

    box-shadow:
        0 8px 22px rgba(120,50,80,.045);
}

.card-heading {
    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 20px;
}

.card-heading-icon {
    width: 39px;
    height: 39px;

    border-radius: 50%;

    background: #fde3ed;

    color: #d34f86;

    display: flex;
    align-items: center;
    justify-content: center;
}

.card-heading h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 21px;

    font-weight: normal;

    color: #642b48;
}

.card-heading p {
    margin: 3px 0 0;

    color: #9a7887;

    font-size: 12px;
}


/* =========================================================
   TABLE
========================================================= */

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;

    border-collapse: collapse;
}

th {
    padding: 12px 10px;

    text-align: left;

    background: #ffffff;

    color: #a44b72;

    font-size: 12px;
}

td {
    padding: 14px 10px;

    border-bottom: 1px solid #f5e6ed;

    color: #725568;

    font-size: 13px;
}

tr:last-child td {
    border-bottom: none;
}

.flow-tag {
    display: inline-block;

    padding: 5px 12px;

    border-radius: 20px;

    background: #fde4ee;

    color: #b74978;

    font-size: 11px;

    font-weight: 600;
}


/* =========================================================
   SYMPTOMS
========================================================= */

.symptom-row {
    display: flex;

    align-items: center;

    gap: 12px;

    padding: 12px 0;

    border-bottom: 1px solid #f6e8ee;
}

.symptom-row:last-child {
    border-bottom: none;
}

.symptom-icon {
    width: 35px;
    height: 35px;

    border-radius: 50%;

    background: #fff0f6;

    color: #d45387;

    display: flex;
    align-items: center;
    justify-content: center;
}

.symptom-name {
    flex: 1;

    color: #684458;

    font-size: 13px;

    font-weight: 600;
}

.symptom-count {
    color: #967482;

    font-size: 12px;
}

.severity-tag {
    background: #fde5ef;

    color: #c34879;

    border-radius: 18px;

    padding: 6px 11px;

    font-size: 11px;
}


/* =========================================================
   QUICK INSIGHTS
========================================================= */

.insights-card {
    margin: 20px 6% 55px;
}

.insights-grid {
    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 0;
}

.insight {
    display: flex;

    gap: 12px;

    padding: 5px 22px;

    border-right: 1px solid #f1e2e9;
}

.insight:first-child {
    padding-left: 0;
}

.insight:last-child {
    border-right: none;
}

.insight-icon {
    width: 38px;
    height: 38px;

    border-radius: 50%;

    background: #fde4ee;

    color: #d14d83;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;
}

.insight p {
    margin: 0;

    color: #75596a;

    font-size: 12px;

    line-height: 1.6;
}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width: 1050px) {

    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .report-grid {
        grid-template-columns: 1fr;
    }

}

@media(max-width: 700px) {

    .report-header {
        margin: 28px 4% 22px;

        flex-direction: column;

        align-items: flex-start;
    }

    .report-header h1 {
        font-size: 30px;
    }

    .summary-grid {
        margin: 0 4%;

        grid-template-columns: 1fr;
    }

    .report-grid {
        margin: 20px 4% 0;
    }

    .welcome-card {
        margin: 22px 4% 40px;
    }

    .insights-card {
        margin: 20px 4% 40px;
    }

    .insights-grid {
        grid-template-columns: 1fr;
    }

    .insight {
        padding: 14px 0;

        border-right: none;

        border-bottom: 1px solid #f1e2e9;
    }

    .insight:last-child {
        border-bottom: none;
    }

}

</style>

</head>


<body>

<main class="app-shell">


<!-- =====================================================
     YOUR NAVBAR — UNCHANGED
===================================================== -->

<nav class="topbar">

<a class="logo" href="dashboard.php">

    <img
        class="logo-mark"
        src="../assets/femtrack-mark.jpeg"
        alt=""
    >

    <span>
        Fem<span>Track</span>
    </span>

</a>


<div class="nav">

<a href="../index.php">
    Home
</a>

<a href="dashboard.php">
    Dashboard
</a>

<a href="track-symptoms.php">
    Track Symptoms
</a>

<a class="active" href="reports.php">
    Reports
</a>

<a href="../about.php">
    About Us
</a>

<a class="logout" href="../logout.php">
    Logout
</a>

</div>

</nav>



<!-- =====================================================
     REPORT HEADER
===================================================== -->

<section class="report-header">

<div class="report-heading">

<div class="report-heading-icon">
    ♡
</div>

<div>

<h1>Your Period Report</h1>

<p>
Your personal cycle and symptom summary.
</p>

</div>

</div>


<div class="period-filter">
    ♡ &nbsp; Your tracking
</div>

</section>



<!-- =====================================================
     SUMMARY CARDS
===================================================== -->

<section class="summary-grid">


<!-- TOTAL CYCLES -->

<article class="summary-card">

<div class="summary-top">

<div class="summary-icon">
    ♡
</div>

<div class="summary-label">
    CYCLES TRACKED
</div>

</div>

<div class="summary-value">
    <?php echo $totalCycles; ?>
</div>

<p class="summary-note">
    <?php echo $totalCycles === 1 ? 'cycle recorded' : 'cycles recorded'; ?>
</p>

</article>



<!-- AVERAGE CYCLE -->

<article class="summary-card">

<div class="summary-top">

<div class="summary-icon">
    ◷
</div>

<div class="summary-label">
    AVERAGE CYCLE
</div>

</div>

<div class="summary-value">

<?php if ($averageCycleLength > 0): ?>

    <?php echo $averageCycleLength; ?>

    <small>days</small>

<?php else: ?>

    —

<?php endif; ?>

</div>

<p class="summary-note">
    based on your records
</p>

</article>



<!-- AVERAGE PERIOD -->

<article class="summary-card">

<div class="summary-top">

<div class="summary-icon">
    ♡
</div>

<div class="summary-label">
    AVERAGE PERIOD
</div>

</div>

<div class="summary-value">

<?php if ($averagePeriodLength > 0): ?>

    <?php echo $averagePeriodLength; ?>

    <small>days</small>

<?php else: ?>

    —

<?php endif; ?>

</div>

<p class="summary-note">
    based on your records
</p>

</article>



<!-- SYMPTOMS -->

<article class="summary-card">

<div class="summary-top">

<div class="summary-icon">
    ✿
</div>

<div class="summary-label">
    SYMPTOMS TRACKED
</div>

</div>

<div class="summary-value">
    <?php echo $totalSymptoms; ?>
</div>

<p class="summary-note">
    <?php echo $totalSymptoms === 1 ? 'check-in recorded' : 'check-ins recorded'; ?>
</p>

</article>


</section>



<?php if (!$hasData): ?>


<!-- =====================================================
     FIRST VISIT / EMPTY REPORT
===================================================== -->

<section class="welcome-card">

<div class="welcome-icon">
    🌷
</div>

<h2>
    Your report starts here
</h2>

<p>
    Start tracking your period and symptoms with FemTrack.
    As you add your information, your personal report will
    appear here.
</p>


<div class="welcome-buttons">

<a
    href="period-log.php"
    class="welcome-btn primary-btn"
>
    Log Your Period →
</a>

<a
    href="track-symptoms.php"
    class="welcome-btn secondary-btn"
>
    Track Symptoms
</a>

</div>

</section>



<?php else: ?>


<!-- =====================================================
     RECENT PERIODS + SYMPTOM SUMMARY
===================================================== -->

<section class="report-grid">


<!-- =====================================================
     RECENT PERIODS
===================================================== -->

<div class="report-card">

<div class="card-heading">

<div class="card-heading-icon">
    ♡
</div>

<div>

<h2>Recent Periods</h2>

<p>
Your latest recorded periods.
</p>

</div>

</div>


<?php if (mysqli_num_rows($periods) === 0): ?>

<div style="text-align:center; padding:20px; color:#927282;">

    <div style="font-size:30px;">🌷</div>

    <p>
        No periods recorded yet.
    </p>

    <a
        href="period-log.php"
        style="color:#c04d7c; text-decoration:none; font-weight:600;"
    >
        Log your period →
    </a>

</div>

<?php else: ?>


<div class="table-wrap">

<table>

<thead>

<tr>

<th>Start Date</th>

<th>End Date</th>

<th>Flow</th>

<th>Length</th>

</tr>

</thead>


<tbody>

<?php while ($period = mysqli_fetch_assoc($periods)): ?>

<?php

$periodLength = 0;

if (
    !empty($period['start_date']) &&
    !empty($period['end_date'])
) {

    $start = new DateTime($period['start_date']);
    $end = new DateTime($period['end_date']);

    $periodLength =
        $start->diff($end)->days + 1;
}

?>


<tr>

<td>
    <?php echo date('M d, Y', strtotime($period['start_date'])); ?>
</td>

<td>

<?php if (!empty($period['end_date'])): ?>

    <?php echo date('M d, Y', strtotime($period['end_date'])); ?>

<?php else: ?>

    —

<?php endif; ?>

</td>

<td>

<span class="flow-tag">

<?php
echo htmlspecialchars(
    ucfirst(
        strtolower(
            $period['flow_intensity']
        )
    )
);
?>

</span>

</td>

<td>

<?php if ($periodLength > 0): ?>

    <?php echo $periodLength; ?> days

<?php else: ?>

    —

<?php endif; ?>

</td>

</tr>


<?php endwhile; ?>

</tbody>

</table>

</div>


<?php endif; ?>

</div>



<!-- =====================================================
     SYMPTOM SUMMARY
===================================================== -->

<div class="report-card">

<div class="card-heading">

<div class="card-heading-icon">
    ♡
</div>

<div>

<h2>Symptom Summary</h2>

<p>
Your most tracked symptoms.
</p>

</div>

</div>


<?php if (empty($topSymptoms)): ?>

<div style="text-align:center; padding:20px; color:#927282;">

<div style="font-size:30px;">
    🌸
</div>

<p>
No symptoms recorded yet.
</p>

<a
    href="track-symptoms.php"
    style="color:#c04d7c; text-decoration:none; font-weight:600;"
>
    Track a symptom →
</a>

</div>

<?php else: ?>


<?php foreach ($topSymptoms as $symptom => $count): ?>

<?php

$severityText = 'Recorded';

if (
    isset($symptomSeverities[$symptom]) &&
    count($symptomSeverities[$symptom]) > 0
) {

    $severityValues =
        array_count_values(
            $symptomSeverities[$symptom]
        );

    arsort($severityValues);

    $severityText =
        ucfirst(
            array_key_first($severityValues)
        );
}

?>


<div class="symptom-row">

<div class="symptom-icon">
    ✿
</div>

<div class="symptom-name">
    <?php echo htmlspecialchars($symptom); ?>
</div>

<div class="symptom-count">

    <?php echo $count; ?>

    <?php echo $count === 1 ? 'time' : 'times'; ?>

</div>

<span class="severity-tag">

    <?php echo htmlspecialchars($severityText); ?>

</span>

</div>


<?php endforeach; ?>


<?php endif; ?>

</div>


</section>



<!-- =====================================================
     QUICK INSIGHTS
===================================================== -->

<section class="report-card insights-card">

<div class="card-heading">

<div class="card-heading-icon">
    ✦
</div>

<div>

<h2>Quick Insights</h2>

<p>
A simple summary of your tracking.
</p>

</div>

</div>


<div class="insights-grid">


<div class="insight">

<div class="insight-icon">
    ♡
</div>

<p>

<?php if ($averageCycleLength > 0): ?>

Your average cycle is
<strong>
<?php echo $averageCycleLength; ?> days
</strong>.

<?php else: ?>

Keep logging your periods to see your average cycle.

<?php endif; ?>

</p>

</div>



<div class="insight">

<div class="insight-icon">
    ✿
</div>

<p>

<?php if ($mostCommonSymptom): ?>

<strong>
<?php echo htmlspecialchars($mostCommonSymptom); ?>
</strong>
is your most frequently tracked symptom.

<?php else: ?>

Track your symptoms to see which ones appear most often.

<?php endif; ?>

</p>

</div>



<div class="insight">

<div class="insight-icon">
    ♡
</div>

<p>

<?php if ($averagePeriodLength > 0): ?>

Your average period lasts
<strong>
<?php echo $averagePeriodLength; ?> days
</strong>.

<?php else: ?>

Keep logging your periods to see your average period length.

<?php endif; ?>

</p>

</div>


</div>

</section>


<?php endif; ?>


</main>

</body>

</html>