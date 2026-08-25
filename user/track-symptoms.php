<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Create symptom_logs table if it does not exist
|--------------------------------------------------------------------------
*/

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS symptom_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    symptom_date DATE NOT NULL,
    symptoms VARCHAR(255) NOT NULL,
    severity VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

/*
|--------------------------------------------------------------------------
| Save symptom entry
|--------------------------------------------------------------------------
*/

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $symptomDate = $_POST['symptom_date'] ?? '';
    $symptoms = trim($_POST['symptoms'] ?? '');
    $severity = $_POST['severity'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (
        $symptomDate === '' ||
        $symptoms === '' ||
        !in_array($severity, ['Mild', 'Moderate', 'Severe'], true)
    ) {

        $message = 'Please complete the date, symptoms, and severity fields.';
        $messageType = 'error';

    } else {

        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO symptom_logs
            (user_id, symptom_date, symptoms, severity, notes)
            VALUES (?, ?, ?, ?, ?)'
        );

        mysqli_stmt_bind_param(
            $stmt,
            'issss',
            $_SESSION['user_id'],
            $symptomDate,
            $symptoms,
            $severity,
            $notes
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Your symptom entry has been saved successfully.';
            $messageType = 'success';
        } else {
            $message = 'Unable to save the entry. Please try again.';
            $messageType = 'error';
        }

        mysqli_stmt_close($stmt);
    }
}

