<?php

session_start();

require_once __DIR__ . '/../config/database.php';


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


/*
|--------------------------------------------------------------------------
| GET USER INFORMATION
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email FROM users WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$user) {

    session_unset();
    session_destroy();

    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK IF USER HAS PERIOD HISTORY
|--------------------------------------------------------------------------
*/

$hasPeriodHistory = false;

$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total FROM period_logs WHERE user_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$periodData = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if ($periodData && $periodData["total"] > 0) {
    $hasPeriodHistory = true;
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

    <title>Dashboard - FemTrack</title>

    <link rel="stylesheet" href="../css/style.css">


    <style>

        /* =========================================================
           FEMTRACK DASHBOARD
        ========================================================= */

        body {
            background: #fff7fb;
            color: #54243d;
        }


        /* =========================================================
           NAVBAR
        ========================================================= */

        .topbar {
            background: white;
            padding: 18px 6%;
            border-bottom: 1px solid #f5dce9;
            box-shadow: 0 4px 20px rgba(180, 80, 130, 0.08);
        }

        .logo {
            color: #3d1934;
        }

        .logo span {
            color: #d75b93;
        }

        .nav a {
            color: #71445a;
            border-radius: 20px;
            transition: 0.3s;
        }

        .nav a:hover,
        .nav a.active {
            background: #fde2ef;
            color: #b33f76;
        }

        .nav .logout {
            background: #c65384;
            color: white;
        }

        .nav .logout:hover {
            background: #a83b6b;
        }


        /* =========================================================
           HERO
        ========================================================= */

        .hero {
            margin: 38px 6% 25px;
            padding: 45px;

            border-radius: 30px;

            background: linear-gradient(
                135deg,
                #f9d8e8,
                #fff0f7
            );

            box-shadow:
                0 15px 35px rgba(190, 80, 130, 0.08);

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .eyebrow {
            color: #c15b89;
            letter-spacing: 2px;
            font-size: 12px;
            font-weight: 700;
        }

        .hero h1 {
            color: #652746;
            font-size: 40px;
            margin: 10px 0;
        }

        .lead {
            color: #8d6174;
            margin: 0;
        }

        .hero-decoration {
            font-size: 70px;
        }


        /* =========================================================
           DASHBOARD GRID
        ========================================================= */

        .dashboard-grid {
            margin: 25px 6% 60px;

            display: grid;

            grid-template-columns:
                1.25fr 1fr 1fr;

            gap: 22px;

            align-items: stretch;
        }


        /* =========================================================
           GENERAL CARD
        ========================================================= */

        .card {
            background: white;

            border-radius: 25px;

            border: 1px solid #f5dce8;

            box-shadow:
                0 12px 30px rgba(160, 70, 110, 0.07);

            transition: 0.3s;

            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-4px);

            box-shadow:
                0 18px 35px rgba(160, 70, 110, 0.11);
        }

        .card h2 {
            color: #6e2e4c;
            margin-top: 0;
        }


        /* =========================================================
           LEFT - FEMTRACK CARE CARD
        ========================================================= */

        .care-card {
            position: relative;

            padding: 30px;

            background: linear-gradient(
                145deg,
                #ffffff 0%,
                #fff3f8 100%
            );

            min-height: 385px;
        }

        .care-card::before {
            content: "";

            position: absolute;

            width: 170px;
            height: 170px;

            right: -65px;
            top: -65px;

            background: #fde1ed;

            border-radius: 50%;

            opacity: 0.55;
        }

        .care-card-header {
            position: relative;
            z-index: 1;

            display: flex;
            align-items: center;
            gap: 12px;

            margin-bottom: 18px;
        }

        .care-icon {
            width: 43px;
            height: 43px;

            border-radius: 50%;

            background: #fde1ed;

            color: #c94e82;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
        }

        .care-card h2 {
            margin: 0;

            font-size: 27px;
        }

        .care-intro {
            position: relative;
            z-index: 1;

            margin: 0 0 22px;

            color: #8b6677;

            font-size: 14px;

            line-height: 1.7;
        }


        /* =========================================================
           CARE ITEMS
        ========================================================= */

        .care-list {
            position: relative;
            z-index: 1;

            display: flex;

            flex-direction: column;

            gap: 11px;
        }

        .care-item {
            display: flex;

            align-items: center;

            gap: 13px;

            padding: 12px 14px;

            background: rgba(255, 255, 255, 0.78);

            border: 1px solid #f5dce8;

            border-radius: 15px;
        }

        .care-item-icon {
            width: 34px;
            height: 34px;

            flex-shrink: 0;

            border-radius: 50%;

            background: #fff0f6;

            color: #cc5083;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 15px;
        }

        .care-item strong {
            display: block;

            color: #813553;

            font-size: 13px;

            margin-bottom: 2px;
        }

        .care-item span {
            color: #997585;

            font-size: 11px;
        }


        /* =========================================================
           BOTTOM MESSAGE
        ========================================================= */

        .care-message {
            position: relative;
            z-index: 1;

            margin-top: 20px;

            color: #b24b78;

            font-size: 12px;

            font-weight: 600;
        }


        /* =========================================================
           QUICK ACTIONS
        ========================================================= */

        .action-card {
            padding: 30px;
        }

        .quick-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 18px;
        }

        .quick-link {
            text-decoration: none;

            padding: 15px;

            border-radius: 16px;

            background: #fff6fa;

            border: 1px solid #f7dce9;

            transition: 0.3s;
        }

        .quick-link:hover {
            background: #fde5ef;
            transform: translateX(5px);
        }

        .quick-link strong {
            display: block;
            color: #a83f70;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .quick-link span {
            color: #96717f;
            font-size: 12px;
        }


        /* =========================================================
           ICON
        ========================================================= */

        .card-icon {
            width: 42px;
            height: 42px;

            border-radius: 50%;

            background: #fde4ef;
            color: #c24f82;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;

            margin-bottom: 15px;
        }


        /* =========================================================
           ACCOUNT
        ========================================================= */

        .account-card {
            padding: 30px;
        }

        .details {
            display: flex;
            flex-direction: column;
            gap: 15px;

            margin-top: 20px;
        }

        .details div {
            padding: 13px 0;

            border-bottom: 1px solid #f5e4eb;
        }

        .details div:last-child {
            border-bottom: none;
        }

        .details dt {
            font-size: 11px;

            color: #a77a8c;

            margin-bottom: 5px;

            text-transform: uppercase;

            letter-spacing: .5px;
        }

        .details dd {
            margin: 0;

            color: #633049;

            font-weight: 600;

            font-size: 13px;

            word-break: break-word;
        }

        .member-badge {
            background: #fde1ed;

            color: #b23e73;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;
        }


        /* =========================================================
           SMALL FOOTER NOTE
        ========================================================= */

        .dashboard-note {
            margin-top: 18px;

            text-align: center;

            color: #a27b8b;

            font-size: 12px;
        }


        /* =========================================================
           RESPONSIVE DESIGN
           Desktop remains unchanged
        ========================================================= */


        /* =========================================================
           TABLET
        ========================================================= */

        @media (max-width: 1100px) {

            .dashboard-grid {
                grid-template-columns: 1fr 1fr;
            }

            .care-card {
                grid-column: 1 / -1;
            }

        }


        /* =========================================================
           TABLET / SMALL LAPTOP
        ========================================================= */

        @media (max-width: 900px) {

            .hero {
                margin: 25px 4%;
                padding: 30px;
                gap: 20px;
            }

            .hero h1 {
                font-size: 30px;
                line-height: 1.2;
            }

            .lead {
                font-size: 14px;
                line-height: 1.6;
            }

            .hero-decoration {
                font-size: 55px;
            }

            .dashboard-grid {
                margin: 25px 4% 45px;

                grid-template-columns: 1fr;

                gap: 18px;
            }

            .care-card {
                grid-column: auto;
            }

        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 600px) {

            /* HERO */

            .hero {
                margin: 20px 12px;

                padding: 27px 20px;

                border-radius: 25px;

                display: block;

                text-align: center;
            }

            .eyebrow {
                font-size: 10px;

                letter-spacing: 1.5px;
            }

            .hero h1 {
                font-size: 27px;

                line-height: 1.3;

                margin: 9px 0;
            }

            .lead {
                font-size: 13px;

                line-height: 1.7;
            }

            .hero-decoration {
                display: block;

                font-size: 42px;

                margin-top: 15px;
            }


            /* DASHBOARD GRID */

            .dashboard-grid {
                margin: 20px 12px 40px;

                gap: 16px;
            }


            /* GENERAL CARDS */

            .card {
                border-radius: 22px;
            }

            .card h2 {
                font-size: 22px;
            }


            /* CARE CARD */

            .care-card {
                min-height: auto;

                padding: 24px 20px;
            }

            .care-card-header {
                gap: 10px;

                align-items: center;
            }

            .care-icon {
                width: 40px;
                height: 40px;

                font-size: 18px;

                flex-shrink: 0;
            }

            .care-card h2 {
                font-size: 23px;
            }

            .care-intro {
                font-size: 13px;

                line-height: 1.7;

                margin-bottom: 18px;
            }


            /* CARE ITEMS */

            .care-list {
                gap: 9px;
            }

            .care-item {
                padding: 11px 12px;

                gap: 10px;
            }

            .care-item-icon {
                width: 32px;
                height: 32px;

                font-size: 14px;

                flex-shrink: 0;
            }

            .care-item strong {
                font-size: 12px;
            }

            .care-item span {
                font-size: 10px;

                line-height: 1.4;
            }

            .care-message {
                font-size: 11px;

                line-height: 1.5;

                margin-top: 17px;
            }


            /* QUICK ACTIONS */

            .action-card {
                padding: 24px 20px;
            }

            .quick-links {
                gap: 10px;

                margin-top: 15px;
            }

            .quick-link {
                padding: 14px;

                border-radius: 14px;
            }

            .quick-link strong {
                font-size: 13px;
            }

            .quick-link span {
                font-size: 11px;

                line-height: 1.5;
            }


            /* ACCOUNT */

            .account-card {
                padding: 24px 20px;
            }

            .details {
                gap: 12px;

                margin-top: 17px;
            }

            .details div {
                padding: 11px 0;
            }

            .details dt {
                font-size: 10px;
            }

            .details dd {
                font-size: 12px;

                overflow-wrap: anywhere;
            }

            .member-badge {
                padding: 5px 10px;

                font-size: 11px;
            }


            /* CARD ICON */

            .card-icon {
                width: 40px;
                height: 40px;

                font-size: 18px;

                margin-bottom: 12px;
            }

        }


        /* =========================================================
           SMALL PHONES
        ========================================================= */

        @media (max-width: 400px) {

            .hero {
                margin: 16px 8px;

                padding: 23px 16px;
            }

            .hero h1 {
                font-size: 24px;
            }

            .lead {
                font-size: 12px;
            }

            .hero-decoration {
                font-size: 36px;
            }


            /* DASHBOARD */

            .dashboard-grid {
                margin: 16px 8px 35px;

                gap: 14px;
            }


            /* CARDS */

            .care-card,
            .action-card,
            .account-card {
                padding: 21px 17px;
            }

            .care-card h2 {
                font-size: 21px;
            }

            .card h2 {
                font-size: 20px;
            }

            .care-item {
                align-items: flex-start;
            }

            .care-item-icon {
                margin-top: 1px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<?php include "../includes/nav.php"; ?>


<!-- =========================================================
     HERO
========================================================= -->

<section class="hero">

    <div>

        <div class="eyebrow">
            ♡ YOUR PERSONAL DASHBOARD
        </div>

        <h1>
            Welcome,
            <?php echo htmlspecialchars($user["name"]); ?> ♡
        </h1>

        <p class="lead">
            A calm little space to understand, track and care for yourself.
        </p>

    </div>


    <div class="hero-decoration">
        🌸
    </div>

</section>



<!-- =========================================================
     DASHBOARD CONTENT
========================================================= -->

<section class="dashboard-grid">


    <!-- =====================================================
         LEFT CARD
    ====================================================== -->

    <article class="card care-card">

        <div class="care-card-header">

            <div class="care-icon">
                ♡
            </div>

            <div>

                <div class="eyebrow">
                    A LITTLE CARE
                </div>

                <h2>
                    Take care of you.
                </h2>

            </div>

        </div>


        <p class="care-intro">

            FemTrack gives you a simple space to keep track
            of your period and how you're feeling.

        </p>


        <div class="care-list">


            <div class="care-item">

                <div class="care-item-icon">
                    ♡
                </div>

                <div>

                    <strong>
                        Track your period
                    </strong>

                    <span>
                        Keep your period dates organized.
                    </span>

                </div>

            </div>


            <div class="care-item">

                <div class="care-item-icon">
                    ✿
                </div>

                <div>

                    <strong>
                        Notice how you feel
                    </strong>

                    <span>
                        Record symptoms throughout your cycle.
                    </span>

                </div>

            </div>


            <div class="care-item">

                <div class="care-item-icon">
                    ♡
                </div>

                <div>

                    <strong>
                        Keep your records
                    </strong>

                    <span>
                        Find your information whenever you need it.
                    </span>

                </div>

            </div>


        </div>


        <div class="care-message">
            Your health. Your records. Your space. ♡
        </div>

    </article>



    <!-- =====================================================
         QUICK ACTIONS
    ====================================================== -->

    <article class="card action-card">

        <div class="card-icon">
            ♡
        </div>

        <h2>
            Quick actions
        </h2>


        <div class="quick-links">


            <!-- LOG PERIOD -->

            <a
                class="quick-link"
                href="period-log.php"
            >

                <strong>
                    ＋ Log a period
                </strong>

                <span>
                    Add a new cycle record
                </span>

            </a>


            <?php if ($hasPeriodHistory): ?>

                <!-- VIEW HISTORY ONLY AFTER FIRST PERIOD IS LOGGED -->

                <a
                    class="quick-link"
                    href="period-history.php"
                >

                    <strong>
                        ♡ View history
                    </strong>

                    <span>
                        Review your saved records
                    </span>

                </a>

            <?php endif; ?>


            <!-- TRACK SYMPTOMS -->

            <a
                class="quick-link"
                href="track-symptoms.php"
            >

                <strong>
                    ✿ Track symptoms
                </strong>

                <span>
                    Record how you're feeling
                </span>

            </a>


        </div>

    </article>



    <!-- =====================================================
         ACCOUNT
    ====================================================== -->

    <article class="card account-card">

        <div class="card-icon">
            ♔
        </div>

        <h2>
            Your account
        </h2>


        <dl class="details">


            <div>

                <dt>
                    Name
                </dt>

                <dd>

                    <?php
                    echo htmlspecialchars($user["name"]);
                    ?>

                </dd>

            </div>


            <div>

                <dt>
                    Email
                </dt>

                <dd>

                    <?php
                    echo htmlspecialchars($user["email"]);
                    ?>

                </dd>

            </div>


            <div>

                <dt>
                    Account type
                </dt>

                <dd>

                    <span class="member-badge">
                        Member ♡
                    </span>

                </dd>

            </div>


        </dl>

    </article>


</section>


</body>

</html>