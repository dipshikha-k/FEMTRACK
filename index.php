<?php
session_start();



// Drop the supplied hero image in assets/home-hero.png to use it automatically.
$heroImage = file_exists(__DIR__ . "/assets/home-hero.png")
    ? "assets/home-hero.png"
    : "assets/femtrack-logo.jpeg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FemTrack — Your cycle, your space</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="home-page">
    <nav class="home-nav" aria-label="Main navigation">
        <a class="home-logo" href="index.php" aria-label="FemTrack home">
            <img src="assets/femtrack-mark.jpeg" alt="">
            <span>Fem<span>Track</span></span>
        </a>
        <div class="home-nav-links app-nav-links">
            <a class="active" href="index.php">Home</a>
            <a href="user/dashboard.php">Dashboard</a>
            <a href="user/track-symptoms.php">Track Symptoms</a>
            <a href="user/reports.php">Reports</a>
            <a href="user/about.php">About Us</a>
            <a class="nav-logout" href="logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <section class="home-hero">
            <div class="home-hero-copy">
                <p class="home-kicker">YOUR CYCLE. YOUR RHYTHM.</p>
                <h1>Feel more in tune with <em>you.</em></h1>
                <p>FemTrack is your soft, private space to record your cycle and keep the details that matter close.</p>
                <div class="hero-actions">
                    <a class="button" href="register.php">Create your space <span aria-hidden="true">→</span></a>
                    <a class="text-link" href="login.php">I already have my space</a>
                </div>
            </div>
            <div class="hero-visual" role="img" aria-label="FemTrack wellness illustration">
                <div class="hero-glow"></div>
                <img src="<?php echo $heroImage; ?>" alt="" aria-hidden="true">
                <div class="floating-note note-one">made gently for you ♡</div>
                <div class="floating-note note-two">your private &amp; little space ♡</div>
            </div>
        </section>

        <section id="how-it-works" class="home-section steps-section">
            <div class="section-intro">
                <p class="home-kicker">SIMPLE BY DESIGN</p>
                <h2>A little clarity can feel like <em>everything.</em></h2>
            </div>
            <div class="steps-grid">
                <article><span>01</span><h3>Make it yours</h3><p>Create your private FemTrack account in a moment.</p></article>
                <article><span>02</span><h3>Log your cycle</h3><p>Save period dates and flow details whenever you need to.</p></article>
                <article><span>03</span><h3>Look back gently</h3><p>See your past records in one calm, organised place.</p></article>
            </div>
        </section>

        <section id="why-femtrack" class="home-section promise-section">
            <div><p class="home-kicker">A SPACE THAT FEELS LIKE YOURS</p><h2>Support your wellbeing, one cycle at a time.</h2></div>
            <a class="button light-button" href="register.php">Start tracking today</a>
        </section>
    </main>
</body>
</html>
