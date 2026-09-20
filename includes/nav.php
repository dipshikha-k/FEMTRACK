
<?php

/*
|--------------------------------------------------------------------------
| FEMTRACK SHARED TOP BAR
|--------------------------------------------------------------------------
*/

$isUserPage = (
    strpos($_SERVER['PHP_SELF'], '/user/') !== false
);


/*
|--------------------------------------------------------------------------
| PATHS
|--------------------------------------------------------------------------
*/

if ($isUserPage) {

    $home = "../index.php";
    $dashboard = "dashboard.php";
    $symptoms = "track-symptoms.php";
    $period = "period-log.php";
    $reports = "reports.php";
    $yoga = "yoga-exercise.php";
    $about = "../about.php";
    $logout = "../logout.php";

} else {

    $home = "index.php";
    $dashboard = "user/dashboard.php";
    $symptoms = "user/track-symptoms.php";
    $period = "user/period-log.php";
    $reports = "user/reports.php";
    $yoga = "user/yoga-exercise.php";
    $about = "about.php";
    $logout = "logout.php";
}

?>

<header class="femtrack-topbar">

    <div class="femtrack-topbar-inner">

        <!-- LOGO -->
        <a
            href="<?php echo $home; ?>"
            class="femtrack-brand"
            aria-label="FemTrack Home"
        >

            <img
                src="<?php echo $isUserPage
                    ? '../assets/femtrack-mark.jpeg'
                    : 'assets/femtrack-mark.jpeg'; ?>"
                alt="FemTrack"
            >

            <span class="femtrack-brand-text">
                Fem<span>Track</span>
            </span>

        </a>


        <!-- MOBILE MENU BUTTON -->
        <button
            type="button"
            class="femtrack-menu-button"
            id="mobileMenuToggle"
            aria-label="Open navigation menu"
            aria-expanded="false"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>


        <!-- NAVIGATION -->
        <nav
            class="femtrack-topbar-nav"
            id="femtrackNavigation"
            aria-label="Main navigation"
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

            <a href="<?php echo $yoga; ?>">
                Yoga &amp; Exercise
            </a>

            <a href="<?php echo $about; ?>">
                About Us
            </a>


            <?php if (isset($_SESSION["user_id"])): ?>

                <a
                    href="<?php echo $logout; ?>"
                    class="femtrack-logout"
                >
                    Logout
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<style>

/* =========================================================
   FEMTRACK TOP BAR
   ========================================================= */

.femtrack-topbar {
    width: 100%;
    height: 76px;

    position: relative;
    z-index: 9999;

    background: rgba(255, 255, 255, 0.97);

    border-bottom: 1px solid rgba(47, 41, 64, 0.08);

    box-shadow: 0 3px 15px rgba(47, 41, 64, 0.06);

    box-sizing: border-box;
}


.femtrack-topbar-inner {
    width: 100%;
    max-width: 1400px;

    height: 76px;

    margin: 0 auto;

    padding: 0 42px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;
}


/* =========================================================
   LOGO
   ========================================================= */

.femtrack-brand {
    display: inline-flex;

    align-items: center;

    gap: 9px;

    flex-shrink: 0;

    text-decoration: none;

    height: 50px;

    box-sizing: border-box;
}


.femtrack-brand img {
    display: block;

    width: 38px !important;
    height: 38px !important;

    min-width: 38px !important;
    max-width: 38px !important;

    min-height: 38px !important;
    max-height: 38px !important;

    object-fit: cover;

    border-radius: 50%;

    margin: 0 !important;
    padding: 0 !important;

    box-sizing: border-box;
}


.femtrack-brand-text {
    display: inline-block;

    margin: 0;
    padding: 0;

    font-family: "Playfair Display", serif;

    font-size: 25px;

    line-height: 1;

    font-weight: 600;

    color: #2f2940;

    white-space: nowrap;

    box-sizing: border-box;
}


.femtrack-brand-text span {
    color: #9b5de5;
}


/* =========================================================
   DESKTOP NAVIGATION
   ========================================================= */

.femtrack-topbar-nav {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 5px;

    margin-left: auto;

    box-sizing: border-box;
}


.femtrack-topbar-nav a {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 40px;

    padding: 8px 13px;

    border-radius: 9px;

    text-decoration: none;

    font-family: "Belleza", sans-serif;

    font-size: 15px;

    line-height: 1.2;

    color: #2f2940;

    white-space: nowrap;

    box-sizing: border-box;

    transition:
        background 0.2s ease,
        color 0.2s ease;
}


.femtrack-topbar-nav a:hover {
    background: rgba(155, 93, 229, 0.08);

    color: #7d3fc2;
}


