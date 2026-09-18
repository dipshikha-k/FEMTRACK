<?php

session_start();

require_once __DIR__ . "/../config/database.php";


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$message = "";


/*
|--------------------------------------------------------------------------
| SAVE PERIOD INFORMATION
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Period Log | FemTrack</title>

    <!-- Main FemTrack stylesheet -->
    <link
        rel="stylesheet"
        href="../css/style.css"
    >


    <style>

        /* =========================================================
           PAGE
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background:
                radial-gradient(
                    circle at 78% 18%,
                    rgba(255, 143, 190, 0.20),
                    transparent 25%
                ),
                radial-gradient(
                    circle at 15% 80%,
                    rgba(255, 185, 214, 0.20),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #fffdfd 0%,
                    #fff7fa 48%,
                    #f9f3f6 100%
                );

            color: #4a2634;

            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;

            min-height: 100vh;
        }


        /* =========================================================
           PERIOD PAGE
        ========================================================= */

        .period-page {

            max-width: 1180px;

            margin: 0 auto;

            padding: 58px 24px 80px;
        }


        /* =========================================================
           FORM CARD
        ========================================================= */

        .content-card {

            max-width: 650px;

            margin: 0 auto;

            background: rgba(255, 255, 255, 0.94);

            border: 1px solid #f3dce6;

            border-radius: 26px;

            padding: 40px;

            box-shadow:
                0 22px 60px rgba(232, 101, 164, 0.10);

            position: relative;

            overflow: hidden;
        }


        .content-card::before {

            content: "";

            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 5px;

            background:
                linear-gradient(
                    90deg,
                    #ff7cad,
                    #e63f86,
                    #ff91bd
                );
        }


        /* =========================================================
           FORM HEADER
        ========================================================= */

        .form-header {

            text-align: center;

            padding-bottom: 25px;

            margin-bottom: 30px;

            border-bottom: 1px solid #f4e4eb;
        }


        .eyebrow {

            display: inline-flex;

            align-items: center;

            padding: 8px 14px;

            margin-bottom: 12px;

            border-radius: 999px;

            background: #fff0f6;

            border: 1px solid #ffd2e2;

            color: #d52d79;

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1.6px;

            text-transform: uppercase;
        }


        .form-header h1 {

            margin: 0;

            color: #3b2031;

            font-size: 36px;

            line-height: 1.15;

            font-weight: 800;

            letter-spacing: -1px;
        }


        .lead {

            max-width: 520px;

            margin: 12px auto 0;

            color: #76606d;

            font-size: 14px;

            line-height: 1.8;
        }


        /* =========================================================
           NOTICE
        ========================================================= */

        .notice {

            margin-bottom: 25px;

            padding: 14px 17px;

            background: #fff0f5;

            border: 1px solid #ffd4e1;

            border-radius: 14px;

            color: #b52f64;

            font-size: 13px;

            font-weight: 700;

            text-align: center;
        }


        /* =========================================================
           FORM
        ========================================================= */

        .form-stack {

            display: flex;

            flex-direction: column;

            gap: 21px;
        }


        .form-stack label {

            display: block;

            color: #593444;

            font-size: 13px;

            font-weight: 800;
        }


        .form-stack input,
        .form-stack select {

            width: 100%;

            box-sizing: border-box;

            display: block;

            margin-top: 8px;

            padding: 13px 15px;

            background: #ffffff;

            border: 1px solid #ecd8e2;

            border-radius: 12px;

            color: #4b3040;

            font-family: inherit;

            font-size: 13px;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                transform .2s ease;
        }


        .form-stack input:hover,
        .form-stack select:hover {

            border-color: #e9a4c0;
        }


        .form-stack input:focus,
        .form-stack select:focus {

            border-color: #e64488;

            box-shadow:
                0 0 0 4px rgba(230, 68, 136, .10);

            background: #ffffff;
        }


        /* =========================================================
           PREDICTION BUTTON
        ========================================================= */

        .predict-button {

            width: 100%;

            padding: 14px 20px;

            border: none;

            border-radius: 14px;

            background:
                linear-gradient(
                    90deg,
                    #d73b80,
                    #ef5f9a
                );

            color: #ffffff;

            font-family: inherit;

            font-size: 14px;

            font-weight: 800;

            cursor: pointer;

            box-shadow:
                0 12px 24px rgba(218, 52, 123, .20);

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }


        .predict-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 16px 30px rgba(218, 52, 123, .27);
        }


        .predict-button:active {

            transform: translateY(0);
        }


        /* =========================================================
           PREDICTION BOX
        ========================================================= */

        .prediction-box {

            display: none;

            background:
                linear-gradient(
                    145deg,
                    #fff0f6,
                    #ffe4ef
                );

            border: 1px solid #f4cddd;

            border-radius: 20px;

            padding: 24px;

            text-align: center;

            margin-top: -2px;
        }


        .prediction-box h2 {

            margin: 0 0 10px;

            color: #a83f70;

            font-size: 20px;

            font-weight: 800;
        }


        .prediction-date {

            color: #d94f83;

            font-size: 28px;

            font-weight: 800;
        }


        .prediction-note {

            color: #98717f;

            font-size: 12px;

            line-height: 1.6;

            margin-top: 9px;
        }


        /* =========================================================
           ACTIONS
        ========================================================= */

        .actions {

            display: flex;

            gap: 12px;

            margin-top: 4px;
        }


        .button {

            flex: 1;

            border: none;

            background:
                linear-gradient(
                    90deg,
                    #d73b80,
                    #ef5f9a
                );

            color: white;

            text-decoration: none;

            padding: 14px 20px;

            border-radius: 14px;

            font-family: inherit;

            font-size: 13px;

            font-weight: 800;

            cursor: pointer;

            box-shadow:
                0 10px 22px rgba(218, 52, 123, .20);

            transition:
                transform .2s ease,
                box-shadow .2s ease;

            text-align: center;
        }


        .button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 14px 27px rgba(218, 52, 123, .25);
        }


        .button.secondary {

            background: #fff0f5;

            color: #c24f79;

            border: 1px solid #f5d5e1;

            box-shadow: none;
        }


        .button.secondary:hover {

            background: #fde1eb;

            box-shadow: none;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 768px) {

            .period-page {

                padding: 35px 15px 60px;
            }


            .content-card {

                padding: 28px 20px;

                border-radius: 22px;
            }


            .form-header h1 {

                font-size: 31px;
            }


            .actions {

                flex-direction: column;
            }

        }


        @media (max-width: 480px) {

            .period-page {

                padding: 25px 12px 50px;
            }


            .content-card {

                padding: 25px 16px;
            }


            .form-header h1 {

                font-size: 28px;
            }


            .prediction-date {

                font-size: 24px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVIGATION
========================================================= -->

<nav class="home-nav" aria-label="Main navigation">

    <a
        class="home-logo"
        href="../index.php"
        aria-label="FemTrack home"
    >

        <img
            src="../assets/femtrack-mark.jpeg"
            alt=""
        >

        <span>
            Fem<span>Track</span>
        </span>

    </a>


    <div class="home-nav-links app-nav-links">

        <a href="../index.php">
            Home
        </a>


        <a href="dashboard.php">
            Dashboard
        </a>


        <a href="track-symptoms.php">
            Track Symptoms
        </a>


        <a
            class="active"
            href="period-log.php"
        >
            Period Log
        </a>


        <a href="reports.php">
            Reports
        </a>


        <a href="../about.php">
            About Us
        </a>


        <a
            class="nav-logout"
            href="../logout.php"
        >
            Logout
        </a>

    </div>

</nav>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="period-page">


    <!-- =========================================================
         PERIOD LOG CARD
    ========================================================= -->

    <section class="content-card">


        <!-- HEADER -->

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



        <!-- =====================================================
             MESSAGE
        ====================================================== -->

        <?php if (!empty($message)): ?>

            <div class="notice">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>



        <!-- =====================================================
             FORM
        ====================================================== -->

        <form
            method="POST"
            class="form-stack"
        >


            <!-- PERIOD START -->

            <label>

                Period Start Date

                <input
                    type="date"
                    name="start_date"
                    id="start_date"
                    required
                >

            </label>



            <!-- PERIOD END -->

            <label>

                Period End Date

                <input
                    type="date"
                    name="end_date"
                    required
                >

            </label>



            <!-- FLOW -->

            <label>

                Flow Intensity

                <select
                    name="flow_intensity"
                    required
                >

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



            <!-- =================================================
                 CYCLE LENGTH
            ================================================== -->

            <label>

                Your Cycle Length

                <select
                    id="cycle_length"
                >

                    <option value="21">
                        21 days
                    </option>

                    <option value="22">
                        22 days
                    </option>

                    <option value="23">
                        23 days
                    </option>

                    <option value="24">
                        24 days
                    </option>

                    <option value="25">
                        25 days
                    </option>

                    <option value="26">
                        26 days
                    </option>

                    <option value="27">
                        27 days
                    </option>

                    <option
                        value="28"
                        selected
                    >
                        28 days
                    </option>

                    <option value="29">
                        29 days
                    </option>

                    <option value="30">
                        30 days
                    </option>

                    <option value="31">
                        31 days
                    </option>

                    <option value="32">
                        32 days
                    </option>

                    <option value="33">
                        33 days
                    </option>

                    <option value="34">
                        34 days
                    </option>

                    <option value="35">
                        35 days
                    </option>

                </select>

            </label>



            <!-- =================================================
                 PREDICT BUTTON
            ================================================== -->

            <button
                type="button"
                class="predict-button"
                onclick="predictPeriod()"
            >
                🌸 Predict Next Period
            </button>



            <!-- =================================================
                 PREDICTION RESULT
            ================================================== -->

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



            <!-- =================================================
                 ACTION BUTTONS
            ================================================== -->

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



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

function predictPeriod() {

    const startDate =
        document.getElementById("start_date").value;


    const cycleLength =
        parseInt(
            document.getElementById("cycle_length").value
        );


    if (!startDate) {

        alert(
            "Please select your last period start date. 🌸"
        );

        return;
    }


    const date =
        new Date(startDate + "T00:00:00");


    date.setDate(
        date.getDate() + cycleLength
    );


    const options = {

        year: "numeric",

        month: "long",

        day: "numeric"

    };


    const predictedDate =
        date.toLocaleDateString(
            "en-US",
            options
        );


    document.getElementById(
        "predictionDate"
    ).textContent = predictedDate;


    document.getElementById(
        "predictionBox"
    ).style.display = "block";

}

</script>


</body>

</html>