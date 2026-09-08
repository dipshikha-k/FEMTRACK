<?php

session_start();

require_once __DIR__ . "/../../config/database.php";


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    header("Location: ../../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Admin Check
|--------------------------------------------------------------------------
|
| This assumes your users table has a "role" column.
| Admin users should have role = "admin".
|
*/

$user_id = $_SESSION["user_id"];


$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, role FROM users WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$admin = mysqli_fetch_assoc($result);


if (!$admin || strtolower($admin["role"]) !== "admin") {

    header("Location: ../dashboard.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get All Symptom Records
|--------------------------------------------------------------------------
*/

$query = "
    SELECT
        symptom_logs.*,
        users.name,
        users.email
    FROM symptom_logs
    INNER JOIN users
        ON symptom_logs.user_id = users.id
    ORDER BY symptom_logs.symptom_date DESC,
             symptom_logs.created_at DESC
";

$records = mysqli_query($conn, $query);


$total_records = 0;

if ($records) {
    $total_records = mysqli_num_rows($records);
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

    <title>Symptom Records | FemTrack Admin</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff7fa;
            color: #351526;
        }


        /* NAVBAR */

        .navbar {
            width: 100%;
            background: white;
            border-bottom: 1px solid #f0dce6;
            padding: 18px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .logo {
            font-size: 26px;
            font-weight: 800;
            color: #c62f6d;
        }


        .admin-badge {
            background: #fff0f5;
            color: #c62f6d;
            padding: 9px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 800;
        }


        .logout {
            margin-left: 15px;
            text-decoration: none;
            color: #654457;
            font-size: 14px;
            font-weight: 700;
        }


        /* PAGE */

        .page {
            width: 90%;
            max-width: 1500px;
            margin: 45px auto;
        }


        .heading {
            margin-bottom: 30px;
        }


        .heading small {
            color: #c62f6d;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
            font-weight: 800;
        }


        .heading h1 {
            font-family: Georgia, serif;
            font-size: 44px;
            margin: 10px 0;
        }


        .heading p {
            color: #806174;
            font-size: 15px;
        }


        /* SUMMARY */

        .summary {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }


        .summary-card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 15px 45px rgba(73, 28, 52, .06);
        }


        .summary-number {
            font-family: Georgia, serif;
            font-size: 35px;
            font-weight: 800;
            color: #c62f6d;
        }


        .summary-label {
            margin-top: 5px;
            color: #806174;
            font-size: 13px;
            font-weight: 700;
        }


        .info-card {
            background: #fff0f5;
            border: 1px solid #f3d6e3;
            border-radius: 22px;
            padding: 25px;
        }


        .info-card strong {
            color: #8e2752;
        }


        .info-card p {
            margin: 8px 0 0;
            color: #806174;
            font-size: 14px;
            line-height: 1.6;
        }


        /* TABLE CARD */

        .table-card {
            background: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(73, 28, 52, .08);
            overflow: hidden;
        }


        .table-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }


        .table-heading h2 {
            margin: 0;
            font-family: Georgia, serif;
            font-size: 27px;
        }


        .record-count {
            color: #806174;
            font-size: 13px;
            font-weight: 700;
        }


        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }


        table {
            width: 100%;
            min-width: 1500px;
            border-collapse: collapse;
        }


        th {
            text-align: left;
            background: #fff5f8;
            color: #744d61;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 15px 13px;
            border-bottom: 1px solid #f0dce6;
            white-space: nowrap;
        }


        td {
            padding: 16px 13px;
            border-bottom: 1px solid #f5e8ee;
            font-size: 13px;
            color: #654457;
            vertical-align: top;
        }


        tr:hover td {
            background: #fffafd;
        }


        .user-name {
            color: #351526;
            font-weight: 800;
        }


        .user-email {
            color: #967b8b;
            font-size: 12px;
            margin-top: 4px;
        }


        .severity {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
        }


        .mild {
            background: #edf8ef;
            color: #3d774d;
        }


        .moderate {
            background: #fff6df;
            color: #98711d;
        }


        .severe {
            background: #fff0f3;
            color: #b52c57;
        }


        .details {
            max-width: 220px;
            line-height: 1.6;
            white-space: normal;
        }


        .empty {
            text-align: center;
            padding: 70px 20px;
            color: #967b8b;
        }


        .empty h3 {
            font-family: Georgia, serif;
            color: #654457;
            font-size: 25px;
        }


        /* MOBILE */

        @media (max-width: 700px) {

            .navbar {
                padding: 18px 5%;
            }

            .admin-badge {
                display: none;
            }

            .heading h1 {
                font-size: 34px;
            }

            .summary {
                grid-template-columns: 1fr;
            }

            .table-card {
                padding: 18px;
            }

        }

    </style>

</head>


<body>


<nav class="navbar">


    <div class="logo">
        FemTrack
    </div>


    <div>

        <span class="admin-badge">
            Admin Panel
        </span>

        <a
            href="../../logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>


</nav>


<main class="page">


    <div class="heading">

        <small>FemTrack Administration</small>

        <h1>
            Symptom Records
        </h1>

        <p>
            View and manage symptom check-ins submitted by FemTrack users.
        </p>

    </div>


    <div class="summary">


        <div class="summary-card">

            <div class="summary-number">
                <?php echo $total_records; ?>
            </div>

            <div class="summary-label">
                Total symptom check-ins
            </div>

        </div>


        <div class="info-card">

            <strong>
                Private admin view
            </strong>

            <p>
                These records are collected from users'
                symptom check-ins and are available here
                for authorized administration only.
            </p>

        </div>


    </div>


    <section class="table-card">


        <div class="table-heading">

            <h2>
                All Check-Ins
            </h2>

            <div class="record-count">

                <?php echo $total_records; ?>
                record(s)

            </div>

        </div>


        <?php if ($total_records > 0): ?>


            <div class="table-wrapper">

                <table>


                    <thead>

                        <tr>

                            <th>
                                User
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Symptoms
                            </th>

                            <th>
                                Severity
                            </th>

                            <th>
                                Mood
                            </th>

                            <th>
                                Mood Swings
                            </th>

                            <th>
                                Cravings
                            </th>

                            <th>
                                Bloating
                            </th>

                            <th>
                                Sleep
                            </th>

                            <th>
                                Energy
                            </th>

                            <th>
                                Pain
                            </th>

                            <th>
                                Skin
                            </th>

                            <th>
                                Medication
                            </th>

                            <th>
                                Notes
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php while ($row = mysqli_fetch_assoc($records)): ?>


                            <tr>


                                <td>

                                    <div class="user-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["name"]
                                        );
                                        ?>

                                    </div>

                                    <div class="user-email">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["email"]
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row["symptom_date"]
                                    );
                                    ?>

                                </td>


                                <td>

                                    <div class="details">

                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                $row["symptoms"]
                                            )
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <span
                                        class="severity <?php echo strtolower($row["severity"]); ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $row["severity"]
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["mood"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["mood_swings"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["cravings"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["bloating"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["sleep"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["energy"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["pain"] ?: "-"); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($row["skin"] ?: "-"); ?>
                                </td>


                                <td>

                                    <div class="details">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["medication"] ?: "-"
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <div class="details">

                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                $row["notes"] ?: "-"
                                            )
                                        );
                                        ?>

                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    </tbody>


                </table>

            </div>


        <?php else: ?>


            <div class="empty">

                <h3>
                    No symptom records yet.
                </h3>

                <p>
                    Once users submit their check-ins,
                    they will appear here.
                </p>

            </div>


        <?php endif; ?>


    </section>


</main>


</body>

</html>
