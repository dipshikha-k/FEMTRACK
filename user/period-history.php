<?php

session_start();

require_once __DIR__ . "/../config/database.php";


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    $_SESSION["redirect_after_login"] = "user/period-history.php";

    header("Location: ../login.php");
    exit;
}


$user_id = $_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| Get User's Period History
|--------------------------------------------------------------------------
*/

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Period History - FemTrack</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <main class="app-shell">

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

                <a href="../index.php">
                    Home
                </a>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="track-symptoms.php">
                    Track Symptoms
                </a>

                <a href="reports.php">
                    Reports
                </a>

                <a class="logout" href="../logout.php">
                    Logout
                </a>

            </div>

        </nav>


        <section class="hero">

            <div>

                <div class="eyebrow">
                    Your records
                </div>

                <h1>
                    Period history
                </h1>

                <p class="lead">
                    A simple view of the cycles you have logged.
                </p>

            </div>


            <a class="button" href="period-log.php">
                + Log new period
            </a>

        </section>


        <section class="card table-card">

            <?php if (mysqli_num_rows($result) > 0): ?>

                <div class="table-wrap">

                    <table>

                        <thead>

                            <tr>

                                <th>S.N</th>

                                <th>
                                    Start Date
                                </th>

                                <th>
                                    End Date
                                </th>

                                <th>
                                    Flow Intensity
                                </th>

                                <th>
                                    Logged On
                                </th>

                                <th>
                                    Action
                                </th>

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
                                        echo htmlspecialchars(
                                            $period["start_date"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $period["end_date"]
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <span class="tag">

                                            <?php
                                            echo htmlspecialchars(
                                                ucfirst(
                                                    $period["flow_intensity"]
                                                )
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $period["created_at"]
                                        );
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
                        No period records yet
                    </h2>

                    <p class="muted">
                        When you log a period, it will appear here.
                    </p>

                    <a
                        class="button"
                        href="period-log.php"
                    >
                        Log your first period
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
