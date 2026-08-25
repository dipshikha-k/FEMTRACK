<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    $_SESSION["redirect_after_login"] = "user/edit-period.php";

    header("Location: ../login.php");
    exit;
}


$user_id = $_SESSION["user_id"];

$message = "";


/*
|--------------------------------------------------------------------------
| Get Period ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    die("Invalid period record.");

}

$period_id = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Get Existing Period
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, start_date, end_date, flow_intensity
     FROM period_logs
     WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $period_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {

    mysqli_stmt_close($stmt);

    die("Period record not found.");

}

$period = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| Update Period
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $flow_intensity = $_POST["flow_intensity"] ?? "";


    if (
        empty($start_date) ||
        empty($end_date) ||
        empty($flow_intensity)
    ) {

        $message = "Please fill in all fields.";

    }

    elseif ($end_date < $start_date) {

        $message = "End date cannot be before start date.";

    }

    else {

        $update = mysqli_prepare(
            $conn,
            "UPDATE period_logs
             SET start_date = ?, end_date = ?, flow_intensity = ?
             WHERE id = ? AND user_id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssii",
            $start_date,
            $end_date,
            $flow_intensity,
            $period_id,
            $user_id
        );

        if (mysqli_stmt_execute($update)) {

            mysqli_stmt_close($update);

            header("Location: period-history.php");
            exit;

        } else {

            $message = "Unable to update period information.";

            mysqli_stmt_close($update);

        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Period - FemTrack</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body>

    <main class="app-shell">


        <!-- =====================================================
             NAVBAR
        ====================================================== -->

        <nav class="topbar">

            <a
                class="logo"
                href="dashboard.php"
            >

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

                <a
                    class="logout"
                    href="../logout.php"
                >
                    Logout
                </a>

            </div>

        </nav>



        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <section class="hero">

            <div>

                <div class="eyebrow">
                    Update your record
                </div>

                <h1>
                    Edit period
                </h1>

                <p class="lead">
                    Update the details of your logged period.
                </p>

            </div>

        </section>



        <!-- =====================================================
             EDIT FORM
        ====================================================== -->

        <section class="card">

            <?php if (!empty($message)): ?>

                <div class="message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- Start Date -->

                <div class="form-group">

                    <label for="start_date">
                        Period Start Date
                    </label>

                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="<?php echo htmlspecialchars($period["start_date"]); ?>"
                        required
                    >

                </div>


                <!-- End Date -->

                <div class="form-group">

                    <label for="end_date">
                        Period End Date
                    </label>

                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="<?php echo htmlspecialchars($period["end_date"]); ?>"
                        required
                    >

                </div>


                <!-- Flow Intensity -->

                <div class="form-group">

                    <label for="flow_intensity">
                        Flow Intensity
                    </label>

                    <select
                        id="flow_intensity"
                        name="flow_intensity"
                        required
                    >

                        <option
                            value="light"
                            <?php
                            echo ($period["flow_intensity"] === "light")
                                ? "selected"
                                : "";
                            ?>
                        >
                            Light
                        </option>


                        <option
                            value="medium"
                            <?php
                            echo ($period["flow_intensity"] === "medium")
                                ? "selected"
                                : "";
                            ?>
                        >
                            Medium
                        </option>


                        <option
                            value="heavy"
                            <?php
                            echo ($period["flow_intensity"] === "heavy")
                                ? "selected"
                                : "";
                            ?>
                        >
                            Heavy
                        </option>

                    </select>

                </div>



                <!-- =================================================
                     BUTTONS
                ================================================== -->

                <div class="form-actions">

                    <button
                        type="submit"
                        class="button"
                    >
                        Update Period
                    </button>


                    <a
                        href="period-history.php"
                        class="button secondary"
                    >
                        ← Back to History
                    </a>

                </div>


            </form>

        </section>


    </main>

</body>

</html>
