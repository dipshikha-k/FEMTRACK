<?php

session_start();

require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION["user_id"];
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $flow_intensity = $_POST["flow_intensity"] ?? "";

    if (empty($start_date) || empty($end_date) || empty($flow_intensity)) {

        $message = "Please fill in all fields.";

    } elseif ($end_date < $start_date) {

        $message = "End date cannot be before start date.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO period_logs 
            (user_id, start_date, end_date, flow_intensity)
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isss",
            $user_id,
            $start_date,
            $end_date,
            $flow_intensity
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Period information saved successfully! 🌸";

        } else {

            $message = "Something went wrong. Please try again.";

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

    <title>Period Log - FemTrack</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>
    <main class="app-shell">
        <nav class="topbar">
            <a class="logo" href="dashboard.php"><img class="logo-mark" src="../assets/femtrack-mark.jpeg" alt="FemTrack logo">Fem<span>Track</span></a>
            <div class="nav"><a href="../index.php">Home</a><a href="dashboard.php">Dashboard</a><a href="track-symptoms.php">Track Symptoms</a><a href="reports.php">Reports</a><a class="logout" href="../logout.php">Logout</a></div>
        </nav>
        <section class="content-card card">
            <div class="form-header"><div class="eyebrow">New record</div><h1>Log your period</h1><p class="lead">Save the start date, end date, and flow intensity for this cycle.</p></div>
            <?php if (!empty($message)): ?><p class="notice"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST" class="form-stack">
                <label>Period start date<input type="date" name="start_date" required></label>
                <label>Period end date<input type="date" name="end_date" required></label>
                <label>Flow intensity<select name="flow_intensity" required><option value="">Select flow</option><option value="light">Light</option><option value="medium">Medium</option><option value="heavy">Heavy</option></select></label>
                <div class="actions"><button class="button" type="submit">Save period</button><a class="button secondary" href="dashboard.php">Back to dashboard</a></div>
            </form>
        </section>
    </main>

</body>

</html>
