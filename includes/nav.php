<?php

/*
|--------------------------------------------------------------------------
| SHARED FEMTRACK NAVIGATION
|--------------------------------------------------------------------------
*/

$isUserPage = (
    strpos($_SERVER['PHP_SELF'], '/user/') !== false
);


/*
|--------------------------------------------------------------------------
| SET PATHS
|--------------------------------------------------------------------------
*/

if ($isUserPage) {

    $home = "../index.php";
    $dashboard = "dashboard.php";
    $symptoms = "track-symptoms.php";
    $period = "period-log.php";
    $reports = "reports.php";
    $about = "../about.php";
    $logout = "../logout.php";

} else {

    $home = "index.php";
    $dashboard = "user/dashboard.php";
    $symptoms = "user/track-symptoms.php";
    $period = "user/period-log.php";
    $reports = "user/reports.php";
    $about = "about.php";
    $logout = "logout.php";
}

?>

<nav class="home-nav" aria-label="Main navigation">

    <!-- LOGO -->
    <a
        class="home-logo"
        href="<?php echo $home; ?>"
        aria-label="FemTrack home"
    >

        <img
            src="<?php echo $isUserPage
                ? '../assets/femtrack-mark.jpeg'
                : 'assets/femtrack-mark.jpeg'; ?>"
            alt=""
        >

        <span>
            Fem<span>Track</span>
        </span>

    </a>


    <!-- MOBILE MENU BUTTON -->
    <button
        type="button"
        class="mobile-menu-toggle"
        id="mobileMenuToggle"
        aria-label="Open navigation menu"
        aria-expanded="false"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>


    <!-- NAVIGATION LINKS -->
    <div
        class="home-nav-links app-nav-links"
        id="femtrackNavigation"
    >

        <a href="<?php echo $home; ?>">
            Home
        </a>

        <a href="<?php echo $dashboard; ?>">
            Dashboard
        </a>

        <a href="<?php echo $symptoms; ?>">
            Track Symptoms
        </a>

        <a href="<?php echo $period; ?>">
            Period Log
        </a>

        <a href="<?php echo $reports; ?>">
            Reports
        </a>

        <a href="<?php echo $about; ?>">
            About Us
        </a>


        <?php if (isset($_SESSION["user_id"])): ?>

            <a
                class="nav-logout"
                href="<?php echo $logout; ?>"
            >
                Logout
            </a>

        <?php endif; ?>

    </div>

</nav>


<style>

/* =========================================================
   MOBILE MENU BUTTON
   Hidden on laptop/desktop
   ========================================================= */

.mobile-menu-toggle {
    display: none;
}


/* =========================================================
   MOBILE NAVIGATION
   ========================================================= */

@media (max-width: 768px) {

    .home-nav {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }


    /* Hamburger button on RIGHT */
    .mobile-menu-toggle {
        display: flex;
        width: 42px;
        height: 42px;
        margin-left: auto;

        padding: 8px;

        border: none;
        background: transparent;

        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 5px;

        cursor: pointer;

        z-index: 1001;
    }


    /* Hamburger lines */
    .mobile-menu-toggle span {
        display: block;

        width: 24px;
        height: 2px;

        background: #2f2940;

        border-radius: 3px;

        transition: all 0.25s ease;
    }


    /* =====================================================
       MENU DROPDOWN
       ===================================================== */

    .home-nav-links {
        display: none;

        position: absolute;

        top: calc(100% + 10px);
        right: 0;

        width: 220px;

        padding: 10px;

        background: #ffffff;

        border: 1px solid rgba(47, 41, 64, 0.08);

        border-radius: 14px;

        box-shadow: 0 10px 30px rgba(47, 41, 64, 0.15);

        flex-direction: column;
        align-items: stretch;

        z-index: 1000;
    }


    /* Show menu */
    .home-nav-links.mobile-open {
        display: flex;
    }


    /* Menu links */
    .home-nav-links a {
        display: block;

        width: 100%;

        padding: 12px 14px;

        text-align: left;

        border-radius: 9px;

        box-sizing: border-box;
    }


    .home-nav-links a:hover {
        background: rgba(155, 93, 229, 0.08);
    }


    /* =====================================================
       HAMBURGER → X
       ===================================================== */

    .mobile-menu-toggle.menu-open span:nth-child(1) {
        transform: translateY(7px) rotate(45deg);
    }

    .mobile-menu-toggle.menu-open span:nth-child(2) {
        opacity: 0;
    }

    .mobile-menu-toggle.menu-open span:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg);
    }

}


/* =========================================================
   SMALL PHONES
   ========================================================= */

@media (max-width: 400px) {

    .home-nav-links {
        width: 200px;
    }

}

</style>


<script>

document.addEventListener("DOMContentLoaded", function () {

    const menuButton = document.getElementById("mobileMenuToggle");
    const navigation = document.getElementById("femtrackNavigation");

    if (!menuButton || !navigation) {
        return;
    }


    menuButton.addEventListener("click", function () {

        const isOpen = navigation.classList.toggle("mobile-open");

        menuButton.classList.toggle("menu-open", isOpen);

        menuButton.setAttribute(
            "aria-expanded",
            isOpen ? "true" : "false"
        );

    });


    /* Close menu after clicking a link */
    const menuLinks = navigation.querySelectorAll("a");

    menuLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            navigation.classList.remove("mobile-open");

            menuButton.classList.remove("menu-open");

            menuButton.setAttribute(
                "aria-expanded",
                "false"
            );

        });

    });

});

</script>