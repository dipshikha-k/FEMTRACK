<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About | FemTrack</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        /* =========================================================
           FEMTRACK ABOUT
           PINTEREST / EDITORIAL / WOMEN'S WELLNESS
           
           IMPORTANT:
           NAVBAR IS NOT STYLED OR MODIFIED HERE.
        ========================================================= */


        .about-page {
            margin: 0;
            background: #fcf7f8;
            color: #3b2933;
            overflow-x: hidden;
        }


        .about-page * {
            box-sizing: border-box;
        }


        /* =========================================================
           EDITORIAL CONTAINER
        ========================================================= */

        .editorial-page {
            width: min(1240px, 92%);
            margin: auto;
        }


        /* =========================================================
           HERO
        ========================================================= */

        .editorial-hero {
            min-height: 620px;

            display: grid;
            grid-template-columns: 1.05fr .95fr;

            align-items: center;

            gap: 60px;

            padding: 75px 5% 85px;

            position: relative;
        }


        .hero-copy {
            position: relative;
            z-index: 2;
        }


        .eyebrow {
            display: flex;
            align-items: center;
            gap: 12px;

            margin-bottom: 25px;

            color: #ad6680;

            font-size: 10px;
            font-weight: 700;

            letter-spacing: 3px;

            text-transform: uppercase;
        }


        .eyebrow::before {
            content: "";

            width: 38px;
            height: 1px;

            background: #c9899f;
        }


        .hero-copy h1 {
            margin: 0;

            max-width: 650px;

            color: #4f2c3c;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: clamp(55px, 7vw, 92px);

            font-weight: 400;

            line-height: .98;

            letter-spacing: -3px;
        }


        .hero-copy h1 em {
            color: #bd6586;

            font-style: italic;
        }


        .hero-copy .hero-description {
            max-width: 470px;

            margin: 32px 0 0;

            color: #7d6570;

            font-size: 15px;

            line-height: 1.9;
        }


        .hero-small {
            margin-top: 32px;

            color: #a58c96;

            font-size: 11px;

            letter-spacing: 1px;
        }


        /* =========================================================
           HERO VISUAL
        ========================================================= */

        .hero-art {
            position: relative;

            min-height: 490px;

            display: flex;

            align-items: center;
            justify-content: center;
        }


        .hero-art-main {
            width: 340px;
            height: 440px;

            position: relative;

            overflow: hidden;

            border-radius: 170px 170px 20px 20px;

            background:
                linear-gradient(
                    145deg,
                    #eed1dc,
                    #f8e8ed 48%,
                    #dfb7c8
                );

            box-shadow:
                20px 30px 60px rgba(91, 47, 66, .13);
        }


        .hero-art-main::before {
            content: "";

            position: absolute;

            width: 250px;
            height: 250px;

            left: 45px;
            top: 65px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(255,255,255,.8) 0 30%,
                    rgba(255,255,255,.28) 31% 60%,
                    transparent 61%
                );
        }


        .hero-art-main::after {
            content: "♡";

            position: absolute;

            left: 50%;
            top: 50%;

            transform: translate(-50%, -50%);

            color: rgba(125,65,91,.45);

            font-family: Georgia, serif;

            font-size: 95px;
        }


        .hero-note {
            position: absolute;

            right: -10px;
            bottom: 38px;

            width: 205px;

            padding: 25px 23px;

            background: rgba(255,255,255,.88);

            border: 1px solid rgba(255,255,255,.9);

            box-shadow:
                0 18px 45px rgba(73,38,55,.10);

            transform: rotate(3deg);
        }


        .hero-note small {
            display: block;

            margin-bottom: 10px;

            color: #b66d87;

            font-size: 9px;

            font-weight: 700;

            letter-spacing: 2px;

            text-transform: uppercase;
        }


        .hero-note strong {
            color: #593343;

            font-family: Georgia, serif;

            font-size: 21px;

            font-weight: 400;

            line-height: 1.35;
        }


        .hero-number {
            position: absolute;

            left: 10px;
            top: 50px;

            color: #d8b4c2;

            font-family: Georgia, serif;

            font-size: 80px;

            font-weight: 400;

            line-height: 1;
        }


        /* =========================================================
           INTRODUCTION
        ========================================================= */

        .editorial-intro {
            padding: 95px 8% 105px;

            border-top: 1px solid #eadde2;

            border-bottom: 1px solid #eadde2;

            text-align: center;
        }


        .intro-kicker {
            color: #b36b84;

            font-size: 10px;

            font-weight: 700;

            letter-spacing: 3px;

            text-transform: uppercase;
        }


        .editorial-intro h2 {
            max-width: 850px;

            margin: 22px auto;

            color: #4f2d3c;

            font-family: Georgia, serif;

            font-size: clamp(34px, 5vw, 57px);

            font-weight: 400;

            line-height: 1.12;

            letter-spacing: -1.5px;
        }


        .editorial-intro h2 em {
            color: #bd6687;

            font-style: italic;
        }


        .editorial-intro p {
            max-width: 650px;

            margin: auto;

            color: #806a74;

            font-size: 14px;

            line-height: 1.9;
        }


        /* =========================================================
           STORY SECTION
        ========================================================= */

        .story-section {
            display: grid;

            grid-template-columns: .8fr 1.2fr;

            gap: 90px;

            align-items: center;

            padding: 120px 7%;
        }


        .story-label {
            color: #b66b85;

            font-size: 10px;

            font-weight: 700;

            letter-spacing: 3px;

            text-transform: uppercase;
        }


        .story-left h2 {
            margin: 18px 0 0;

            color: #4e2b3b;

            font-family: Georgia, serif;

            font-size: 49px;

            font-weight: 400;

            line-height: 1.08;
        }


        .story-left h2 em {
            color: #bd6586;

            font-style: italic;
        }


        .story-right {
            padding-left: 35px;

            border-left: 1px solid #dfcbd4;
        }


        .story-right p {
            margin: 0 0 22px;

            color: #765f6a;

            font-size: 15px;

            line-height: 1.95;
        }


        .story-signature {
            margin-top: 28px;

            color: #ad7186;

            font-family: Georgia, serif;

            font-size: 16px;

            font-style: italic;
        }


        /* =========================================================
           FEATURE EDITORIAL
        ========================================================= */

        .feature-section {
            padding: 110px 5%;

            background: #f5e7ec;
        }


        .feature-heading {
            display: flex;

            justify-content: space-between;

            align-items: flex-end;

            gap: 40px;

            margin-bottom: 65px;
        }


        .feature-heading h2 {
            margin: 0;

            color: #4e2c3b;

            font-family: Georgia, serif;

            font-size: clamp(38px, 5vw, 59px);

            font-weight: 400;

            line-height: 1.05;
        }


        .feature-heading p {
            max-width: 310px;

            margin: 0;

            color: #806975;

            font-size: 13px;

            line-height: 1.8;
        }


        .feature-list {
            border-top: 1px solid #d8bec9;
        }


        .editorial-feature {
            display: grid;

            grid-template-columns: 90px 1fr 1fr;

            align-items: center;

            gap: 40px;

            min-height: 145px;

            border-bottom: 1px solid #d8bec9;
        }


        .feature-number {
            color: #b7788e;

            font-family: Georgia, serif;

            font-size: 16px;
        }


        .editorial-feature h3 {
            margin: 0;

            color: #543041;

            font-family: Georgia, serif;

            font-size: 28px;

            font-weight: 400;
        }


        .editorial-feature p {
            margin: 0;

            max-width: 390px;

            color: #79636d;

            font-size: 13px;

            line-height: 1.8;
        }


        /* =========================================================
           CENTER STATEMENT
        ========================================================= */

        .statement-section {
            padding: 130px 10%;

            text-align: center;
        }


        .statement-small {
            margin-bottom: 22px;

            color: #b36a83;

            font-size: 10px;

            font-weight: 700;

            letter-spacing: 3px;

            text-transform: uppercase;
        }


        .statement-section h2 {
            max-width: 900px;

            margin: auto;

            color: #4c2b3a;

            font-family: Georgia, serif;

            font-size: clamp(40px, 6vw, 72px);

            font-weight: 400;

            line-height: 1.08;

            letter-spacing: -2px;
        }


        .statement-section h2 em {
            color: #bd6686;

            font-style: italic;
        }


        .statement-line {
            width: 55px;
            height: 1px;

            margin: 35px auto;

            background: #c9879e;
        }


        .statement-section p {
            max-width: 580px;

            margin: auto;

            color: #806974;

            font-size: 14px;

            line-height: 1.9;
        }


        /* =========================================================
           FINAL CTA
        ========================================================= */

        .final-section {
            position: relative;

            margin-bottom: 65px;

            padding: 90px 30px;

            overflow: hidden;

            text-align: center;

            background: #4f2d3d;

            border-radius: 4px;
        }


        .final-section::before {
            content: "";

            position: absolute;

            width: 400px;
            height: 400px;

            border: 1px solid rgba(255,255,255,.08);

            border-radius: 50%;

            left: -190px;
            top: -210px;
        }


        .final-section::after {
            content: "";

            position: absolute;

            width: 300px;
            height: 300px;

            border: 1px solid rgba(255,255,255,.07);

            border-radius: 50%;

            right: -140px;
            bottom: -180px;
        }


        .final-content {
            position: relative;

            z-index: 2;
        }


        .final-small {
            color: #d9aabc;

            font-size: 10px;

            font-weight: 700;

            letter-spacing: 3px;

            text-transform: uppercase;
        }


        .final-section h2 {
            max-width: 700px;

            margin: 18px auto;

            color: white;

            font-family: Georgia, serif;

            font-size: clamp(38px, 5vw, 57px);

            font-weight: 400;

            line-height: 1.08;
        }


        .final-section p {
            max-width: 560px;

            margin: auto;

            color: #ead9df;

            font-size: 14px;

            line-height: 1.8;
        }


        .final-button {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            margin-top: 30px;

            padding: 13px 27px;

            background: #fff7fa;

            color: #5b3044;

            text-decoration: none;

            font-size: 12px;

            font-weight: 600;

            letter-spacing: .4px;

            transition: .3s ease;
        }


        .final-button:hover {
            transform: translateY(-3px);

            background: #f7dce7;

            color: #4d2739;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .editorial-footer {
            padding: 0 0 40px;

            text-align: center;

            color: #a58b96;

            font-size: 11px;

            letter-spacing: .5px;
        }


        .editorial-footer span {
            color: #bc6b88;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 900px) {

            .editorial-hero {
                grid-template-columns: 1fr;

                text-align: center;

                padding-top: 60px;
            }


            .eyebrow {
                justify-content: center;
            }


            .hero-copy .hero-description {
                margin-left: auto;
                margin-right: auto;
            }


            .hero-art {
                min-height: 420px;
            }


            .story-section {
                grid-template-columns: 1fr;

                gap: 45px;

                padding: 85px 8%;
            }


            .story-right {
                padding-left: 25px;
            }


            .feature-heading {
                flex-direction: column;

                align-items: flex-start;
            }

        }


        @media (max-width: 650px) {

            .editorial-page {
                width: 94%;
            }


            .editorial-hero {
                min-height: auto;

                padding: 55px 3% 65px;

                gap: 40px;
            }


            .hero-copy h1 {
                font-size: 52px;

                letter-spacing: -2px;
            }


            .hero-art-main {
                width: 265px;
                height: 350px;
            }


            .hero-note {
                right: 0;

                bottom: 15px;

                width: 170px;

                padding: 20px;
            }


            .hero-note strong {
                font-size: 17px;
            }


            .hero-number {
                left: 0;

                font-size: 60px;
            }


            .editorial-intro {
                padding: 75px 5%;
            }


            .editorial-intro h2 {
                font-size: 36px;
            }


            .story-section {
                padding: 75px 7%;
            }


            .story-left h2 {
                font-size: 40px;
            }


            .feature-section {
                padding: 75px 6%;
            }


            .editorial-feature {
                grid-template-columns: 40px 1fr;

                gap: 12px;

                padding: 25px 0;
            }


            .editorial-feature p {
                grid-column: 2;

                margin-top: -8px;
            }


            .statement-section {
                padding: 85px 7%;
            }


            .statement-section h2 {
                font-size: 42px;
            }


            .final-section {
                margin-bottom: 40px;

                padding: 70px 20px;
            }


            .final-section h2 {
                font-size: 40px;
            }

        }


        @media (max-width: 400px) {

            .hero-copy h1 {
                font-size: 45px;
            }


            .hero-art-main {
                width: 240px;
                height: 320px;
            }


            .hero-note {
                width: 155px;
            }


            .statement-section h2 {
                font-size: 36px;
            }

        }

    </style>

