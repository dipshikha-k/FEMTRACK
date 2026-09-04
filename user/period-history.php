```php
<?php

session_start();

require_once __DIR__ . "/../config/database.php";

/* Check Login */
if (!isset($_SESSION["user_id"])) {
    $_SESSION["redirect_after_login"] = "user/period-history.php";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

/* Get User's Period History */
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, start_date, end_date, flow_intensity, created_at
     FROM period_logs
     WHERE user_id = ?
     ORDER BY start_date DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Period History - FemTrack</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        body {
            background: #fff5f8;
            color: #4a2634;
            font-family: "Segoe UI", sans-serif;
        }

        .app-shell {
            max-width: 1200px;
            margin: auto;
            padding: 20px 30px;
        }

        /* Navbar */

        .topbar {
            background: white;
            border-radius: 18px;
            padding: 14px 22px;
            box-shadow: 0 5px 20px rgba(190, 80, 120, 0.12);
        }

        .logo {
            color: #d94f83;
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
        }

        .logo span {
            color: #8f4967;
        }

        .logo-mark {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            vertical-align: middle;
        }

        .nav a {
            color: #704052;
            text-decoration: none;
            margin-left: 20px;
            font-size: 15px;
        }

        .nav a:hover {
            color: #d94f83;
        }

        .nav .logout {
            color: #c94b73;
        }

        /* Hero */

        .hero {
            margin: 45px 0 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .eyebrow {
            color: #d94f83;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .hero h1 {
            margin: 8px 0;
            color: #5d3042;
            font-size: 38px;
        }

        .lead {
            color: #8b6875;
            margin: 0;
        }

        /* Button */

        .button {
            background: #e978a0;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(217, 79, 131, 0.25);
            transition: 0.2s;
            white-space: nowrap;
        }

        .button:hover {
            background: #d95d88;
            transform: translateY(-2px);
        }

        /* Table Card */

        .card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(190, 80, 120, 0.12);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #fff0f5;
            color: #9b4565;
            padding: 15px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid #f7dfe8;
            color: #634452;
        }

        tr:hover {
            background: #fff9fb;
        }

        /* Flow Tag */

        .tag {
            display: inline-block;
            background: #fde1eb;
            color: #c24f79;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Edit Link */

        td a {
            color: #d45682;
            text-decoration: none;
            font-weight: 600;
        }

        td a:hover {
            text-decoration: underline;
        }

        /* Empty State */

        .empty {
            text-align: center;
            padding: 60px 20px;
        }

        .empty h2 {
            color: #633548;
        }

        .muted {
            color: #9b7b87;
            margin-bottom: 25px;
        }

        /* Mobile */

        @media (max-width: 768px) {

            .app-shell {
                padding: 15px;
            }

            .hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero h1 {
                font-size: 30px;
            }

            .nav {
                margin-top: 10px;
            }

            .nav a {
                margin-left: 8px;
                font-size: 13px;
            }

            .card {
                padding: 15px;
            }

        }

    </style>

</head>

<body>

<main class="app-shell">

    <!-- Navbar -->

    <nav class="topbar">

        <a class="logo" href="dashboard.php">

            <img
                class="logo-mark"
                src="../assets/femtrack-mark.jpeg"
                alt="FemTrack logo"
            >

            Fem<span>Track</span>

        </a>

        <div class="nav">

            <a href="../index.php">Home</a>

            <a href="dashboard.php">Dashboard</a>

            <a href="track-symptoms.php">Track Symptoms</a>

            <a href="reports.php">Reports</a>

            <a href="../about.php">About Us</a>

            <a class="logout" href="../logout.php">Logout</a>

        </div>

    </nav>


    <!-- Hero -->

    <section class="hero">

        <div>

            <div class="eyebrow">
                ♡ YOUR RECORDS
            </div>

            <h1>
                Period History
            </h1>

            <p class="lead">
                A simple and private view of the cycles you have logged.
            </p>

        </div>

        <a class="button" href="period-log.php">
            + Log New Period
        </a>

    </section>


    <!-- History -->

    <section class="card table-card">

        <?php if (mysqli_num_rows($result) > 0): ?>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>S.N</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Flow Intensity</th>
                            <th>Logged On</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php $count = 1; ?>

                        <?php while ($period = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    <?php echo $count++; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($period["start_date"]);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($period["end_date"]);
                                    ?>
                                </td>

                                <td>

                                    <span class="tag">

                                        <?php
                                        echo htmlspecialchars(
                                            ucfirst($period["flow_intensity"])
                                        );
                                        ?>

                                    </span>

                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars($period["created_at"]);
                                    ?>
                                </td>

                                <td>

                                    <a
                                        href="edit-period.php?id=<?php echo $period['id']; ?>"
                                    >
                                        Edit
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty">

                <h2>
                    🌸 No Period Records Yet
                </h2>

                <p class="muted">
                    When you log a period, your cycle history will appear here.
                </p>

                <a class="button" href="period-log.php">
                    Log Your First Period
                </a>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>

</html>

<?php

mysqli_stmt_close($stmt);

?>

