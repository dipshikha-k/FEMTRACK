<?php

session_start();

require_once __DIR__ . "/../config/database.php";

/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION["user_id"])) {
    $_SESSION["redirect_after_login"] = "user/period-history.php";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];


/* =========================================================
   GET USER'S PERIOD HISTORY
========================================================= */

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

    <title>Period History | FemTrack</title>

    <!-- Main FemTrack stylesheet -->
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

            color: #4a2634;

            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;
        }


        /* =========================================================
           MAIN PAGE
        ========================================================= */

        .history-page {

            max-width: 1180px;

            margin: 0 auto;

            padding: 58px 24px 80px;
        }


        /* =========================================================
           PAGE HEADING
        ========================================================= */

        .page-heading {

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
           HISTORY CARD
        ========================================================= */

        .history-card {

            position: relative;

            overflow: hidden;

            background: rgba(255, 255, 255, 0.94);

            border: 1px solid #f3dce6;

            border-radius: 26px;

            padding: 30px;

            box-shadow:
                0 22px 60px rgba(232, 101, 164, 0.10);
        }


        .history-card::before {

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
           CARD HEADER
        ========================================================= */

        .history-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            padding-bottom: 22px;

            margin-bottom: 25px;

            border-bottom: 1px solid #f4e4eb;
        }


        .history-header h2 {

            margin: 0;

            color: #432535;

            font-size: 22px;

            font-weight: 800;
        }


        .history-header p {

            margin: 6px 0 0;

            color: #987b89;

            font-size: 13px;

            line-height: 1.6;
        }


        /* =========================================================
           BUTTON
        ========================================================= */

        .log-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 12px 20px;

            border-radius: 14px;

            background:
                linear-gradient(
                    90deg,
                    #d73b80,
                    #ef5f9a
                );

            color: #ffffff;

            text-decoration: none;

            font-size: 13px;

            font-weight: 800;

            white-space: nowrap;

            box-shadow:
                0 10px 22px rgba(218, 52, 123, .18);

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }


        .log-button:hover {

            color: #ffffff;

            text-decoration: none;

            transform: translateY(-2px);

            box-shadow:
                0 14px 28px rgba(218, 52, 123, .24);
        }


        /* =========================================================
           TABLE
        ========================================================= */

        .table-wrap {

            width: 100%;

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 700px;
        }


        thead th {

            padding: 15px 14px;

            background: #fff0f6;

            color: #a03f67;

            font-size: 11px;

            font-weight: 800;

            letter-spacing: .5px;

            text-transform: uppercase;

            text-align: left;

            border-bottom: 1px solid #f4dce6;
        }


        tbody td {

            padding: 17px 14px;

            color: #634452;

            font-size: 13px;

            border-bottom: 1px solid #f5e3ea;

            vertical-align: middle;
        }


        tbody tr {

            transition:
                background .18s ease;
        }


        tbody tr:hover {

            background: #fff9fb;
        }


        tbody tr:last-child td {

            border-bottom: none;
        }


        /* =========================================================
           FLOW TAG
        ========================================================= */

        .flow-tag {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 6px 12px;

            border-radius: 999px;

            background: #ffe6f0;

            border: 1px solid #ffd0e0;

            color: #c72d6e;

            font-size: 11px;

            font-weight: 800;
        }


        /* =========================================================
           EDIT LINK
        ========================================================= */

        .edit-link {

            color: #d73b80;

            text-decoration: none;

            font-size: 12px;

            font-weight: 800;
        }


        .edit-link:hover {

            color: #b92766;

            text-decoration: underline;
        }


        /* =========================================================
           EMPTY STATE
        ========================================================= */

        .empty-state {

            text-align: center;

            padding: 65px 20px;
        }


        .empty-icon {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 65px;

            height: 65px;

            margin: 0 auto 20px;

            border-radius: 20px;

            background: #fff0f6;

            color: #d73b80;

            font-size: 28px;
        }


        .empty-state h2 {

            margin: 0 0 10px;

            color: #432535;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 27px;

            font-style: italic;
        }


        .empty-state p {

            max-width: 500px;

            margin: 0 auto 25px;

            color: #987b89;

            font-size: 13px;

            line-height: 1.7;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 700px) {

            .history-page {

                padding: 35px 15px 60px;
            }


            .page-heading h1 {

                font-size: 37px;
            }


            .heading-soft {

                font-size: 17px;
            }


            .history-card {

                padding: 20px 15px;

                border-radius: 21px;
            }


            .history-header {

                flex-direction: column;

                align-items: flex-start;
            }


            .log-button {

                width: 100%;
            }


            .table-wrap {

                margin-left: -2px;

                margin-right: -2px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     SAME NAVIGATION AS TRACK-SYMPTOMS.PHP
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
            href="period-history.php"
        >
            History
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

<main class="history-page">


    <!-- PAGE INTRO -->

    <div class="page-heading">

        <div class="page-eyebrow">
            Your cycle records ♡
        </div>


        <h1>
            Period History
        </h1>


        <span class="heading-soft">
            Your cycle, your records, your little timeline.
        </span>


        <p>
            Keep track of the periods you've logged and look back
            at your cycle information whenever you need it.
        </p>

    </div>



    <!-- =====================================================
         HISTORY CARD
    ====================================================== -->

    <section class="history-card">


        <div class="history-header">

            <div>

                <h2>
                    Your Period Records ♡
                </h2>

                <p>
                    A simple view of the periods you have logged.
                </p>

            </div>


            <a
                class="log-button"
                href="period-log.php"
            >
                + Log New Period
            </a>

        </div>



        <?php if (mysqli_num_rows($result) > 0): ?>


            <!-- TABLE -->

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                S.N
                            </th>

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
                                    <?php
                                    echo $count++;
                                    ?>
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

                                    <span class="flow-tag">

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
                                        class="edit-link"
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


            <!-- EMPTY STATE -->

            <div class="empty-state">

                <div class="empty-icon">
                    ♡
                </div>


                <h2>
                    No Period Records Yet
                </h2>


                <p>
                    When you log your first period, your cycle
                    history will appear here.
                </p>


                <a
                    class="log-button"
                    href="period-log.php"
                >
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