/* =========================================================
   LOGOUT
   ========================================================= */

.femtrack-topbar-nav .femtrack-logout {
    margin-left: 4px;

    color: #7d3fc2;
}


/* =========================================================
   MOBILE BUTTON
   ========================================================= */

.femtrack-menu-button {
    display: none;

    width: 42px;
    height: 42px;

    padding: 8px;

    margin: 0;

    border: none;

    background: transparent;

    cursor: pointer;

    align-items: center;

    justify-content: center;

    flex-direction: column;

    gap: 5px;

    flex-shrink: 0;

    box-sizing: border-box;
}


.femtrack-menu-button span {
    display: block;

    width: 24px;
    height: 2px;

    margin: 0;
    padding: 0;

    background: #2f2940;

    border-radius: 3px;

    transition: all 0.25s ease;

    box-sizing: border-box;
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 1050px) {

    .femtrack-topbar-inner {
        padding-left: 25px;
        padding-right: 25px;
    }

    .femtrack-topbar-nav {
        gap: 2px;
    }

    .femtrack-topbar-nav a {
        padding-left: 9px;
        padding-right: 9px;

        font-size: 14px;
    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 768px) {

    .femtrack-topbar {
        height: 68px;
    }


    .femtrack-topbar-inner {
        height: 68px;

        padding-left: 18px;
        padding-right: 18px;
    }


    .femtrack-brand img {
        width: 34px !important;
        height: 34px !important;

        min-width: 34px !important;
        max-width: 34px !important;

        min-height: 34px !important;
        max-height: 34px !important;
    }


    .femtrack-brand-text {
        font-size: 22px;
    }


    .femtrack-menu-button {
        display: flex;

        margin-left: auto;
    }


    .femtrack-topbar-nav {
        display: none;

        position: absolute;

        top: 68px;
        right: 18px;

        width: 225px;

        padding: 9px;

        margin: 0;

        background: #ffffff;

        border: 1px solid rgba(47, 41, 64, 0.08);

        border-radius: 14px;

        box-shadow: 0 10px 30px rgba(47, 41, 64, 0.15);

        flex-direction: column;

        align-items: stretch;

        box-sizing: border-box;
    }


    .femtrack-topbar-nav.mobile-open {
        display: flex;
    }


    .femtrack-topbar-nav a {
        display: flex;

        width: 100%;

        min-height: 43px;

        padding: 11px 13px;

        justify-content: flex-start;

        text-align: left;

        border-radius: 9px;

        box-sizing: border-box;
    }


    .femtrack-topbar-nav .femtrack-logout {
        margin-left: 0;
    }


    /* Hamburger → X */

    .femtrack-menu-button.menu-open span:nth-child(1) {
        transform: translateY(7px) rotate(45deg);
    }

    .femtrack-menu-button.menu-open span:nth-child(2) {
        opacity: 0;
    }

    .femtrack-menu-button.menu-open span:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg);
    }

}


/* =========================================================
   SMALL PHONES
   ========================================================= */

@media (max-width: 400px) {

    .femtrack-topbar-inner {
        padding-left: 13px;
        padding-right: 13px;
    }


    .femtrack-brand-text {
        font-size: 20px;
    }


    .femtrack-topbar-nav {
        right: 13px;

        width: 205px;
    }

}

</style>


<script>

document.addEventListener("DOMContentLoaded", function () {

    const menuButton =
        document.getElementById("mobileMenuToggle");

    const navigation =
        document.getElementById("femtrackNavigation");


    if (!menuButton || !navigation) {
        return;
    }


    menuButton.addEventListener("click", function () {

        const isOpen =
            navigation.classList.toggle("mobile-open");

        menuButton.classList.toggle(
            "menu-open",
            isOpen
        );

        menuButton.setAttribute(
            "aria-expanded",
            isOpen ? "true" : "false"
        );

    });


    const links =
        navigation.querySelectorAll("a");


    links.forEach(function (link) {

        link.addEventListener("click", function () {

            navigation.classList.remove(
                "mobile-open"
            );

            menuButton.classList.remove(
                "menu-open"
            );

            menuButton.setAttribute(
                "aria-expanded",
                "false"
            );

        });

    });


    document.addEventListener("click", function (event) {

        if (
            !navigation.contains(event.target) &&
            !menuButton.contains(event.target)
        ) {

            navigation.classList.remove(
                "mobile-open"
            );

            menuButton.classList.remove(
                "menu-open"
            );

            menuButton.setAttribute(
                "aria-expanded",
                "false"
            );

        }

    });

});

</script>

