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
$message_type = "";
$femtrackMessage = "";


/*
|--------------------------------------------------------------------------
| CREATE SYMPTOM TABLE
|--------------------------------------------------------------------------
*/

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS symptom_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        symptom_date DATE NOT NULL,
        symptoms VARCHAR(255) NOT NULL,
        severity VARCHAR(50) NOT NULL,
        mood VARCHAR(50) DEFAULT NULL,
        mood_swings VARCHAR(50) DEFAULT NULL,
        cravings VARCHAR(100) DEFAULT NULL,
        bloating VARCHAR(50) DEFAULT NULL,
        sleep VARCHAR(50) DEFAULT NULL,
        energy VARCHAR(50) DEFAULT NULL,
        pain VARCHAR(100) DEFAULT NULL,
        skin VARCHAR(100) DEFAULT NULL,
        medication VARCHAR(255) DEFAULT NULL,
        notes TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");


/*
|--------------------------------------------------------------------------
| ADD MISSING COLUMNS
|--------------------------------------------------------------------------
*/

$columns = [
    "mood" => "VARCHAR(50) DEFAULT NULL",
    "mood_swings" => "VARCHAR(50) DEFAULT NULL",
    "cravings" => "VARCHAR(100) DEFAULT NULL",
    "bloating" => "VARCHAR(50) DEFAULT NULL",
    "sleep" => "VARCHAR(50) DEFAULT NULL",
    "energy" => "VARCHAR(50) DEFAULT NULL",
    "pain" => "VARCHAR(100) DEFAULT NULL",
    "skin" => "VARCHAR(100) DEFAULT NULL",
    "medication" => "VARCHAR(255) DEFAULT NULL",
    "notes" => "TEXT DEFAULT NULL"
];

foreach ($columns as $column => $definition) {

    $check = mysqli_query(
        $conn,
        "SHOW COLUMNS FROM symptom_logs LIKE '$column'"
    );

    if ($check && mysqli_num_rows($check) === 0) {

        mysqli_query(
            $conn,
            "ALTER TABLE symptom_logs ADD COLUMN $column $definition"
        );
    }
}


