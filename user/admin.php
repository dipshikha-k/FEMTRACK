
<?php

session_start();

require_once __DIR__ . "/../config/database.php";


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    $_SESSION["redirect_after_login"] = "user/admin.php";

    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET CURRENT USER
|--------------------------------------------------------------------------
*/

$user_id = $_SESSION["user_id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, role
     FROM users
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$current_user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| ADMIN CHECK
|--------------------------------------------------------------------------
*/

if (
    !$current_user ||
    $current_user["role"] !== "admin"
) {

    header("Location: dashboard.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| DASHBOARD STATISTICS
|--------------------------------------------------------------------------
*/


/* Total users */

$total_users = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_users = (int) $row["total"];
}


/* Total period records */

$total_periods = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM period_logs"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_periods = (int) $row["total"];
}


/* Total symptom records */

$total_symptoms = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM symptom_logs"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_symptoms = (int) $row["total"];
}


/*
|--------------------------------------------------------------------------
| RECENT USERS
|--------------------------------------------------------------------------
*/

$recent_users = mysqli_query(
    $conn,
    "SELECT id, name, email, role, created_at
     FROM users
     ORDER BY created_at DESC
     LIMIT 8"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | FemTrack</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        /* =========================================================
           RESET
        ========================================================= */

        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;

            min-height: 100vh;

            background:
                radial-gradient(
                    circle at 8% 12%,
                    rgba(255, 179, 211, .18),
                    transparent 24%
                ),
                radial-gradient(
                    circle at 94% 20%,
                    rgba(232, 105, 157, .13),
                    transparent 27%
                ),
                radial-gradient(
                    circle at 50% 100%,
                    rgba(255, 210, 226, .20),
                    transparent 32%
                ),
                linear-gradient(
                    135deg,
                    #fffdfd 0%,
                    #fff8fb 48%,
                    #fff3f7 100%
                );

            color: #432536;

            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;
        }


        a {
            text-decoration: none;
        }


        /* =========================================================
           NAVIGATION
        ========================================================= */

        .home-nav {

            position: sticky;

            top: 0;

            z-index: 1000;

            width: 100%;

            min-height: 74px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;

            padding: 0 5%;

            background:
                rgba(255, 255, 255, .88);

            border-bottom:
                1px solid rgba(231, 178, 199, .30);

            box-shadow:
                0 8px 30px rgba(104, 39, 69, .055);

            backdrop-filter: blur(18px);

            -webkit-backdrop-filter: blur(18px);
        }


        .home-logo {

            display: flex;

            align-items: center;

            gap: 10px;

            color: #402333;

            font-size: 22px;

            font-weight: 850;

            letter-spacing: -.8px;

            white-space: nowrap;

            transition:
                transform .25s ease;
        }


        .home-logo:hover {

            transform: translateY(-1px);

            color: #402333;
        }


        .home-logo img {

            width: 40px;

            height: 40px;

            object-fit: cover;

            border-radius: 50px;

            border: 1px solid #f4c4d7;

            box-shadow:
                0 5px 16px rgba(218, 55, 119, .14);
        }


        .home-logo span span {

            color: #df397d;
        }


        .home-nav-links {

            display: flex;

            align-items: center;

            gap: 6px;
        }


        .home-nav-links a {

            position: relative;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 9px 13px;

            border-radius: 11px;

            color: #745665;

            font-size: 11px;

            font-weight: 750;

            transition:
                color .22s ease,
                background .22s ease,
                transform .22s ease;
        }


        .home-nav-links a:hover {

            color: #d53277;

            background: #fff1f6;

            transform: translateY(-1px);
        }


        .home-nav-links a.active {

            color: #cf2f72;

            background:
                linear-gradient(
                    135deg,
                    #fff0f6,
                    #ffe8f1
                );

            border: 1px solid #ffd0df;

            box-shadow:
                0 5px 16px rgba(210, 46, 113, .07);
        }


        .nav-logout {

            margin-left: 4px;

            background: #fff7fa;

            border: 1px solid #f3d5e1;
        }


        /* =========================================================
           MAIN WRAPPER
        ========================================================= */

        .admin-page {

            width: min(1180px, calc(100% - 40px));

            margin: 0 auto;

            padding: 58px 0 85px;
        }


        /* =========================================================
           HERO
        ========================================================= */

        .admin-hero {

            position: relative;

            overflow: hidden;

            margin-bottom: 25px;

            padding: 40px 42px;

            border:
                1px solid rgba(240, 189, 208, .65);

            border-radius: 28px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,255,255,.97),
                    rgba(255,244,249,.95)
                );

            box-shadow:
                0 20px 55px rgba(90, 35, 61, .075);
        }


        .admin-hero::before {

            content: "";

            position: absolute;

            width: 290px;
            height: 290px;

            right: -95px;
            top: -145px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(230, 65, 126, .13),
                    rgba(230, 65, 126, 0)
                );

            pointer-events: none;
        }


        .admin-hero::after {

            content: "♡";

            position: absolute;

            right: 45px;
            bottom: 13px;

            color: rgba(217, 53, 119, .08);

            font-size: 125px;

            line-height: 1;

            transform: rotate(-10deg);

            pointer-events: none;
        }


        .admin-badge {

            position: relative;

            z-index: 2;

            display: inline-flex;

            align-items: center;

            gap: 7px;

            margin-bottom: 17px;

            padding: 8px 14px;

            border-radius: 999px;

            background:
                linear-gradient(
                    135deg,
                    #fff0f6,
                    #ffe5ef
                );

            border: 1px solid #ffcddd;

            color: #cf3374;

            font-size: 9px;

            font-weight: 850;

            letter-spacing: 1.3px;

            text-transform: uppercase;

            box-shadow:
                0 7px 20px rgba(208, 48, 113, .08);
        }


        .admin-heading h1 {

            position: relative;

            z-index: 2;

            max-width: 800px;

            margin: 0;

            color: #3e2231;

            font-size: clamp(38px, 5vw, 58px);

            line-height: 1.05;

            font-weight: 900;

            letter-spacing: -2.6px;
        }


        .admin-heading h1 span {

            display: inline-block;

            color: #d9367a;

            background:
                linear-gradient(
                    90deg,
                    #c92e70,
                    #ee659d
                );

            -webkit-background-clip: text;

            -webkit-text-fill-color: transparent;
        }


        .admin-heading p {

            position: relative;

            z-index: 2;

            max-width: 620px;

            margin: 16px 0 0;

            color: #8b6a7a;

            font-size: 14px;

            line-height: 1.8;
        }


        /* =========================================================
           WELCOME CARD
        ========================================================= */

        .welcome-card {

            position: relative;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;

            overflow: hidden;

            margin-bottom: 25px;

            padding: 24px 27px;

            border:
                1px solid #f0d7e2;

            border-radius: 21px;

            background:
                rgba(255, 255, 255, .92);

            box-shadow:
                0 12px 35px rgba(80, 33, 55, .055);
        }


        .welcome-card::before {

            content: "";

            position: absolute;

            left: 0;
            top: 0;
            bottom: 0;

            width: 4px;

            background:
                linear-gradient(
                    180deg,
                    #ee619c,
                    #c92e70
                );
        }


        .welcome-card::after {

            content: "♡";

            position: absolute;

            right: 22px;
            top: -23px;

            color: rgba(221, 54, 121, .055);

            font-size: 100px;

            pointer-events: none;
        }


        .welcome-content {

            position: relative;

            z-index: 2;
        }


        .welcome-label {

            margin-bottom: 5px;

            color: #b07d92;

            font-size: 9px;

            font-weight: 850;

            letter-spacing: 1.1px;

            text-transform: uppercase;
        }


        .welcome-card h2 {

            margin: 0;

            color: #422434;

            font-size: 19px;

            font-weight: 850;

            letter-spacing: -.3px;
        }


        .welcome-card h2 span {

            color: #d53476;
        }


        .welcome-card p {

            margin: 6px 0 0;

            color: #957584;

            font-size: 12px;
        }


        .admin-role {

            position: relative;

            z-index: 3;

            display: inline-flex;

            align-items: center;

            gap: 6px;

            flex-shrink: 0;

            padding: 9px 15px;

            border-radius: 999px;

            background:
                linear-gradient(
                    135deg,
                    #fff0f6,
                    #ffe4ee
                );

            border: 1px solid #ffc9db;

            color: #c72f70;

            font-size: 9px;

            font-weight: 850;

            letter-spacing: .7px;

            box-shadow:
                0 7px 18px rgba(203, 47, 111, .08);
        }


        /* =========================================================
           STATS HEADER
        ========================================================= */

        .section-kicker {

            display: flex;

            align-items: center;

            gap: 9px;

            margin: 0 0 13px 3px;

            color: #a0657d;

            font-size: 9px;

            font-weight: 850;

            letter-spacing: 1.2px;

            text-transform: uppercase;
        }


        .section-kicker::before {

            content: "";

            width: 22px;
            height: 2px;

            border-radius: 5px;

            background:
                linear-gradient(
                    90deg,
                    #df397b,
                    #f6b1c9
                );
        }


        /* =========================================================
           STATISTICS
        ========================================================= */

        .stats-grid {

            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 18px;

            margin-bottom: 27px;
        }


        .stat-card {

            position: relative;

            min-height: 175px;

            overflow: hidden;

            padding: 27px 25px 25px 28px;

            border:
                1px solid #f0d9e3;

            border-radius: 22px;

            background:
                rgba(255,255,255,.94);

            box-shadow:
                0 13px 36px rgba(82, 37, 58, .055);

            transition:
                transform .28s ease,
                box-shadow .28s ease,
                border-color .28s ease;
        }


        .stat-card:hover {

            transform: translateY(-5px);

            border-color: #efbdd1;

            box-shadow:
                0 20px 45px rgba(211, 50, 116, .11);
        }


        .stat-card::before {

            content: "";

            position: absolute;

            left: 0;
            top: 20px;
            bottom: 20px;

            width: 4px;

            border-radius: 0 5px 5px 0;

            background:
                linear-gradient(
                    180deg,
                    #f3659f,
                    #cf2e72
                );
        }


        .stat-card:nth-child(2)::before {

            background:
                linear-gradient(
                    180deg,
                    #d5a0ec,
                    #9860c6
                );
        }


        .stat-card:nth-child(3)::before {

            background:
                linear-gradient(
                    180deg,
                    #ff9eb8,
                    #e35a7b
                );
        }


        .stat-icon {

            position: absolute;

            top: 22px;
            right: 22px;

            display: flex;

            align-items: center;

            justify-content: center;

            width: 42px;
            height: 42px;

            border-radius: 14px;

            background: #fff2f7;

            border: 1px solid #fbd2e1;

            color: #d43a78;

            font-size: 18px;
        }


        .stat-card:nth-child(2) .stat-icon {

            color: #9660c0;

            background: #f8f0fb;

            border-color: #ead8f3;
        }


        .stat-card:nth-child(3) .stat-icon {

            color: #df5979;

            background: #fff1f4;

            border-color: #f8d4de;
        }


        .stat-label {

            position: relative;

            z-index: 2;

            color: #8d6d7c;

            font-size: 9px;

            font-weight: 850;

            letter-spacing: 1px;

            text-transform: uppercase;
        }


        .stat-number {

            position: relative;

            z-index: 2;

            margin-top: 13px;

            color: #3e2131;

            font-size: 42px;

            font-weight: 900;

            line-height: 1;

            letter-spacing: -1.5px;
        }


        .stat-note {

            position: relative;

            z-index: 2;

            margin-top: 10px;

            color: #ad909f;

            font-size: 11px;
        }


        .stat-card::after {

            content: "";

            position: absolute;

            width: 160px;
            height: 160px;

            right: -85px;
            bottom: -95px;

            border-radius: 50%;

            background:
                rgba(232, 74, 137, .055);
        }


        /* =========================================================
           MEMBERS CARD
        ========================================================= */

        .users-card {

            overflow: hidden;

            border:
                1px solid #efdbe4;

            border-radius: 24px;

            background:
                rgba(255,255,255,.96);

            box-shadow:
                0 17px 45px rgba(82, 37, 58, .065);
        }


        .users-header {

            position: relative;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding: 26px 28px 23px;

            background:
                linear-gradient(
                    135deg,
                    #ffffff,
                    #fff7fa
                );

            border-bottom:
                1px solid #f3e1e8;
        }


        .users-title {

            display: flex;

            align-items: center;

            gap: 13px;
        }


        .users-icon {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 43px;
            height: 43px;

            flex-shrink: 0;

            border-radius: 14px;

            background:
                linear-gradient(
                    135deg,
                    #fff0f6,
                    #ffe4ef
                );

            border: 1px solid #ffd0df;

            color: #d53576;

            font-size: 19px;

            box-shadow:
                0 6px 17px rgba(207, 49, 112, .07);
        }


        .users-header h2 {

            margin: 0;

            color: #422434;

            font-size: 20px;

            font-weight: 850;

            letter-spacing: -.4px;
        }


        .users-header p {

            margin: 5px 0 0;

            color: #987887;

            font-size: 11px;
        }


        .member-count {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 8px 13px;

            border-radius: 999px;

            background: #fff1f6;

            border: 1px solid #f8d4e2;

            color: #c9577e;

            font-size: 9px;

            font-weight: 850;

            white-space: nowrap;
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

            min-width: 760px;

            border-collapse: collapse;
        }


        th {

            padding: 14px 18px;

            background:
                linear-gradient(
                    90deg,
                    #fff5f8,
                    #fff1f6
                );

            color: #a04e70;

            font-size: 8px;

            font-weight: 850;

            letter-spacing: 1px;

            text-align: left;

            text-transform: uppercase;

            border-bottom:
                1px solid #f3dce5;
        }


        td {

            padding: 16px 18px;

            color: #654657;

            font-size: 12px;

            border-bottom:
                1px solid #f5e8ed;

            vertical-align: middle;
        }


        tbody tr {

            transition:
                background .22s ease;
        }


        tbody tr:hover {

            background:
                linear-gradient(
                    90deg,
                    #fffafd,
                    #fff6fa
                );
        }


        tbody tr:last-child td {

            border-bottom: none;
        }


        td:first-child {

            color: #b08b9b;

            font-size: 11px;

            font-weight: 750;
        }


        /* =========================================================
           MEMBER NAME
        ========================================================= */

        .member-name {

            display: flex;

            align-items: center;

            gap: 11px;

            color: #4b2a3a;

            font-weight: 750;

            white-space: nowrap;
        }


        .member-avatar {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 35px;
            height: 35px;

            flex-shrink: 0;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    #ffe2ee,
                    #ffd0e2
                );

            border:
                1px solid #ffc4da;

            color: #c93271;

            font-size: 11px;

            font-weight: 900;

            box-shadow:
                0 4px 12px rgba(204, 48, 112, .08);
        }


        /* =========================================================
           EMAIL
        ========================================================= */

        .member-email {

            color: #806473;

            font-size: 11px;
        }


        /* =========================================================
           ROLE BADGES
        ========================================================= */

        .role-user,
        .role-admin {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 6px 11px;

            border-radius: 999px;

            font-size: 8px;

            font-weight: 850;

            letter-spacing: .3px;
        }


        .role-user {

            background: #f8f3f5;

            color: #806674;

            border:
                1px solid #eadde3;
        }


        .role-admin {

            background:
                linear-gradient(
                    135deg,
                    #ffeaf2,
                    #ffe2ed
                );

            color: #c72f70;

            border:
                1px solid #ffcbdc;

            box-shadow:
                0 4px 12px rgba(201, 47, 112, .05);
        }


        /* =========================================================
           DATE
        ========================================================= */

        .joined-date {

            color: #927584;

            font-size: 10px;

            white-space: nowrap;
        }


        /* =========================================================
           EMPTY
        ========================================================= */

        .empty-users {

            padding: 65px 20px;

            text-align: center;

            color: #927584;

            font-size: 13px;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 900px) {

            .home-nav {

                padding: 0 20px;
            }


            .home-nav-links {

                gap: 2px;
            }


            .home-nav-links a {

                padding: 8px 9px;

                font-size: 10px;
            }


            .admin-page {

                width: min(100% - 30px, 760px);

                padding-top: 42px;
            }


            .stats-grid {

                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 650px) {

            .home-nav {

                position: relative;

                align-items: flex-start;

                flex-direction: column;

                gap: 10px;

                padding: 16px 18px;
            }


            .home-nav-links {

                width: 100%;

                overflow-x: auto;

                padding-bottom: 2px;
            }


            .home-nav-links a {

                flex-shrink: 0;
            }


            .admin-page {

                width: calc(100% - 22px);

                padding-top: 28px;

                padding-bottom: 55px;
            }


            .admin-hero {

                padding: 29px 23px;

                border-radius: 23px;
            }


            .admin-heading h1 {

                font-size: 35px;

                letter-spacing: -1.6px;
            }


            .admin-heading p {

                font-size: 12px;
            }


            .welcome-card {

                align-items: flex-start;

                flex-direction: column;

                padding: 21px;
            }


            .admin-role {

                align-self: flex-start;
            }


            .stat-card {

                min-height: 155px;

                padding: 23px 22px 21px 25px;
            }


            .stat-number {

                font-size: 38px;
            }


            .users-header {

                align-items: flex-start;

                flex-direction: column;

                padding: 21px 18px;
            }


            .member-count {

                align-self: flex-start;
            }


            th,
            td {

                padding-left: 13px;

                padding-right: 13px;
            }

        }


        @media (prefers-reduced-motion: reduce) {

            * {

                scroll-behavior: auto !important;

                transition: none !important;
            }
        }

    </style>

</head>


<body>


<!-- =========================================================
     ADMIN NAVIGATION
========================================================= -->

<nav
    class="home-nav"
    aria-label="Main navigation"
>

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

        <a
            class="active"
            href="admin.php"
        >
            Admin Dashboard
        </a>


        <a href="dashboard.php">
            Member Dashboard
        </a>


        <a href="../index.php">
            Home
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
     MAIN ADMIN CONTENT
========================================================= -->

<main class="admin-page">


    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="admin-hero admin-heading">

        <div class="admin-badge">
         Administrator Area
        </div>


        <h1>
            Welcome to your
            <span>Admin Dashboard</span>
        </h1>


        <p>
            Manage and monitor your FemTrack application
            from one simple, private space.
        </p>

    </section>



    <!-- =====================================================
         CURRENT ADMIN
    ====================================================== -->

    <section class="welcome-card">

        <div class="welcome-content">

            <div class="welcome-label">
                Your account
            </div>


            <h2>
                Hello,
                <span>
                    <?php
                    echo htmlspecialchars(
                        $current_user["name"]
                    );
                    ?>
                </span>
            </h2>


            <p>
                <?php
                echo htmlspecialchars(
                    $current_user["email"]
                );
                ?>
            </p>

        </div>


        <div class="admin-role">
             ADMIN
        </div>

    </section>



    <!-- =====================================================
         STATISTICS
====================================================== -->

    <div class="section-kicker">
        Overview
    </div>


    <section class="stats-grid">


        <!-- REGISTERED MEMBERS -->

        <article class="stat-card">

            <div class="stat-icon">
                ♡
            </div>


            <div class="stat-label">
                Registered Members
            </div>


            <div class="stat-number">
                <?php
                echo $total_users;
                ?>
            </div>


            <div class="stat-note">
                Total accounts in FemTrack
            </div>

        </article>



        <!-- PERIOD RECORDS -->

        <article class="stat-card">

            <div class="stat-icon">
                ◌
            </div>


            <div class="stat-label">
                Period Records
            </div>


            <div class="stat-number">
                <?php
                echo $total_periods;
                ?>
            </div>


            <div class="stat-note">
                Period logs recorded
            </div>

        </article>



        <!-- SYMPTOM RECORDS -->

        <article class="stat-card">

            <div class="stat-icon">
                ✦
            </div>


            <div class="stat-label">
                Symptom Records
            </div>


            <div class="stat-number">
                <?php
                echo $total_symptoms;
                ?>
            </div>


            <div class="stat-note">
                Symptom entries recorded
            </div>

        </article>


    </section>



    <!-- =====================================================
         RECENT MEMBERS
====================================================== -->

    <section class="users-card">


        <div class="users-header">

            <div class="users-title">

                <div class="users-icon">
                    ♡
                </div>


                <div>

                    <h2>
                        Recent Members
                    </h2>


                    <p>
                        The latest accounts registered in FemTrack.
                    </p>

                </div>

            </div>


            <div class="member-count">
             <?php echo $total_users; ?> accounts
            </div>

        </div>



        <?php if ($recent_users && mysqli_num_rows($recent_users) > 0): ?>


            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Joined
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($member = mysqli_fetch_assoc($recent_users)): ?>


                            <?php

                            $member_name = $member["name"];

                            $first_letter = strtoupper(
                                substr(
                                    trim($member_name),
                                    0,
                                    1
                                )
                            );

                            ?>


                            <tr>


                                <!-- ID -->

                                <td>
                                    <?php
                                    echo (int) $member["id"];
                                    ?>
                                </td>



                                <!-- NAME -->

                                <td>

                                    <div class="member-name">

                                        <span class="member-avatar">
                                            <?php
                                            echo htmlspecialchars(
                                                $first_letter
                                            );
                                            ?>
                                        </span>


                                        <span>
                                            <?php
                                            echo htmlspecialchars(
                                                $member["name"]
                                            );
                                            ?>
                                        </span>

                                    </div>

                                </td>



                                <!-- EMAIL -->

                                <td>

                                    <span class="member-email">

                                        <?php
                                        echo htmlspecialchars(
                                            $member["email"]
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- ROLE -->

                                <td>

                                    <?php if ($member["role"] === "admin"): ?>

                                        <span class="role-admin">
                                           Admin
                                        </span>

                                    <?php else: ?>

                                        <span class="role-user">
                                             Member
                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- JOINED -->

                                <td>

                                    <span class="joined-date">

                                        <?php
                                        echo htmlspecialchars(
                                            $member["created_at"]
                                        );
                                        ?>

                                    </span>

                                </td>


                            </tr>


                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="empty-users">

                ♡

                <br><br>

                No members found.

            </div>


        <?php endif; ?>


    </section>


</main>


</body>

</html>
