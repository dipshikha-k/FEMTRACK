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
    </html>