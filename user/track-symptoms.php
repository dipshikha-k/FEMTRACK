
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

            $message = "Check-in saved successfully.";
            $message_type = "success";


            /*
            |--------------------------------------------------------------------------
            | FEMTRACK DRAMATIC MESSAGE
            |--------------------------------------------------------------------------
            */

            if ($cravings === "Chocolate" && $energy === "Very Low") {

                $femtrackMessage =
                    "Girl, chocolate AND zero energy? You need a vacation, not another responsibility.";

            } elseif (
                $energy === "High" &&
                $mood_swings === "Severe"
            ) {

                $femtrackMessage =
                    "Energy level: unstoppable. Mood swings: equally unstoppable. Go irritate someone else too.";

            } elseif (
                $mood === "Irritated" &&
                $mood_swings === "Severe"
            ) {

                $femtrackMessage =
                    "Okay girl, everybody back away slowly. You are officially in your don't-test-me era.";

            } elseif (
                $mood === "Sad" &&
                $energy === "Very Low"
            ) {

                $femtrackMessage =
                    "Low mood, low energy. Cancel unnecessary plans and romanticize doing absolutely nothing.";

            } elseif ($cravings === "Chocolate") {

                $femtrackMessage =
                    "Chocolate craving detected. Honestly, your body has submitted a very specific request.";

            } elseif ($cravings === "Spicy") {

                $femtrackMessage =
                    "Spicy cravings? Apparently your period wanted drama AND seasoning.";

            } elseif (
                $sleep === "Poor" &&
                $energy === "Low"
            ) {

                $femtrackMessage =
                    "You slept badly and now you're tired. Shocking. Your body has filed a formal complaint.";

            } elseif (
                $mood === "Happy" &&
                $energy === "High"
            ) {

                $femtrackMessage =
                    "Happy and full of energy? Look at you being suspiciously productive.";

            } elseif ($mood === "Calm") {

                $femtrackMessage =
                    "Calm day detected. Protect this peace like it owes you money.";

            } elseif ($severity === "Severe") {

                $femtrackMessage =
                    "Okay, your body is clearly being dramatic today. Be extra gentle with yourself.";

            } else {

                $femtrackMessage =
                    "Another day, another plot twist from your body. At least now we have receipts.";
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

    <title>Track Symptoms - FemTrack</title>

    <link rel="stylesheet" href="../css/style.css">


    <style>

        /* =========================================================
           BASE
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #fff7fb;
            color: #54243d;
            font-family: Arial, Helvetica, sans-serif;
        }


        /* =========================================================
           NAVBAR — SAME VIBE AS DASHBOARD
        ========================================================= */

        .topbar {
            width: 100%;
            background: white;

            padding: 18px 6%;

            border-bottom: 1px solid #f5dce9;

            box-shadow:
                0 4px 20px rgba(180, 80, 130, 0.08);

            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .logo {
            display: flex;
            align-items: center;
            gap: 9px;

            text-decoration: none;

            color: #8f3d68;

            font-size: 27px;
            font-weight: 800;
        }


        .logo span {
            color: #e78ab5;
        }


        .logo-mark {
            width: 34px;
            height: 34px;

            border-radius: 50%;

            object-fit: cover;
        }


        .nav {
            display: flex;
            align-items: center;
            gap: 5px;
        }


        .nav a {
            text-decoration: none;

            color: #71445a;

            font-size: 14px;
            font-weight: 700;

            padding: 10px 14px;

            border-radius: 20px;

            transition: .25s;
        }


        .nav a:hover {
            background: #fde2ef;
            color: #b33f76;
        }


        /* ACTIVE TRACK SYMPTOMS */

        .nav a.active {
            background: rgba(231, 138, 181, 0.20);
            color: #b33f76;
        }


        /* LOGOUT ALWAYS DARK PINK */

        .nav a.logout {
            background: #c65384;
            color: white;
        }


        .nav a.logout:hover {
            background: #a83b6b;
            color: white;
        }


        /* =========================================================
           PAGE
        ========================================================= */

        .page {
            width: 88%;
            max-width: 1250px;

            margin: 48px auto 75px;
        }


        .page-heading {
            margin-bottom: 32px;
        }


        .eyebrow {
            margin-bottom: 9px;

            color: #c15b89;

            font-size: 13px;
            font-weight: 800;

            letter-spacing: 2px;
        }


        .page-heading h1 {
            margin: 0;

            color: #652746;

            font-size: 40px;
            font-weight: 800;

            letter-spacing: -1px;
        }


        .page-heading p {
            margin: 10px 0 0;

            color: #8d6174;

            font-size: 16px;

            line-height: 1.7;
        }


        /* =========================================================
           CONTENT GRID
        ========================================================= */

        .content-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1.55fr)
                minmax(320px, .75fr);

            gap: 35px;

            align-items: start;
        }


        /* =========================================================
           FORM CARD
        ========================================================= */

        .form-card {
            background: white;

            border: 1px solid #f5dce8;

            border-radius: 27px;

            padding: 38px;

            box-shadow:
                0 15px 35px rgba(160, 70, 110, 0.08);
        }


        .form-title {
            margin: 0 0 8px;

            color: #6e2e4c;

            font-size: 28px;
            font-weight: 800;
        }


        .form-subtitle {
            margin: 0 0 30px;

            color: #8d6978;

            font-size: 15px;

            line-height: 1.7;
        }


        /* =========================================================
           ALERT
        ========================================================= */

        .alert {
            padding: 15px 18px;

            margin-bottom: 25px;

            border-radius: 14px;

            font-size: 14px;
            font-weight: 700;
        }


        .alert-success {
            background: #eefaf2;
            color: #39734b;

            border: 1px solid #cdebd5;
        }


        .alert-error {
            background: #fff0f2;
            color: #b64c63;

            border: 1px solid #f4ccd5;
        }


        /* =========================================================
           FORM
        ========================================================= */

        .form-group {
            margin-bottom: 23px;
        }


        .form-group label {
            display: block;

            margin-bottom: 9px;

            color: #513642;

            font-size: 14px;
            font-weight: 800;
        }


        input,
        select,
        textarea {
            width: 100%;

            padding: 15px 16px;

            border: 1px solid #ead4dd;

            border-radius: 14px;

            outline: none;

            background: #fffafd;

            color: #4b3440;

            font-family: inherit;

            font-size: 15px;

            transition: .25s;
        }


        input::placeholder,
        textarea::placeholder {
            color: #b89aa8;
        }


        input:focus,
        select:focus,
        textarea:focus {
            border-color: #df82a7;

            background: white;

            box-shadow:
                0 0 0 4px rgba(223, 130, 167, .11);
        }


        textarea {
            min-height: 125px;
            resize: vertical;
        }


        .two-columns {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;
        }


        /* =========================================================
           SECTION TITLES
        ========================================================= */

        .section-title {
            margin: 32px 0 20px;

            padding-bottom: 10px;

            border-bottom: 1px solid #f1dce5;

            color: #c65384;

            font-size: 17px;

            font-weight: 800;
        }


        /* =========================================================
           BUTTON
        ========================================================= */

        .submit-btn {
            width: 100%;

            margin-top: 8px;

            padding: 17px 20px;

            border: none;

            border-radius: 15px;

            background: #c65384;

            color: white;

            font-family: inherit;

            font-size: 16px;

            font-weight: 800;

            cursor: pointer;

            box-shadow:
                0 9px 20px rgba(198, 83, 132, .20);

            transition: .25s;
        }


        .submit-btn:hover {
            background: #a83b6b;

            transform: translateY(-2px);

            box-shadow:
                0 13px 25px rgba(198, 83, 132, .25);
        }


        /* =========================================================
           SIDE COLUMN
        ========================================================= */

        .side-column {
            position: sticky;
            top: 25px;
        }


        /* =========================================================
           STICKY NOTE
        ========================================================= */

        .sticky-note {
            position: relative;

            min-height: 315px;

            padding: 48px 36px 35px;

            background: #fff9d9;

            border-radius: 6px 6px 28px 6px;

            box-shadow:
                0 22px 38px rgba(82, 53, 20, .14),
                0 4px 10px rgba(82, 53, 20, .06);

            transform: rotate(2deg);

            overflow: hidden;
        }


        /* TAPE */

        .sticky-note::before {
            content: "";

            position: absolute;

            top: -8px;
            left: 50%;

            width: 125px;
            height: 32px;

            transform:
                translateX(-50%)
                rotate(-3deg);

            background:
                rgba(255,255,255,.72);

            border:
                1px solid rgba(255,255,255,.5);

            box-shadow:
                0 2px 6px rgba(0,0,0,.05);
        }


        /* CORNER FOLD */

        .sticky-note::after {
            content: "";

            position: absolute;

            right: 0;
            bottom: 0;

            width: 0;
            height: 0;

            border-top:
                52px solid transparent;

            border-right:
                52px solid #f4e7a6;
        }


        .sticky-title {
            color: #4b3518;

            font-family: Georgia, serif;

            font-size: 30px;

            line-height: 1.25;

            font-weight: 800;
        }


        .sticky-title::after {
            content: "";

            display: block;

            width: 60px;
            height: 3px;

            margin-top: 13px;

            background: #d79a3b;

            border-radius: 20px;

            transform: rotate(-2deg);
        }


        /* MESSAGE ONLY AFTER SAVE */

        .sticky-text {
            margin-top: 25px;

            color: #5e4925;

            font-size: 16px;

            line-height: 1.75;

            font-weight: 600;
        }


        /* SIGNATURE ALWAYS VISIBLE */

        .sticky-signature {
            display: inline-flex;

            align-items: center;

            gap: 5px;

            margin-top: 29px;

            padding: 8px 14px;

            color: #9d7938;

            font-family: Georgia, serif;

            font-size: 14px;

            font-style: italic;

            background:
                rgba(255,255,255,.48);

            border:
                1px dashed rgba(161,124,54,.35);

            border-radius: 20px;

            transform: rotate(-2deg);

            box-shadow:
                0 3px 8px rgba(82,53,20,.05);
        }


        .sticky-signature strong {
            color: #c38b2f;

            font-size: 15px;

            font-style: normal;
        }


        /* =========================================================
           PRIVACY CARD
        ========================================================= */

        .privacy-card {
            margin-top: 28px;

            padding: 24px;

            background: white;

            border: 1px solid #f5dce8;

            border-radius: 22px;

            box-shadow:
                0 12px 30px rgba(160, 70, 110, 0.07);
        }


        .privacy-card h3 {
            margin: 0 0 9px;

            color: #6e2e4c;

            font-size: 17px;
        }


        .privacy-card p {
            margin: 0;

            color: #8d6978;

            font-size: 13px;

            line-height: 1.7;
        }


        /* =========================================================
           TABLET
        ========================================================= */

        @media (max-width: 1000px) {

            .topbar {
                padding: 18px 4%;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .side-column {
                position: static;
            }

        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 700px) {

            .topbar {
                flex-direction: column;
                gap: 15px;
            }

            .nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav a {
                padding: 8px 10px;
                font-size: 12px;
            }

            .page {
                width: 93%;
                margin-top: 35px;
            }

            .page-heading h1 {
                font-size: 31px;
            }

            .form-card {
                padding: 25px 20px;
            }

            .two-columns {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .sticky-note {
                transform: rotate(1deg);
            }

        }


    </style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

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
            class="logout"
            href="../logout.php"
        >
            Logout
        </a>


    </div>

</nav>



<!-- =========================================================
     PAGE
========================================================= -->

<main class="page">


    <!-- PAGE HEADING -->

    <div class="page-heading">

        <div class="eyebrow">
            YOUR PERSONAL CHECK-IN
        </div>

        <h1>
            Track how you are feeling
        </h1>

        <p>
            Your body is allowed to have plot twists.
            Tell FemTrack what's going on.
        </p>

    </div>



    <!-- CONTENT -->

    <div class="content-grid">


        <!-- =================================================
             FORM CARD
        ================================================== -->

        <div class="form-card">


            <?php if ($message !== ""): ?>

                <div class="alert <?php
                    echo $message_type === "success"
                        ? "alert-success"
                        : "alert-error";
                ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <h2 class="form-title">
                Today's check-in
            </h2>


            <p class="form-subtitle">
                Be honest. No judgement here.
                Your entries are saved privately with your account.
            </p>


            <form method="POST">


                <!-- DATE -->

                <div class="form-group">

                    <label for="symptom_date">
                        Date
                    </label>

                    <input
                        type="date"
                        id="symptom_date"
                        name="symptom_date"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["symptom_date"] ?? date("Y-m-d")
                            );
                        ?>"
                        required
                    >

                </div>



                <!-- SYMPTOMS -->

                <div class="form-group">

                    <label for="symptoms">
                        What are you feeling?
                    </label>

                    <input
                        type="text"
                        id="symptoms"
                        name="symptoms"
                        placeholder="e.g. cramps, headache, tiredness"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["symptoms"] ?? ""
                            );
                        ?>"
                        required
                    >

                </div>



                <!-- SEVERITY -->

                <div class="form-group">

                    <label for="severity">
                        Overall severity
                    </label>

                    <select
                        id="severity"
                        name="severity"
                        required
                    >

                        <option value="">
                            Select severity
                        </option>

                        <option value="Mild">
                            Mild
                        </option>

                        <option value="Moderate">
                            Moderate
                        </option>

                        <option value="Severe">
                            Severe
                        </option>

                    </select>

                </div>



                <!-- MOOD -->

                <h3 class="section-title">
                    Mood & energy
                </h3>


                <div class="two-columns">


                    <div class="form-group">

                        <label for="mood">
                            Mood
                        </label>

                        <select id="mood" name="mood">

                            <option value="">
                                Select mood
                            </option>

                            <option value="Happy">
                                Happy
                            </option>

                            <option value="Calm">
                                Calm
                            </option>

                            <option value="Sad">
                                Sad
                            </option>

                            <option value="Irritated">
                                Irritated
                            </option>

                            <option value="Anxious">
                                Anxious
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="mood_swings">
                            Mood swings
                        </label>

                        <select
                            id="mood_swings"
                            name="mood_swings"
                        >

                            <option value="">
                                Select level
                            </option>

                            <option value="None">
                                None
                            </option>

                            <option value="Mild">
                                Mild
                            </option>

                            <option value="Moderate">
                                Moderate
                            </option>

                            <option value="Severe">
                                Severe
                            </option>

                        </select>

                    </div>


                </div>



                <div class="form-group">

                    <label for="energy">
                        Energy level
                    </label>

                    <select
                        id="energy"
                        name="energy"
                    >

                        <option value="">
                            Select energy level
                        </option>

                        <option value="High">
                            High
                        </option>

                        <option value="Normal">
                            Normal
                        </option>

                        <option value="Low">
                            Low
                        </option>

                        <option value="Very Low">
                            Very Low
                        </option>

                    </select>

                </div>



                <!-- BODY -->

                <h3 class="section-title">
                    Body check
                </h3>


                <div class="two-columns">


                    <div class="form-group">

                        <label for="bloating">
                            Bloating
                        </label>

                        <select
                            id="bloating"
                            name="bloating"
                        >

                            <option value="">
                                Select
                            </option>

                            <option value="None">
                                None
                            </option>

                            <option value="Mild">
                                Mild
                            </option>

                            <option value="Moderate">
                                Moderate
                            </option>

                            <option value="Severe">
                                Severe
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="pain">
                            Pain
                        </label>

                        <input
                            type="text"
                            id="pain"
                            name="pain"
                            placeholder="e.g. cramps, back pain"
                        >

                    </div>


                </div>



                <div class="two-columns">


                    <div class="form-group">

                        <label for="sleep">
                            Sleep
                        </label>

                        <select
                            id="sleep"
                            name="sleep"
                        >

                            <option value="">
                                Select
                            </option>

                            <option value="Good">
                                Good
                            </option>

                            <option value="Average">
                                Average
                            </option>

                            <option value="Poor">
                                Poor
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="skin">
                            Skin
                        </label>

                        <input
                            type="text"
                            id="skin"
                            name="skin"
                            placeholder="e.g. acne, clear, dry"
                        >

                    </div>


                </div>



                <!-- CRAVINGS -->

                <h3 class="section-title">
                    Cravings & care
                </h3>


                <div class="two-columns">


                    <div class="form-group">

                        <label for="cravings">
                            Cravings
                        </label>

                        <select
                            id="cravings"
                            name="cravings"
                        >

                            <option value="">
                                Select craving
                            </option>

                            <option value="Chocolate">
                                Chocolate
                            </option>

                            <option value="Spicy">
                                Spicy
                            </option>

                            <option value="Sweet">
                                Sweet
                            </option>

                            <option value="Salty">
                                Salty
                            </option>

                            <option value="None">
                                None
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="medication">
                            Medication
                        </label>

                        <input
                            type="text"
                            id="medication"
                            name="medication"
                            placeholder="Optional"
                        >

                    </div>


                </div>



                <!-- NOTES -->

                <div class="form-group">

                    <label for="notes">
                        Anything else?
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        placeholder="Write anything you want FemTrack to know..."
                    ></textarea>

                </div>



                <!-- BUTTON -->

                <button
                    type="submit"
                    class="submit-btn"
                >
                    Save Check-in
                </button>


            </form>


        </div>



        <!-- =================================================
             SIDE COLUMN
        ================================================== -->

        <div class="side-column">


            <!-- STICKY NOTE -->

            <div class="sticky-note">


                <div class="sticky-title">
                    girl... we need to talk.
                </div>


                <?php if ($femtrackMessage !== ""): ?>

                    <div class="sticky-text">

                        <?php
                            echo htmlspecialchars($femtrackMessage);
                        ?>

                    </div>

                <?php endif; ?>


                <!-- THIS IS OUTSIDE THE IF,
                     SO IT ALWAYS SHOWS -->

                <div class="sticky-signature">

                    — sincerely,

                    <strong>
                        FemTrack
                    </strong>

                </div>


            </div>



            <!-- PRIVACY -->

            <div class="privacy-card">

                <h3>
                    Your check-in stays private.
                </h3>

                <p>
                    Your symptom information is saved securely
                    with your account and isn't displayed as a
                    history list on this page.
                </p>

            </div>


        </div>


    </div>

</main>


</body>

</html>

