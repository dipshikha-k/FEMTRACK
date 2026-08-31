
<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

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
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - FemTrack</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* ===== FEMTRACK DASHBOARD ===== */

        body {
            background: #fff7fb;
            color: #54243d;
        }

        /* NAVBAR */

        .topbar {
            background: white;
            padding: 18px 6%;
            border-bottom: 1px solid #f5dce9;
            box-shadow: 0 4px 20px rgba(180, 80, 130, 0.08);
        }

        .logo {
            color: #8f3d68;
        }

        .logo span {
            color: #e78ab5;
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


        /* HERO */

        .hero {
            margin: 45px 6% 25px;
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
        }

        .hero h1 {
            color: #652746;
            font-size: 40px;
        }

        .lead {
            color: #8d6174;
        }

        .hero-decoration {
            font-size: 70px;
        }


        /* DASHBOARD */

        .dashboard-grid {
            margin: 25px 6% 60px;

            display: grid;

            grid-template-columns:
                1.2fr 1fr 1fr;

            gap: 22px;
        }

        .card {
            background: white;

            border-radius: 25px;

            border: 1px solid #f5dce8;

            box-shadow:
                0 12px 30px rgba(160, 70, 110, 0.08);

            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);

            box-shadow:
                0 18px 35px rgba(160, 70, 110, 0.13);
        }

        .card h2 {
            color: #6e2e4c;
        }


        /* WELCOME CARD */

        .welcome-card {
            background: linear-gradient(
                145deg,
                #ffffff,
                #fff0f7
            );
        }

        .welcome-card h2 {
            font-size: 29px;
        }

        .welcome-card h2 span {
            color: #d75d91;
        }

        .muted {
            color: #8d6978;
            line-height: 1.7;
        }

        .flower {
            position: absolute;
            right: 25px;
            bottom: 20px;
            font-size: 45px;
        }


        /* QUICK ACTIONS */

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
        }

        .quick-link span {
            color: #96717f;
            font-size: 13px;
        }


        /* ICON */

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


        /* ACCOUNT */

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

        .details dt {
            font-size: 12px;
            color: #a77a8c;
            margin-bottom: 5px;
        }

        .details dd {
            margin: 0;
            color: #633049;
            font-weight: 600;
        }

        .member-badge {
            background: #fde1ed;
            color: #b23e73;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;
        }


        /* MOBILE */

        @media (max-width: 900px) {

            .topbar {
                flex-direction: column;
                gap: 15px;
            }

            .nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero {
                margin: 25px 4%;
                padding: 30px;
            }

            .hero h1 {
                font-size: 30px;
            }

            .hero-decoration {
                display: none;
            }

            .dashboard-grid {
                margin: 25px 4%;
                grid-template-columns: 1fr;
            }
        }

    </style>

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

            <a class="active" href="dashboard.php">
                Dashboard
            </a>

            <a href="track-symptoms.php">
                Track Symptoms
            </a>

            <a href="reports.php">
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


    <section class="hero">

        <div>

            <div class="eyebrow">
                ♡ YOUR PERSONAL DASHBOARD
            </div>

            <h1>
                Welcome back,
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


    <section class="dashboard-grid">


        <article class="card welcome-card">

            <div class="eyebrow">
                TODAY WITH FEMTRACK
            </div>

            <h2>
                Your wellbeing,<br>
                <span>your way.</span>
            </h2>

            <p class="muted">
                Your body deserves attention, kindness and care.
                Keep your cycle records safely in one beautiful place.
            </p>

            <div class="flower">
                🌷
            </div>

        </article>


        <article class="card">

            <div class="card-icon">
                ♡
            </div>

            <h2>
                Quick actions
            </h2>

            <div class="quick-links">

                <a class="quick-link" href="period-log.php">

                    <strong>
                        ＋ Log a period
                    </strong>

                    <span>
                        Add a new cycle record
                    </span>

                </a>


                <a class="quick-link" href="period-history.php">

                    <strong>
                        ♡ View history
                    </strong>

                    <span>
                        Review your saved records
                    </span>

                </a>


                <a class="quick-link" href="track-symptoms.php">

                    <strong>
                        ✿ Track symptoms
                    </strong>

                    <span>
                        Record how you're feeling
                    </span>

                </a>

            </div>

        </article>


        <article class="card">

            <div class="card-icon">
                ♔
            </div>

            <h2>
                Your account
            </h2>

            <dl class="details">

                <div>

                    <dt>Name</dt>

                    <dd>
                        <?php echo htmlspecialchars($user["name"]); ?>
                    </dd>

                </div>


                <div>

                    <dt>Email</dt>

                    <dd>
                        <?php echo htmlspecialchars($user["email"]); ?>
                    </dd>

                </div>


                <div>

                    <dt>Account type</dt>

                    <dd>
                        <span class="member-badge">
                            Member ♡
                        </span>
                    </dd>

                </div>

            </dl>

        </article>

    </section>

</main>

</body>

</html>