/*
|--------------------------------------------------------------------------
| SAVE CHECK-IN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $symptom_date = $_POST["symptom_date"] ?? "";
    $symptoms = trim($_POST["symptoms"] ?? "");
    $severity = $_POST["severity"] ?? "";

    $mood = $_POST["mood"] ?? "";
    $mood_swings = $_POST["mood_swings"] ?? "";
    $cravings = $_POST["cravings"] ?? "";
    $bloating = $_POST["bloating"] ?? "";
    $sleep = $_POST["sleep"] ?? "";
    $energy = $_POST["energy"] ?? "";
    $pain = $_POST["pain"] ?? "";
    $skin = $_POST["skin"] ?? "";
    $medication = trim($_POST["medication"] ?? "");
    $notes = trim($_POST["notes"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($symptom_date === "") {

        $message = "Please select a date.";
        $message_type = "error";

    } elseif ($symptoms === "") {

        $message = "Please tell us what you're experiencing.";
        $message_type = "error";

    } elseif (!in_array($severity, ["Mild", "Moderate", "Severe"])) {

        $message = "Please select a valid severity level.";
        $message_type = "error";

    } else {


        /*
        |--------------------------------------------------------------------------
        | INSERT
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO symptom_logs
            (
                user_id,
                symptom_date,
                symptoms,
                severity,
                mood,
                mood_swings,
                cravings,
                bloating,
                sleep,
                energy,
                pain,
                skin,
                medication,
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "isssssssssssss",
            $user_id,
            $symptom_date,
            $symptoms,
            $severity,
            $mood,
            $mood_swings,
            $cravings,
            $bloating,
            $sleep,
            $energy,
            $pain,
            $skin,
            $medication,
            $notes
        );


        if (mysqli_stmt_execute($stmt)) {

            $message = "Your check-in is saved successfully ♡";
            $message_type = "success";


            /*
            |--------------------------------------------------------------------------
            | FEMTRACK PERSONALIZED NOTE
            |--------------------------------------------------------------------------
            */

            if (
                $mood === "Irritated" &&
                $mood_swings === "Severe"
            ) {

                $femtrackMessage =
                    "Girl, put on your lip gloss, fix your crown, and SLAY, diva. Don't give a shit about this irritating world today — protect your peace and remember that not everything deserves your energy.";

            } elseif ($mood === "Irritated") {

                $femtrackMessage =
                    "Girl, put on your lip gloss and remember who you are. The world can be irritating without getting access to your peace, so protect your energy and keep it moving.";

            } elseif (
                $mood === "Happy" &&
                $energy === "High"
            ) {

                $femtrackMessage =
                    "Girl, put on your lip gloss and SLAY, diva. This energy looks good on you, so enjoy it, protect it, and don't let this irritating world dim your shine.";

            } elseif ($mood === "Happy") {

                $femtrackMessage =
                    "Girl, okayyy we love this energy for you. Put on a little lip gloss, enjoy your good mood, and let yourself have your main-character moment.";

            } elseif (
                $cravings === "Spicy" &&
                $mood_swings === "Severe"
            ) {

                $femtrackMessage =
                    "Girl… spicy cravings and mood swings are really doing the most today. Take a breath, protect your peace, and please do not make any emotionally questionable decisions tonight.";

            } elseif (
                $cravings === "Chocolate" &&
                $energy === "Very Low"
            ) {

                $femtrackMessage =
                    "Girl… your body ordered chocolate and a full recharge. Get comfortable, have your little treat, and let today be slower than usual.";

            } elseif (
                $mood === "Sad" &&
                $energy === "Very Low"
            ) {

                $femtrackMessage =
                    "Girl… today feels a little heavy, and that's okay. Cancel the unnecessary pressure, get cozy, and give yourself permission to simply rest.";

            } elseif (
                $sleep === "Poor" &&
                $energy === "Very Low"
            ) {

                $femtrackMessage =
                    "Girl… sleep clearly did not show up for you last night. Today does not need to be a productivity competition, so let yourself recover.";

            } elseif (
                $sleep === "Poor" &&
                $energy === "Low"
            ) {

                $femtrackMessage =
                    "Girl… you barely slept and your body is asking for a break. Lower the expectations today and give yourself the care you would give someone you love.";

            } elseif (
                $mood === "Anxious" &&
                $mood_swings === "Severe"
            ) {

                $femtrackMessage =
                    "Girl… anxious thoughts and big mood swings are a lot to carry at once. Slow everything down for a moment and give yourself some quiet before you deal with the rest of the world.";

            } elseif (
                $pain !== "" &&
                $severity === "Severe"
            ) {

                $femtrackMessage =
                    "Girl… your body is asking for extra care today. Slow down, get comfortable, and make taking care of yourself the priority.";

            } elseif (
                $bloating === "Severe" &&
                $cravings === "Chocolate"
            ) {

                $femtrackMessage =
                    "Girl… bloating and chocolate cravings have clearly joined forces today. Be gentle with yourself, get comfortable, and give your body what feels soothing.";

            } elseif ($energy === "Very Low") {

                $femtrackMessage =
                    "Girl… your battery is asking for a serious recharge. Lower the expectations, take care of the essentials, and let yourself rest without guilt.";

            } elseif ($energy === "Low") {

                $femtrackMessage =
                    "Girl… your energy is running a little low today. Do what matters, leave what can wait, and give yourself a softer pace.";

            } elseif ($mood === "Sad") {

                $femtrackMessage =
                    "Girl… your heart feels a little heavy today. You don't have to force yourself to be okay; give yourself comfort and take the day gently.";

            } elseif ($mood === "Anxious") {

                $femtrackMessage =
                    "Girl… take a breath and slow everything down for a minute. You don't have to figure everything out at once, so be gentle with yourself today.";

            } elseif ($mood === "Calm") {

                $femtrackMessage =
                    "Girl… this peaceful little moment is worth protecting. Keep the unnecessary drama away and make room for whatever helps you feel grounded.";

            } elseif ($cravings === "Spicy") {

                $femtrackMessage =
                    "Girl… the spicy cravings have entered the chat. Your body clearly wants a little excitement, so enjoy the craving and take care of yourself while you're at it.";

            } elseif ($cravings === "Chocolate") {

                $femtrackMessage =
                    "Girl… chocolate is clearly on the agenda today. Consider this your gentle reminder that a little comfort is allowed.";

            } elseif ($severity === "Severe") {

                $femtrackMessage =
                    "Girl… your body is asking for extra attention today. Be gentle with yourself, slow the pace, and make your comfort a priority.";

            } else {

                $femtrackMessage =
                    "Girl… thank you for checking in with yourself today. Whatever your body is telling you, listening to it is already a form of self-care.";
            }

        } else {

            $message = "Something went wrong while saving your check-in.";
            $message_type = "error";
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

    <title>Track Symptoms | FemTrack</title>

    <link rel="stylesheet" href="../css/style.css">


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
        }

        .track-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 58px 24px 80px;

            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;
        }


        /* =========================================================
           HEADING
        ========================================================= */

        .page-heading {
            position: relative;
            max-width: 760px;
            margin-bottom: 38px;
        }

        .page-eyebrow {
            display: inline-flex;

            align-items: center;

            padding: 8px 14px;
            margin-bottom: 15px;

            border-radius: 999px;

            background: #fff0f6;
            border: 1px solid #ffd2e2;

            color: #d52d79;

            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .page-heading h1 {
            margin: 0;

            color: #3b2031;

            font-size: clamp(35px, 5vw, 54px);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -1.8px;
        }

        .heading-soft {
            display: block;

            margin-top: 10px;

            color: #d84d8c;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 20px;
            font-style: italic;
        }

        .page-heading p {
            max-width: 670px;

            margin: 18px 0 0;

            color: #76606d;

            font-size: 14px;
            line-height: 1.8;
        }


        /* =========================================================
           LAYOUT
        ========================================================= */

        .content-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                320px;

            gap: 30px;

            align-items: start;
        }


        /* =========================================================
           FORM CARD
        ========================================================= */

        .form-card {
            position: relative;

            overflow: hidden;

            background: rgba(255, 255, 255, 0.92);

            border: 1px solid #f3dce6;
            border-radius: 26px;

            padding: 34px;

            box-shadow:
                0 22px 60px rgba(104, 36, 69, 0.10);
        }

        .form-card::before {
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


        .form-card-header {
            padding-bottom: 23px;
            margin-bottom: 30px;

            border-bottom: 1px solid #f4e4eb;
        }

        .form-card-header h2 {
            margin: 0;

            color: #432535;

            font-size: 23px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        .form-card-header p {
            margin: 7px 0 0;

            color: #987b89;

            font-size: 13px;
            line-height: 1.6;
        }


        /* =========================================================
           ALERT
        ========================================================= */

        .form-alert {
            margin-bottom: 25px;

            padding: 14px 17px;

            border-radius: 14px;

            font-size: 13px;
            font-weight: 700;
        }

        .form-alert.success {
            background: #effbf4;
            border: 1px solid #cfeedd;
            color: #34724e;
        }

        .form-alert.error {
            background: #fff0f5;
            border: 1px solid #ffd4e1;
            color: #b52f64;
        }


        /* =========================================================
           SECTION
        ========================================================= */

        .form-section {
            margin-bottom: 34px;
            padding: 23px;

            background:
                linear-gradient(
                    135deg,
                    #fffafb,
                    #fff6f9
                );

            border: 1px solid #f7e3eb;
            border-radius: 20px;
        }

        .section-heading {
            margin-bottom: 20px;
        }

        .section-heading h3 {
            margin: 0;

            color: #4a2939;

            font-size: 16px;
            font-weight: 800;
        }

        .section-heading p {
            margin: 5px 0 0;

            color: #9b7d8b;

            font-size: 12px;
        }


        /* =========================================================
           FORM GROUP
        ========================================================= */

        .form-group {
            margin-bottom: 21px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            color: #593444;

            font-size: 12px;
            font-weight: 800;
        }

        .form-hint {
            display: block;

            margin: -2px 0 9px;

            color: #aa8b99;

            font-size: 11px;
            line-height: 1.5;
        }


        /* =========================================================
           INPUTS
        ========================================================= */

        .form-group input[type="date"],
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {

            width: 100%;

            padding: 12px 14px;

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

        .form-group input[type="date"]:hover,
        .form-group input[type="text"]:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: #e9a4c0;
        }

        .form-group input[type="date"]:focus,
        .form-group input[type="text"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {

            border-color: #e64488;

            box-shadow:
                0 0 0 4px rgba(230, 68, 136, .10);

            background: #ffffff;
        }

        .form-group textarea {
            min-height: 105px;

            resize: vertical;

            line-height: 1.6;
        }


        /* =========================================================
           TWO COLUMN INPUTS
        ========================================================= */

        .form-row {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 18px;
        }


        /* =========================================================
           CHOICE BUTTONS
        ========================================================= */

        .choice-grid {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 10px;
        }

        .choice-grid.four {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }

        .choice-grid.five {
            grid-template-columns:
                repeat(5, minmax(0, 1fr));
        }

        .choice-option {
            position: relative;
        }

        .choice-option input {
            position: absolute;

            opacity: 0;

            pointer-events: none;
        }

        .choice-option label {

            display: flex;

            align-items: center;
            justify-content: center;

            min-height: 43px;

            padding: 8px 9px;

            margin: 0;

            background: #ffffff;

            border: 1px solid #ecd9e3;
            border-radius: 13px;

            color: #765867;

            font-size: 12px;
            font-weight: 700;

            text-align: center;

            cursor: pointer;

            transition:
                all .18s ease;
        }

        .choice-option label:hover {

            background: #fff0f6;

            border-color: #f28ab6;

            color: #d9347d;

            transform: translateY(-2px);
        }

        .choice-option input:checked + label {

            background:
                linear-gradient(
                    135deg,
                    #ffe6f0,
                    #fff0f6
                );

            border-color: #e84287;

            color: #c72d6e;

            box-shadow:
                0 7px 16px rgba(218, 57, 125, .12);

            transform: translateY(-1px);
        }


        /* =========================================================
           SAVE
        ========================================================= */

        .save-area {

            padding-top: 8px;
        }

        .save-button {

            width: 100%;

            padding: 15px 20px;

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

            letter-spacing: 0.1px;

            cursor: pointer;

            box-shadow:
                0 12px 24px rgba(218, 52, 123, .22);

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }

        .save-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 16px 30px rgba(218, 52, 123, .28);
        }

        .save-button:active {
            transform: translateY(0);
        }


        /* =========================================================
           SIDE COLUMN
        ========================================================= */

        .side-column {

            display: flex;

            flex-direction: column;

            gap: 24px;
        }


        /* =========================================================
           FEMTRACK NOTE
        ========================================================= */

        .sticky-note {

            position: relative;

            min-height: 330px;

            padding: 37px 28px 30px;

            background:
                linear-gradient(
                    145deg,
                    #fff0f6,
                    #ffe2ed
                );

            border: 1px solid #f7c6d8;

            border-radius: 28px;

            box-shadow:
                0 18px 40px rgba(180, 51, 109, .14);
        }

        .sticky-note::before {

            content: "♡";

            position: absolute;

            top: 18px;
            right: 23px;

            color: #ec5c98;

            font-size: 22px;
        }

        .sticky-note::after {

            content: "";

            position: absolute;

            left: 0;
            right: 0;
            bottom: 0;

            height: 7px;

            border-radius:
                0 0 28px 28px;

            background:
                linear-gradient(
                    90deg,
                    #e63d84,
                    #ff8dbb
                );
        }

        .sticky-title {

            margin-bottom: 23px;

            color: #482638;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 30px;
            line-height: 1.12;

            font-style: italic;
            font-weight: 600;
        }

        .sticky-label {

            display: inline-block;

            margin-bottom: 10px;

            padding: 6px 10px;

            background: rgba(255, 255, 255, .55);

            border-radius: 999px;

            color: #d43b7e;

            font-size: 9px;
            font-weight: 800;

            letter-spacing: 1.3px;

            text-transform: uppercase;
        }

        .sticky-divider {

            width: 46px;
            height: 3px;

            margin-bottom: 17px;

            background: #e84e91;

            border-radius: 999px;
        }

        .sticky-text {

            color: #624353;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 15px;

            line-height: 1.75;
        }

        .sticky-signature {

            margin-top: 23px;

            color: #bc5d86;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 13px;

            font-style: italic;
        }


        /* =========================================================
           EMPTY NOTE STATE
        ========================================================= */

        .sticky-empty {

            margin-top: 45px;

            color: #9b7182;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 15px;

            line-height: 1.75;

            font-style: italic;
        }


        /* =========================================================
           PRIVACY CARD
        ========================================================= */

        .privacy-card {

            padding: 24px;

            background: rgba(255, 255, 255, 0.88);

            border: 1px solid #f0dfe7;
            border-radius: 22px;

            box-shadow:
                0 12px 30px rgba(105, 45, 73, .07);
        }

        .privacy-icon {

            display: flex;

            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;

            margin-bottom: 13px;

            background: #fff0f6;

            border-radius: 13px;

            color: #d73b80;

            font-size: 18px;
        }

        .privacy-card h3 {

            margin: 0 0 8px;

            color: #4d2c3c;

            font-size: 15px;
            font-weight: 800;
        }

        .privacy-card p {

            margin: 0;

            color: #8b6f7d;

            font-size: 12px;

            line-height: 1.7;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 900px) {

            .content-grid {

                grid-template-columns: 1fr;
            }

            .side-column {

                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                align-items: start;
            }
        }


        @media (max-width: 650px) {

            .track-page {

                padding: 35px 15px 60px;
            }

            .page-heading h1 {

                font-size: 37px;
            }

            .form-card {

                padding: 20px 15px;

                border-radius: 21px;
            }

            .form-section {

                padding: 18px 14px;

                border-radius: 17px;
            }

            .form-row {

                grid-template-columns: 1fr;

                gap: 0;
            }

            .choice-grid,
            .choice-grid.four,
            .choice-grid.five {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .side-column {

                grid-template-columns: 1fr;
            }

            .sticky-note {

                min-height: auto;
            }

            .sticky-title {

                font-size: 27px;
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

        <a
            class="active"
            href="track-symptoms.php"
        >
            Track Symptoms
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

<main class="track-page">


    <!-- PAGE INTRO -->

    <div class="page-heading">

        <div class="page-eyebrow">
            Your gentle little check-in ♡
        </div>

        <h1>
            Hey girl, let’s see how you’re feeling.
        </h1>

        <span class="heading-soft">
            No pressure, no judgment — just you and your body ♡
        </span>

        <p>
            Your body has a lot to say. Take a quiet moment,
            tell us what you're experiencing, and let FemTrack
            help you notice the little patterns along the way.
        </p>

    </div>



    <!-- CONTENT -->

    <div class="content-grid">


        <!-- =====================================================
             FORM
        ====================================================== -->

        <div class="form-card">


            <div class="form-card-header">

                <h2>
                    Just a few little questions ♡
                </h2>

                <p>
                    Because apparently our bodies like keeping secrets.
                </p>

            </div>



            <?php if ($message !== ""): ?>

                <div class="form-alert <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>



            <form method="POST" action="">


                <!-- =================================================
                     FIRST THINGS FIRST
                ================================================== -->

                <section class="form-section">

                    <div class="section-heading">

                        <h3>
                            First things first
                        </h3>

                        <p>
                            Let's keep this nice and simple.
                        </p>

                    </div>


                    <div class="form-row">


                        <div class="form-group">

                            <label for="symptom_date">
                                What day are we checking in?
                            </label>

                            <input
                                type="date"
                                id="symptom_date"
                                name="symptom_date"
                                value="<?php echo htmlspecialchars($_POST["symptom_date"] ?? date("Y-m-d")); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="severity">
                                How intense is it today?
                            </label>

                            <select
                                id="severity"
                                name="severity"
                                required
                            >

                                <option value="">
                                    Choose one
                                </option>

                                <option
                                    value="Mild"
                                    <?php echo (($_POST["severity"] ?? "") === "Mild") ? "selected" : ""; ?>
                                >
                                    Mild
                                </option>

                                <option
                                    value="Moderate"
                                    <?php echo (($_POST["severity"] ?? "") === "Moderate") ? "selected" : ""; ?>
                                >
                                    Moderate
                                </option>

                                <option
                                    value="Severe"
                                    <?php echo (($_POST["severity"] ?? "") === "Severe") ? "selected" : ""; ?>
                                >
                                    Severe
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="symptoms">
                            What are you experiencing?
                        </label>

                        <span class="form-hint">
                            Tell us what feels a little different today.
                        </span>

                        <input
                            type="text"
                            id="symptoms"
                            name="symptoms"
                            placeholder="e.g. cramps, headache, back pain..."
                            value="<?php echo htmlspecialchars($_POST["symptoms"] ?? ""); ?>"
                            required
                        >

                    </div>

                </section>



                <!-- =================================================
                     MOOD
                ================================================== -->

                <section class="form-section">

                    <div class="section-heading">

                        <h3>
                            Okay, emotional check ♡
                        </h3>

                        <p>
                            How are we doing up there, babe?
                        </p>

                    </div>


                    <!-- MOOD -->

                    <div class="form-group">

                        <label>
                            What's your mood like today?
                        </label>

                        <div class="choice-grid">

                            <?php

                            $moods = [
                                "Happy",
                                "Calm",
                                "Sad",
                                "Irritated",
                                "Anxious"
                            ];

                            foreach ($moods as $option):

                            ?>

                                <div class="choice-option">

                                    <input
                                        type="radio"
                                        id="mood_<?php echo strtolower($option); ?>"
                                        name="mood"
                                        value="<?php echo $option; ?>"
                                        <?php echo (($_POST["mood"] ?? "") === $option) ? "checked" : ""; ?>
                                    >

                                    <label
                                        for="mood_<?php echo strtolower($option); ?>"
                                    >
                                        <?php echo $option; ?>
                                    </label>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>



                    <!-- MOOD SWINGS -->

                    <div class="form-group">

                        <label>
                            How much are your moods switching up?
                        </label>

                        <div class="choice-grid">

                            <?php

                            foreach (
                                ["Mild", "Moderate", "Severe"]
                                as $option
                            ):

                            ?>

                                <div class="choice-option">

                                    <input
                                        type="radio"
                                        id="mood_swings_<?php echo strtolower($option); ?>"
                                        name="mood_swings"
                                        value="<?php echo $option; ?>"
                                        <?php echo (($_POST["mood_swings"] ?? "") === $option) ? "checked" : ""; ?>
                                    >

                                    <label
                                        for="mood_swings_<?php echo strtolower($option); ?>"
                                    >
                                        <?php echo $option; ?>
                                    </label>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>



                    <!-- ENERGY -->

                    <div class="form-group">

                        <label>
                            How's your energy?
                        </label>

                        <div class="choice-grid four">

                            <?php

                            foreach (
                                ["High", "Normal", "Low", "Very Low"]
                                as $option
                            ):

                                $id =
                                    strtolower(
                                        str_replace(" ", "_", $option)
                                    );

                            ?>

                                <div class="choice-option">

                                    <input
                                        type="radio"
                                        id="energy_<?php echo $id; ?>"
                                        name="energy"
                                        value="<?php echo $option; ?>"
                                        <?php echo (($_POST["energy"] ?? "") === $option) ? "checked" : ""; ?>
                                    >

                                    <label
                                        for="energy_<?php echo $id; ?>"
                                    >
                                        <?php echo $option; ?>
                                    </label>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </section>



                <!-- =================================================
                     BODY
                ================================================== -->

                <section class="form-section">

                    <div class="section-heading">

                        <h3>
                            Now let's check on the body
                        </h3>

                        <p>
                            Just a quick little body check.
                        </p>

                    </div>


                    <!-- BLOATING -->

                    <div class="form-group">

                        <label>
                            Any bloating?
                        </label>

                        <div class="choice-grid four">

                            <?php

                            foreach (
                                ["None", "Mild", "Moderate", "Severe"]
                                as $option
                            ):

                            ?>

                                <div class="choice-option">

                                    <input
                                        type="radio"
                                        id="bloating_<?php echo strtolower($option); ?>"
                                        name="bloating"
                                        value="<?php echo $option; ?>"
                                        <?php echo (($_POST["bloating"] ?? "") === $option) ? "checked" : ""; ?>
                                    >

                                    <label
                                        for="bloating_<?php echo strtolower($option); ?>"
                                    >
                                        <?php echo $option; ?>
                                    </label>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>



                    <!-- PAIN -->

                    <div class="form-group">

                        <label>
                            Any pain or discomfort?
                        </label>

                        <select name="pain">

                            <option value="">
                                Choose one
                            </option>

                            <?php

                            $painOptions = [
                                "None",
                                "Mild cramps",
                                "Moderate cramps",
                                "Severe cramps",
                                "Headache",
                                "Back pain",
                                "Other discomfort"
                            ];

                            foreach ($painOptions as $option):

                            ?>

                                <option
                                    value="<?php echo $option; ?>"
                                    <?php echo (($_POST["pain"] ?? "") === $option) ? "selected" : ""; ?>
                                >
                                    <?php echo $option; ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>



                    <!-- SLEEP -->

                    <div class="form-group">

                        <label>
                            How did you sleep?
                        </label>

                        <div class="choice-grid">

                            <?php

                            foreach (
                                ["Good", "Average", "Poor"]
                                as $option
                            ):

                            ?>

                                <div class="choice-option">

                                    <input
                                        type="radio"
                                        id="sleep_<?php echo strtolower($option); ?>"
                                        name="sleep"
                                        value="<?php echo $option; ?>"
                                        <?php echo (($_POST["sleep"] ?? "") === $option) ? "checked" : ""; ?>
                                    >

                                    <label
                                        for="sleep_<?php echo strtolower($option); ?>"
                                    >
                                        <?php echo $option; ?>
                                    </label>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>



                    <!-- SKIN -->

                    <div class="form-group">

                        <label>
                            Any skin changes?
                        </label>

                        <select name="skin">

                            <option value="">
                                Choose one
                            </option>

                            <?php

                            $skinOptions = [
                                "No changes",
                                "Breakouts",
                                "Dry skin",
                                "Oily skin",
                                "Sensitive skin",
                                "Other"
                            ];

                            foreach ($skinOptions as $option):

                            ?>

                                <option
                                    value="<?php echo $option; ?>"
                                    <?php echo (($_POST["skin"] ?? "") === $option) ? "selected" : ""; ?>
                                >
                                    <?php echo $option; ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </section>



                <!-- =================================================
                     CRAVINGS & CARE
                ================================================== -->

                <section class="form-section">

                    <div class="section-heading">

                        <h3>
                            And finally... cravings & care ♡
                        </h3>

                        <p>
                            Because yes, we should probably talk about those too.
                        </p>

                    </div>


                    <!-- CRAVINGS -->

                    <div class="form-group">

                        <label>
                            What are you craving?
                        </label>

                        <div class="choice-grid five">

                            <?php

                            foreach (
                                [
                                    "Chocolate",
                                    "Spicy",
                                    "Sweet",
                                    "Salty",
                                    "None"
                                ]
                                as $option
                            ):

                            ?>

                                <div class="choice-option">

                                    <input
                                        type="radio"
                                        id="craving_<?php echo strtolower($option); ?>"
                                        name="cravings"
                                        value="<?php echo $option; ?>"
                                        <?php echo (($_POST["cravings"] ?? "") === $option) ? "checked" : ""; ?>
                                    >

                                    <label
                                        for="craving_<?php echo strtolower($option); ?>"
                                    >
                                        <?php echo $option; ?>
                                    </label>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>



                    <!-- MEDICATION -->

                    <div class="form-group">

                        <label for="medication">
                            Taking anything for your symptoms?
                        </label>

                        <span class="form-hint">
                            Totally optional, babe — skip if it doesn't apply.
                        </span>

                        <input
                            type="text"
                            id="medication"
                            name="medication"
                            placeholder="Optional"
                            value="<?php echo htmlspecialchars($_POST["medication"] ?? ""); ?>"
                        >

                    </div>



                    <!-- NOTES -->

                    <div class="form-group">

                        <label for="notes">
                            Anything else on your mind?
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            placeholder="Anything else you'd like FemTrack to know..."
                        ><?php echo htmlspecialchars($_POST["notes"] ?? ""); ?></textarea>

                    </div>

                </section>



                <!-- =================================================
                     SAVE BUTTON
                ================================================== -->

                <div class="save-area">

                    <button
                        type="submit"
                        class="save-button"
                    >
                        Save my check-in ♡
                    </button>

                </div>


            </form>

        </div>



        <!-- =====================================================
             SIDE COLUMN
        ====================================================== -->

        <aside class="side-column">


            <!-- FEMTRACK NOTE -->

            <div class="sticky-note">

                <div class="sticky-title">
                    girl... we need to talk.
                </div>


                <?php if ($femtrackMessage !== ""): ?>

                    <div class="sticky-label">
                        A little note for you
                    </div>

                    <div class="sticky-divider"></div>

                    <div class="sticky-text">

                        <?php
                        echo htmlspecialchars($femtrackMessage);
                        ?>

                    </div>

                    <div class="sticky-signature">
                        with love, FemTrack ♡
                    </div>

                <?php else: ?>

                    <div class="sticky-empty">

                        Fill in your little check-in and
                        I'll leave you a personalized note
                        right here, babe ♡

                    </div>

                <?php endif; ?>

            </div>



            <!-- PRIVACY -->

            <div class="privacy-card">

                <div class="privacy-icon">
                    ♡
                </div>

                <h3>
                    Your check-in stays private.
                </h3>

                <p>
                    Your symptom information is saved securely
                    with your account and isn't displayed as a
                    history list on this page.
                </p>

            </div>


        </aside>


    </div>

</main>


</body>

</html>