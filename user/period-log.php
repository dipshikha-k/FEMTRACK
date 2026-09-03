
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

        /* Form Card */

        .content-card {
            max-width: 650px;
            margin: 55px auto;
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 35px rgba(190, 80, 120, 0.13);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .eyebrow {
            color: #d94f83;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .form-header h1 {
            color: #5d3042;
            font-size: 34px;
            margin: 8px 0;
        }

        .lead {
            color: #8b6875;
            line-height: 1.6;
        }

        /* Notice */

        .notice {
            background: #fff0f5;
            color: #b64c72;
            border: 1px solid #f6d5e1;
            border-radius: 12px;
            padding: 12px 15px;
            text-align: center;
            margin-bottom: 25px;
        }

        /* Form */

        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-stack label {
            color: #633548;
            font-weight: 600;
            font-size: 15px;
        }

        .form-stack input,
        .form-stack select {
            width: 100%;
            box-sizing: border-box;
            margin-top: 8px;
            padding: 13px 15px;
            border: 1px solid #efcbd8;
            border-radius: 12px;
            background: #fffafb;
            color: #633548;
            font-size: 15px;
            outline: none;
        }

        .form-stack input:focus,
        .form-stack select:focus {
            border-color: #e978a0;
            box-shadow: 0 0 0 3px rgba(233, 120, 160, 0.12);
        }

        /* Prediction */

        .prediction-box {
            display: none;
            background: linear-gradient(
                145deg,
                #fff0f6,
                #ffe4ef
            );
            border: 1px solid #f4cddd;
            border-radius: 18px;
            padding: 22px;
            text-align: center;
            margin-top: 5px;
        }

        .prediction-box h2 {
            color: #a83f70;
            font-size: 20px;
            margin: 0 0 8px;
        }

        .prediction-date {
            color: #d94f83;
            font-size: 28px;
            font-weight: 700;
        }

        .prediction-note {
            color: #98717f;
            font-size: 13px;
            margin-top: 8px;
        }

        /* Actions */

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .button {
            border: none;
            background: #e978a0;
            color: white;
            text-decoration: none;
            padding: 13px 20px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(217, 79, 131, 0.22);
            transition: 0.2s;
            text-align: center;
        }

        .button:hover {
            background: #d95d88;
            transform: translateY(-2px);
        }

        .button.secondary {
            background: #fff0f5;
            color: #c24f79;
            box-shadow: none;
        }

        .button.secondary:hover {
            background: #fde1eb;
        }

        /* Mobile */

        @media (max-width: 768px) {

            .app-shell {
                padding: 15px;
            }

            .content-card {
                margin: 30px auto;
                padding: 25px;
            }

            .form-header h1 {
                font-size: 29px;
            }

            .actions {
                flex-direction: column;
            }

            .nav a {
                margin-left: 8px;
                font-size: 13px;
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


    <!-- Period Log Form -->

    <section class="content-card card">

        <div class="form-header">

            <div class="eyebrow">
                ♡ NEW RECORD
            </div>

            <h1>
                Log Your Period
            </h1>

            <p class="lead">
                Keep track of your cycle and predict your next period.
            </p>

        </div>


        <?php if (!empty($message)): ?>

            <p class="notice">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>


        <form method="POST" class="form-stack">

            <label>

                Period Start Date

                <input
                    type="date"
                    name="start_date"
                    id="start_date"
                    required
                >

            </label>


            <label>

                Period End Date

                <input
                    type="date"
                    name="end_date"
                    required
                >

            </label>


            <label>

                Flow Intensity

                <select name="flow_intensity" required>

                    <option value="">
                        Select flow
                    </option>

                    <option value="light">
                        Light
                    </option>

                    <option value="medium">
                        Medium
                    </option>

                    <option value="heavy">
                        Heavy
                    </option>

                </select>

            </label>


            <!-- Cycle Length -->

            <label>

                Your Cycle Length

                <select id="cycle_length">

                    <option value="21">21 days</option>
                    <option value="22">22 days</option>
                    <option value="23">23 days</option>
                    <option value="24">24 days</option>
                    <option value="25">25 days</option>
                    <option value="26">26 days</option>
                    <option value="27">27 days</option>
                    <option value="28" selected>28 days</option>
                    <option value="29">29 days</option>
                    <option value="30">30 days</option>
                    <option value="31">31 days</option>
                    <option value="32">32 days</option>
                    <option value="33">33 days</option>
                    <option value="34">34 days</option>
                    <option value="35">35 days</option>

                </select>

            </label>


            <!-- Predict Button -->

            <button
                type="button"
                class="button"
                onclick="predictPeriod()"
            >
                🌸 Predict Next Period
            </button>


            <!-- Prediction Result -->

            <div
                class="prediction-box"
                id="predictionBox"
            >

                <h2>
                    🌸 Your Next Period
                </h2>

                <div
                    class="prediction-date"
                    id="predictionDate"
                ></div>

                <div class="prediction-note">
                    Based on your selected cycle length.
                    This is an estimated date and may vary.
                </div>

            </div>


            <div class="actions">

                <button
                    class="button"
                    type="submit"
                >
                    Save Period 🌸
                </button>

                <a
                    class="button secondary"
                    href="dashboard.php"
                >
                    Back to Dashboard
                </a>

            </div>

        </form>

    </section>

</main>


<script>

function predictPeriod() {

    const startDate = document.getElementById("start_date").value;
    const cycleLength = parseInt(
        document.getElementById("cycle_length").value
    );

    if (!startDate) {

        alert("Please select your last period start date. 🌸");
        return;
    }

    const date = new Date(startDate + "T00:00:00");

    date.setDate(date.getDate() + cycleLength);

    const options = {
        year: "numeric",
        month: "long",
        day: "numeric"
    };

    const predictedDate = date.toLocaleDateString(
        "en-US",
        options
    );

    document.getElementById("predictionDate").textContent =
        predictedDate;

    document.getElementById("predictionBox").style.display =
        "block";
}

</script>

</body>

</html>