</head>


<body class="about-page">


    <!-- =====================================================
         EXISTING NAVBAR — DO NOT TOUCH
    ====================================================== -->

    <?php include "includes/nav.php"; ?>


    <main class="editorial-page">


        <!-- =====================================================
             HERO
        ====================================================== -->

        <section class="editorial-hero">


            <div class="hero-copy">


                <div class="eyebrow">
                    About FemTrack
                </div>


                <h1>
                    More than a
                    <em>tracker.</em>
                </h1>


                <p class="hero-description">
                    A gentle digital space designed to help you
                    record your cycle, understand your symptoms,
                    and keep your menstrual health information
                    beautifully organised.
                </p>


                <div class="hero-small">
                    Your cycle · Your rhythm · Your strength
                </div>


            </div>



            <div class="hero-art">


                <div class="hero-number">
                    01
                </div>


                <div class="hero-art-main"></div>


                <div class="hero-note">

                    <small>
                        FemTrack
                    </small>

                    <strong>
                        A little space
                        to understand
                        yourself.
                    </strong>

                </div>


            </div>


        </section>



        <!-- =====================================================
             INTRO
        ====================================================== -->

        <section class="editorial-intro">


            <div class="intro-kicker">
                The idea behind FemTrack
            </div>


            <h2>
                Your body has a rhythm.
                <em>It deserves to be noticed.</em>
            </h2>


            <p>
                FemTrack was created as a simple menstrual health
                tracking system where users can keep their period
                information, symptoms, history, and wellness
                activities together in one place.
            </p>


        </section>



        <!-- =====================================================
             STORY
        ====================================================== -->

        <section class="story-section">


            <div class="story-left">

                <div class="story-label">
                    Why we created it
                </div>


                <h2>
                    Because keeping
                    track should feel
                    <em>simple.</em>
                </h2>

            </div>



            <div class="story-right">

                <p>
                    Menstrual health is personal, and every cycle
                    can feel a little different. Dates, symptoms,
                    and experiences can easily become difficult
                    to remember when they are scattered across
                    different places.
                </p>


                <p>
                    FemTrack brings these records into one organised
                    digital space, making it easier to record
                    information and look back at your own history.
                </p>


                <div class="story-signature">
                    — The idea behind FemTrack
                </div>

            </div>


        </section>



        <!-- =====================================================
             FEATURES
        ====================================================== -->

        <section class="feature-section">


            <div class="feature-heading">


                <h2>
                    What's<br>
                    inside.
                </h2>


                <p>
                    Four simple parts of FemTrack designed around
                    recording, reviewing, and caring for your
                    menstrual health.
                </p>


            </div>



            <div class="feature-list">


                <div class="editorial-feature">

                    <div class="feature-number">
                        01
                    </div>

                    <h3>
                        Period Log
                    </h3>

                    <p>
                        Record your period information and keep
                        your cycle history organised.
                    </p>

                </div>



                <div class="editorial-feature">

                    <div class="feature-number">
                        02
                    </div>

                    <h3>
                        Track Symptoms
                    </h3>

                    <p>
                        Record symptoms throughout your cycle
                        and keep your experiences together.
                    </p>

                </div>



                <div class="editorial-feature">

                    <div class="feature-number">
                        03
                    </div>

                    <h3>
                        Reports
                    </h3>

                    <p>
                        Review the information you have recorded
                        and look back at your menstrual history.
                    </p>

                </div>



                <div class="editorial-feature">

                    <div class="feature-number">
                        04
                    </div>

                    <h3>
                        Yoga & Exercise
                    </h3>

                    <p>
                        Explore simple wellness activities that
                        can become part of your self-care routine.
                    </p>

                </div>


            </div>


        </section>



        <!-- =====================================================
             BIG STATEMENT
        ====================================================== -->

        <section class="statement-section">


            <div class="statement-small">
                A reminder
            </div>


            <h2>
                Your cycle changes.
                <br>
                <em>And that's okay.</em>
            </h2>


            <div class="statement-line"></div>


            <p>
                FemTrack is not about making every month look the
                same. It is about giving you a simple place to
                record what happens and become more aware of
                your own patterns.
            </p>


        </section>



        <!-- =====================================================
             FINAL CTA
        ====================================================== -->

        <section class="final-section">


            <div class="final-content">


                <div class="final-small">
                    Welcome to FemTrack
                </div>


                <h2>
                    Know your rhythm.
                    Own your story.
                </h2>


                <p>
                    Keep your menstrual health information
                    organised in one thoughtful space.
                </p>



                <?php if (isset($_SESSION['user_id'])): ?>

                    <a
                        href="user/dashboard.php"
                        class="final-button"
                    >
                        Go to Dashboard →
                    </a>

                <?php else: ?>

                    <a
                        href="login.php"
                        class="final-button"
                    >
                        Get Started →
                    </a>

                <?php endif; ?>


            </div>


        </section>



        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <div class="editorial-footer">

            FemTrack —
            <span>Your Cycle, Your Strength.</span>

        </div>


    </main>


</body>

</html>