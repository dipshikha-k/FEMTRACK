
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Us | FemTrack</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        /* ================================
           FEMTRACK - ABOUT US
        ================================= */

        * {
            box-sizing: border-box;
        }

        body.about-page {
            margin: 0;
            background: #fff8fb;
            color: #4f263b;
        }


        /* ---------- NAVBAR ---------- */

        .about-page .topbar {
            width: 100%;
            background: rgba(255,255,255,0.96);
            border-bottom: 1px solid #f3dce7;
            box-shadow: 0 4px 20px rgba(100,40,70,0.06);
        }
        .about-page .logo {
    color: #3d1934;
    font-size: 1.2rem;
    font-weight: 900;
    letter-spacing: -.04em;
    text-decoration: none;
}

.about-page .logo > span > span {
    color: #d75b93;
}

.about-page .logo:hover {
    color: #3d1934;
    text-decoration: none;
}

.about-page .nav a {
    color: #70485b;
    transition: .3s;
}

.about-page .nav a:hover,
.about-page .nav a.active {
    color: #c64e86;
    background: transparent;
}

.about-page .nav .logout {
    background: #401c38;
    color: white;
}

   


        /* ---------- MAIN ---------- */

        .about-content {
            max-width: 1180px;
            margin: auto;
            padding: 60px 25px 80px;
        }


        /* ---------- HERO ---------- */

        .about-hero {
            position: relative;
            min-height: 480px;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 70px 30px;

            border-radius: 45px;

            overflow: hidden;

            background:
                radial-gradient(circle at 15% 20%, #f9d7e6 0 70px, transparent 71px),
                radial-gradient(circle at 88% 80%, #f8d9e8 0 90px, transparent 91px),
                linear-gradient(135deg, #fff1f6, #fbe1ed);

            box-shadow:
                0 25px 60px rgba(158,67,105,.10);
        }


        .about-hero::before {
            content: "✿";
            position: absolute;
            left: 55px;
            top: 45px;

            font-size: 80px;

            color: #df9fba;

            opacity: .45;
        }

        .about-hero::after {
            content: "❀";
            position: absolute;
            right: 55px;
            bottom: 35px;

            font-size: 90px;

            color: #df9fba;

            opacity: .4;
        }


        .hero-content {
            max-width: 800px;
            position: relative;
            z-index: 2;
        }


        .hero-small {
            color: #c15a87;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }


        .about-hero h1 {
            margin: 0;

            color: #5b2942;

            font-family: Georgia, "Times New Roman", serif;

            font-size: clamp(42px, 6vw, 72px);

            line-height: 1.05;

            font-weight: normal;
        }


        .about-hero h1 em {
            color: #c95887;
        }


        .hero-line {
            width: 70px;
            height: 2px;

            background: #d4779f;

            margin: 25px auto;
        }


        .about-hero p {
            max-width: 680px;

            margin: auto;

            color: #795a68;

            font-size: 17px;

            line-height: 1.9;
        }


        /* ---------- INTRO ---------- */

        .intro {
            text-align: center;

            max-width: 760px;

            margin: 75px auto 45px;
        }

        .section-label {
            color: #c15a87;

            font-size: 11px;

            font-weight: bold;

            letter-spacing: 3px;

            text-transform: uppercase;
        }

        .intro h2 {
            margin: 12px 0;

            color: #5b2942;

            font-family: Georgia, serif;

            font-size: 38px;

            font-weight: normal;
        }

        .intro h2 span {
            color: #d16a97;
        }

        .intro p {
            color: #846875;

            line-height: 1.8;

            font-size: 15px;
        }


        /* ---------- VALUES ---------- */

        .values {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 22px;
        }


        .value-card {
            position: relative;

            background: #ffffff;

            border: 1px solid #f3dce7;

            border-radius: 28px;

            padding: 35px 30px;

            text-align: center;

            box-shadow:
                0 15px 35px rgba(130,55,90,.06);

            transition: .35s;
        }


        .value-card:hover {
            transform: translateY(-8px);

            box-shadow:
                0 22px 45px rgba(130,55,90,.11);
        }


        .value-icon {
            width: 65px;
            height: 65px;

            margin: 0 auto 22px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #fce5ef;

            color: #c65384;

            font-size: 27px;
        }


        .value-card h3 {
            color: #633049;

            font-family: Georgia, serif;

            font-size: 21px;

            margin-bottom: 12px;
        }


        .value-card p {
            color: #886b78;

            line-height: 1.7;

            font-size: 14px;

            margin: 0;
        }


        /* ---------- STORY ---------- */

        .story {
            margin-top: 70px;

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 25px;

            align-items: stretch;
        }


        .story-image {
            min-height: 390px;

            border-radius: 35px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            background:
                linear-gradient(
                    145deg,
                    #f8d7e6,
                    #fcecf3
                );

            position: relative;

            overflow: hidden;
        }


        .story-image::before {
            content: "♡";

            position: absolute;

            top: 25px;
            left: 35px;

            font-size: 70px;

            color: #d58aaa;

            opacity: .35;
        }


        .story-image::after {
            content: "✿";

            position: absolute;

            right: 30px;
            bottom: 20px;

            font-size: 80px;

            color: #d58aaa;

            opacity: .35;
        }


        .story-flower {
            font-size: 110px;

            filter: drop-shadow(
                0 12px 15px rgba(150,60,100,.12)
            );
        }


        .story-text {
            background: white;

            border: 1px solid #f3dce7;

            border-radius: 35px;

            padding: 50px;

            box-shadow:
                0 15px 35px rgba(130,55,90,.06);
        }


        .story-text h2 {
            color: #5b2942;

            font-family: Georgia, serif;

            font-size: 36px;

            font-weight: normal;

            line-height: 1.2;

            margin: 12px 0 20px;
        }


        .story-text h2 span {
            color: #d16a97;
        }


        .story-text p {
            color: #846875;

            line-height: 1.85;

            font-size: 15px;
        }


        /* ---------- QUOTE ---------- */

        .quote {
            margin-top: 70px;

            padding: 60px 30px;

            text-align: center;

            border-radius: 35px;

            background: #5d2c45;

            color: white;

            position: relative;

            overflow: hidden;
        }


        .quote::before {
            content: "“";

            position: absolute;

            left: 25px;
            top: -20px;

            font-family: Georgia, serif;

            font-size: 180px;

            color: rgba(255,255,255,.07);
        }


        .quote p {
            position: relative;

            max-width: 750px;

            margin: auto;

            font-family: Georgia, serif;

            font-size: 28px;

            line-height: 1.5;
        }


        .quote span {
            display: block;

            margin-top: 20px;

            color: #f3b5ce;

            font-family: Arial, sans-serif;

            font-size: 11px;

            letter-spacing: 3px;
        }


        /* ---------- CTA ---------- */

        .about-note {
            margin-top: 35px;

            padding: 40px;

            border-radius: 30px;

            background: linear-gradient(
                135deg,
                #f8d6e5,
                #fff0f6
            );

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 25px;
        }


        .about-note h2 {
            margin: 8px 0 0;

            color: #5b2942;

            font-family: Georgia, serif;

            font-weight: normal;

            font-size: 27px;
        }


        .about-button {
            text-decoration: none;

            background: #c65384;

            color: white;

            padding: 14px 25px;

            border-radius: 30px;

            font-size: 14px;

            white-space: nowrap;

            transition: .3s;
        }


        .about-button:hover {
            background: #a93b6b;

            transform: translateY(-3px);
        }


        /* ---------- FOOTER ---------- */

        .about-footer {
            text-align: center;

            margin-top: 35px;

            color: #a27d8c;

            font-size: 13px;
        }

        .about-footer b {
            color: #d16a97;
        }


        /* ---------- MOBILE ---------- */

        @media (max-width: 850px) {

            .about-page .topbar {
                flex-direction: column;

                gap: 15px;
            }

            .about-page .nav {
                flex-wrap: wrap;

                justify-content: center;
            }

            .about-content {
                padding: 35px 18px 60px;
            }

            .about-hero {
                min-height: 420px;

                padding: 55px 20px;
            }

            .about-hero::before {
                left: 15px;
                top: 20px;
            }

            .about-hero::after {
                right: 15px;
            }

            .values {
                grid-template-columns: 1fr;
            }

            .story {
                grid-template-columns: 1fr;
            }

            .story-image {
                min-height: 280px;
            }

            .story-text {
                padding: 35px;
            }

            .quote p {
                font-size: 22px;
            }

            .about-note {
                flex-direction: column;

                text-align: center;

                padding: 35px 25px;
            }

        }

    </style>

</head>


<body class="about-page">


<!-- ================= NAVBAR ================= -->

<nav class="topbar">

<a class="logo" href="index.php">

    <img
        class="logo-mark"
        src="assets/femtrack-mark.jpeg"
        alt="FemTrack"
    >

    <span>Fem<span>Track</span></span>

</a>


    <div class="nav">

        <a href="index.php">
            Home
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="user/dashboard.php">
                Dashboard
            </a>

            <a href="user/track-symptoms.php">
                Track Symptoms
            </a>

            <a href="user/reports.php">
                Reports
            </a>

        <?php endif; ?>


        <a class="active" href="about.php">
            About Us
        </a>


        <?php if (isset($_SESSION['user_id'])): ?>

            <a class="logout" href="logout.php">
                Logout
            </a>

        <?php else: ?>

            <a class="logout" href="login.php">
                Log in
            </a>

        <?php endif; ?>

    </div>

</nav>


<main class="about-content">


<!-- ================= HERO ================= -->

<section class="about-hero">

    <div class="hero-content">

        <div class="hero-small">
            ✦ ABOUT FEMTRACK ✦
        </div>

        <h1>
            A little more care
            for <em>you.</em>
        </h1>

        <div class="hero-line"></div>

        <p>
            FemTrack is a gentle digital space created to make
            period and cycle tracking feel simple, comfortable,
            and personal.
        </p>

    </div>

</section>


<!-- ================= INTRO ================= -->

<section class="intro">

    <div class="section-label">
        Our little philosophy
    </div>

    <h2>
        Because your body deserves
        <span>your attention.</span>
    </h2>

    <p>
        Understanding your cycle doesn't have to be complicated.
        FemTrack gives you a simple place to record your periods,
        track symptoms, and look back at your own patterns.
    </p>

</section>


<!-- ================= VALUES ================= -->

<section class="values">


    <article class="value-card">

        <div class="value-icon">
            ♡
        </div>

        <h3>
            Made for You
        </h3>

        <p>
            Your cycle is personal. FemTrack keeps your records
            organised in a space designed around your everyday needs.
        </p>

    </article>


    <article class="value-card">

        <div class="value-icon">
            ✿
        </div>

        <h3>
            Simple & Gentle
        </h3>

        <p>
            No overwhelming screens or complicated steps.
            Just simple tools to help you keep track of yourself.
        </p>

    </article>


    <article class="value-card">

        <div class="value-icon">
            ✦
        </div>

        <h3>
            Understand Your Patterns
        </h3>

        <p>
            Keep your history together and use your records
            to notice patterns and feel more prepared.
        </p>

    </article>


</section>


<!-- ================= STORY ================= -->

<section class="story">


    <div class="story-image">

        <div class="story-flower">
            🌷
        </div>

    </div>


    <div class="story-text">

        <div class="section-label">
            Why FemTrack exists
        </div>

        <h2>
            Tracking shouldn't feel like
            <span>another task.</span>
        </h2>

        <p>
            Period tracking is something many women do every month,
            yet it can easily become confusing when dates, symptoms,
            and previous records are scattered everywhere.
        </p>

        <p>
            FemTrack brings those little pieces together into one
            calm and easy-to-use space — so you can spend less time
            worrying about remembering everything and more time
            understanding yourself.
        </p>

    </div>

</section>


<!-- ================= QUOTE ================= -->

<section class="quote">

    <p>
        Your body is not something to figure out.
        It is something to listen to.
    </p>

    <span>
        FEMTRACK
    </span>

</section>


<!-- ================= CTA ================= -->

<section class="about-note">

    <div>

        <div class="section-label">
            Ready when you are ♡
        </div>

        <h2>
            Take a moment for yourself today.
        </h2>

    </div>


    <?php if (isset($_SESSION['user_id'])): ?>

        <a
            class="about-button"
            href="user/dashboard.php"
        >
            Go to Dashboard →
        </a>

    <?php else: ?>

        <a
            class="about-button"
            href="login.php"
        >
            Get Started →
        </a>

    <?php endif; ?>

</section>


<div class="about-footer">

     ~Made with <b>♡</b> for women who deserve
    a little more care.~

</div>


</main>

</body>

</html>