/*
|--------------------------------------------------------------------------
| Get recent symptoms
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    'SELECT symptom_date, symptoms, severity, notes
     FROM symptom_logs
     WHERE user_id = ?
     ORDER BY symptom_date DESC, id DESC
     LIMIT 5'
);

mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

$recentSymptoms = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Track Symptoms - FemTrack</title>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap"
          rel="stylesheet">


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: #42152f;
            background:
                radial-gradient(
                    circle at 85% 15%,
                    rgba(247, 164, 202, 0.30),
                    transparent 28%
                ),
                linear-gradient(
                    135deg,
                    #fff9fc 0%,
                    #fff1f7 48%,
                    #fce6f1 100%
                );

            min-height: 100vh;
        }


        /* =========================================================
           NAVBAR
        ========================================================= */

        .topbar {
            width: 100%;
            height: 80px;

            background: rgba(255, 255, 255, 0.92);

            border-bottom: 1px solid #f3d9e5;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 7%;

            position: sticky;
            top: 0;
            z-index: 1000;

            backdrop-filter: blur(15px);
        }


        .logo {
            display: flex;
            align-items: center;

            text-decoration: none;

            color: #42152f;

            font-size: 22px;
            font-weight: 800;

            gap: 9px;
        }


        .logo span {
            color: #df3d83;
        }


        .logo-mark {
            width: 43px;
            height: 43px;

            border-radius: 50%;

            object-fit: cover;

            box-shadow:
                0 5px 15px rgba(220, 52, 126, 0.18);
        }


        .nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }


        .nav a {
            text-decoration: none;

            color: #57243f;

            font-size: 14px;
            font-weight: 600;

            padding: 10px 15px;

            border-radius: 30px;

            transition: 0.25s ease;
        }


        .nav a:hover {
            color: #d92f7c;
            background: #fff0f7;
        }


        .nav a.active {
            color: #d72f7c;
            background: #fff0f7;
        }


        .nav .logout {
            color: white;

            background: #48152f;

            margin-left: 8px;

            padding: 11px 20px;
        }


        .nav .logout:hover {
            background: #d92f7c;
            color: white;
        }


        /* =========================================================
           MAIN
        ========================================================= */

        .page {
            width: 100%;
            max-width: 1250px;

            margin: 0 auto;

            padding: 65px 6% 80px;
        }


        /* =========================================================
           HERO
        ========================================================= */

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 45px;
        }


        .hero-content {
            max-width: 700px;
        }


        .eyebrow {
            color: #d6387d;

            font-size: 12px;
            font-weight: 800;

            letter-spacing: 3px;

            text-transform: uppercase;

            margin-bottom: 14px;
        }


        .hero h1 {
            font-family: 'Playfair Display', serif;

            font-size: clamp(45px, 6vw, 72px);

            line-height: 0.98;

            letter-spacing: -2px;

            color: #41132f;

            margin-bottom: 20px;
        }


        .hero h1 em {
            color: #d83d82;
            font-weight: 600;
        }


        .hero p {
            color: #81516b;

            font-size: 17px;

            line-height: 1.7;

            max-width: 580px;
        }


        .hero-decoration {
            width: 110px;
            height: 110px;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    #f8b5d2,
                    #e75b9b
                );

            display: flex;
            align-items: center;
            justify-content: center;

            box-shadow:
                0 20px 45px rgba(207, 59, 125, 0.20);

            transform: rotate(8deg);
        }


        .hero-decoration::before {
            content: '♡';

            color: white;

            font-size: 55px;
        }


        /* =========================================================
           GRID
        ========================================================= */

        .main-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1.15fr);

            gap: 28px;

            align-items: start;
        }


        /* =========================================================
           CARD
        ========================================================= */

        .card {
            background: rgba(255, 255, 255, 0.88);

            border: 1px solid rgba(238, 184, 211, 0.55);

            border-radius: 28px;

            box-shadow:
                0 18px 50px rgba(91, 29, 62, 0.08);

            backdrop-filter: blur(14px);
        }


        .form-card {
            padding: 34px;
        }


        .card-title {
            font-family: 'Playfair Display', serif;

            font-size: 29px;

            color: #42152f;

            margin-bottom: 7px;
        }


        .card-subtitle {
            color: #93637b;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 28px;
        }


        /* =========================================================
           MESSAGE
        ========================================================= */

        .notice {
            border-radius: 15px;

            padding: 14px 16px;

            font-size: 14px;

            margin-bottom: 22px;

            font-weight: 600;
        }


        .notice.success {
            background: #f1fbf6;
            color: #287354;
            border: 1px solid #c9ecd9;
        }


        .notice.error {
            background: #fff1f3;
            color: #b52d55;
            border: 1px solid #f3c8d5;
        }


        /* =========================================================
           FORM
        ========================================================= */

        .form-stack {
            display: flex;
            flex-direction: column;

            gap: 20px;
        }


        .form-group {
            display: flex;
            flex-direction: column;

            gap: 8px;
        }


        .form-group label {
            color: #54233e;

            font-size: 13px;

            font-weight: 700;
        }


        .input,
        select {
            width: 100%;

            border: 1px solid #ecd3df;

            background: #fffafd;

            border-radius: 14px;

            padding: 14px 16px;

            color: #4b1d37;

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition: 0.25s ease;
        }


        .input:focus,
        select:focus {
            border-color: #e25b98;

            box-shadow:
                0 0 0 4px rgba(226, 91, 152, 0.10);

            background: white;
        }


        .input::placeholder {
            color: #b999a9;
        }


        /* =========================================================
           BUTTON
        ========================================================= */

        .button {
            width: 100%;

            border: none;

            border-radius: 15px;

            padding: 15px 22px;

            margin-top: 5px;

            color: white;

            font-family: inherit;

            font-size: 14px;

            font-weight: 800;

            cursor: pointer;

            background:
                linear-gradient(
                    100deg,
                    #a94ee7,
                    #e63f93
                );

            box-shadow:
                0 12px 25px rgba(207, 59, 139, 0.22);

            transition: 0.25s ease;
        }


        .button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 16px 30px rgba(207, 59, 139, 0.30);
        }


        /* =========================================================
           RECENT ENTRIES
        ========================================================= */

        .recent-card {
            overflow: hidden;
        }


        .recent-header {
            padding: 30px 32px 22px;

            border-bottom: 1px solid #f1dfe8;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .recent-header h2 {
            font-family: 'Playfair Display', serif;

            font-size: 29px;

            color: #42152f;
        }


        .recent-header span {
            font-size: 12px;

            color: #d43a7c;

            background: #fff0f7;

            padding: 8px 12px;

            border-radius: 30px;

            font-weight: 700;
        }


        .entries {
            padding: 8px 0;
        }


        .entry {
            padding: 21px 32px;

            display: grid;

            grid-template-columns: 90px 1fr auto;

            gap: 18px;

            align-items: center;

            border-bottom: 1px solid #f7e7ef;

            transition: 0.2s ease;
        }


        .entry:last-child {
            border-bottom: none;
        }


        .entry:hover {
            background: #fff8fb;
        }


        .entry-date {
            font-size: 12px;

            font-weight: 700;

            color: #9c7087;
        }


        .entry-symptoms {
            color: #4d1d38;

            font-size: 14px;

            font-weight: 600;
        }


        .entry-notes {
            color: #9b7187;

            font-size: 12px;

            margin-top: 5px;

            line-height: 1.4;
        }


        .tag {
            display: inline-flex;

            padding: 7px 12px;

            border-radius: 30px;

            font-size: 11px;

            font-weight: 800;

            background: #fff0f7;

            color: #d43b7d;
        }


        .empty {
            padding: 45px 30px;

            text-align: center;

            color: #a2788d;

            font-size: 14px;
        }


        /* =========================================================
           FOOTER NOTE
        ========================================================= */

        .privacy-note {
            text-align: center;

            margin-top: 35px;

            color: #9a7186;

            font-size: 12px;
        }


        .privacy-note span {
            color: #df3c82;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 900px) {

            .topbar {
                padding: 0 4%;
            }


            .nav a {
                padding: 8px 9px;

                font-size: 12px;
            }


            .nav .logout {
                padding: 9px 13px;
            }


            .page {
                padding: 50px 4% 60px;
            }


            .main-grid {
                grid-template-columns: 1fr;
            }


            .hero-decoration {
                display: none;
            }

        }


        @media (max-width: 650px) {

            .topbar {
                height: auto;

                min-height: 75px;

                flex-wrap: wrap;

                gap: 10px;

                padding: 12px 5%;
            }


            .nav {
                width: 100%;

                overflow-x: auto;

                padding-bottom: 4px;
            }


            .nav a {
                white-space: nowrap;
            }


            .hero h1 {
                font-size: 48px;
            }


            .form-card {
                padding: 25px 20px;
            }


            .recent-header {
                padding: 25px 20px 20px;
            }


            .entry {
                grid-template-columns: 1fr;

                gap: 8px;

                padding: 20px;
            }


            .entry .tag {
                width: fit-content;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="topbar">

    <a href="dashboard.php" class="logo">

        <img
            src="../assets/femtrack-mark.jpeg"
            alt="FemTrack logo"
            class="logo-mark"
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

        <a href="track-symptoms.php" class="active">
            Track Symptoms
        </a>

        <a href="reports.php">
            Reports
        </a>

        <a href="/FEMTRACK/about.php">About Us</a>

        <a href="../logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>



<!-- =========================================================
     PAGE
========================================================= -->

<main class="page">


    <!-- HERO -->

    <section class="hero">

        <div class="hero-content">

            <div class="eyebrow">
                Your daily check-in
            </div>

            <h1>
                Listen to your
                <em>body.</em>
            </h1>

            <p>
                Keep track of how you feel throughout your cycle.
                Small details today can help you understand your
                patterns tomorrow.
            </p>

        </div>


        <div class="hero-decoration"></div>

    </section>



    <!-- MAIN CONTENT -->

    <section class="main-grid">


        <!-- =====================================================
             FORM CARD
        ====================================================== -->

        <div class="card form-card">

            <h2 class="card-title">
                How are you feeling?
            </h2>

            <p class="card-subtitle">
                Take a little moment for yourself and record
                what your body is telling you today.
            </p>


            <?php if ($message !== ''): ?>

                <div class="notice <?php echo $messageType; ?>">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>


            <form method="POST" class="form-stack">


                <!-- DATE -->

                <div class="form-group">

                    <label for="symptom_date">
                        Date
                    </label>

                    <input
                        class="input"
                        type="date"
                        id="symptom_date"
                        name="symptom_date"
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >

                </div>


                <!-- SYMPTOMS -->

                <div class="form-group">

                    <label for="symptoms">
                        What are you experiencing?
                    </label>

                    <input
                        class="input"
                        type="text"
                        id="symptoms"
                        name="symptoms"
                        placeholder="e.g. cramps, headache, fatigue"
                        required
                    >

                </div>


                <!-- SEVERITY -->

                <div class="form-group">

                    <label for="severity">
                        How intense is it?
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


                <!-- NOTES -->

                <div class="form-group">

                    <label for="notes">
                        Notes <span style="font-weight:400;color:#aa8798;">
                            (optional)
                        </span>
                    </label>

                    <input
                        class="input"
                        type="text"
                        id="notes"
                        name="notes"
                        placeholder="Anything else you'd like to remember?"
                    >

                </div>


                <!-- BUTTON -->

                <button
                    class="button"
                    type="submit"
                >
                    Save my symptoms&nbsp; →
                </button>


            </form>

        </div>



        <!-- =====================================================
             RECENT ENTRIES
        ====================================================== -->

        <div class="card recent-card">


            <div class="recent-header">

                <h2>
                    Recent entries
                </h2>

                <span>
                    Last 5
                </span>

            </div>


            <div class="entries">


                <?php if (mysqli_num_rows($recentSymptoms) === 0): ?>


                    <div class="empty">

                        <div style="font-size:35px;margin-bottom:10px;">
                            ♡
                        </div>

                        No symptom entries yet.

                        <br>

                        Start your first check-in today.

                    </div>


                <?php else: ?>


                    <?php while ($entry = mysqli_fetch_assoc($recentSymptoms)): ?>


                        <div class="entry">


                            <div class="entry-date">

                                <?php
                                echo date(
                                    'M d, Y',
                                    strtotime($entry['symptom_date'])
                                );
                                ?>

                            </div>


                            <div>

                                <div class="entry-symptoms">

                                    <?php
                                    echo htmlspecialchars(
                                        $entry['symptoms']
                                    );
                                    ?>

                                </div>


                                <?php if (!empty($entry['notes'])): ?>

                                    <div class="entry-notes">

                                        <?php
                                        echo htmlspecialchars(
                                            $entry['notes']
                                        );
                                        ?>

                                    </div>

                                <?php endif; ?>

                            </div>


                            <div>

                                <span class="tag">

                                    <?php
                                    echo htmlspecialchars(
                                        $entry['severity']
                                    );
                                    ?>

                                </span>

                            </div>


                        </div>


                    <?php endwhile; ?>


                <?php endif; ?>


            </div>

        </div>


    </section>



    <!-- PRIVACY -->

    <div class="privacy-note">

        <span>♡</span>
        Your FemTrack information stays private and personal.

    </div>


</main>


</body>

</html>