<?php
/*
|--------------------------------------------------------------------------
| FEMTRACK — SHARED NAVBAR
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isUserPage = strpos($_SERVER['PHP_SELF'], '/user/') !== false;

/*
|--------------------------------------------------------------------------
| PATHS
|--------------------------------------------------------------------------
*/

if ($isUserPage) {

    $home     = "../index.php";
    $dashboard = "dashboard.php";
    $symptoms  = "track-symptoms.php";
    $period    = "period-log.php";
    $reports   = "reports.php";
    $yoga      = "yoga-exercise.php";
    $about     = "../about.php";
    $logout    = "../logout.php";
    $logo      = "../assets/femtrack-mark.jpeg";

} else {

    $home     = "index.php";
    $dashboard = "user/dashboard.php";
    $symptoms  = "user/track-symptoms.php";
    $period    = "user/period-log.php";
    $reports   = "user/reports.php";
    $yoga      = "user/yoga-exercise.php";
    $about     = "about.php";
    $logout    = "logout.php";
    $logo      = "assets/femtrack-mark.jpeg";
}


/*
|--------------------------------------------------------------------------
| CURRENT PAGE
|--------------------------------------------------------------------------
*/

$currentPage = basename($_SERVER['PHP_SELF']);

function femtrackActive($page, $currentPage)
{
    return $page === $currentPage ? 'is-active' : '';
}

?>

<header class="ft-navbar">

    <div class="ft-navbar-inner">

        <!-- BRAND -->
        <a
            href="<?php echo $home; ?>"
            class="ft-brand"
            aria-label="FemTrack Home"
        >

            <img
                src="<?php echo $logo; ?>"
                alt="FemTrack"
            >

            <span class="ft-brand-name">
                Fem<span>Track</span>
            </span>

        </a>


        <!-- MOBILE BUTTON -->
        <button
            type="button"
            class="ft-menu-button"
            id="ftMenuButton"
            aria-label="Open navigation menu"
            aria-expanded="false"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>


        <!-- NAVIGATION -->
        <nav
            class="ft-nav"
            id="ftNavigation"
            aria-label="Main navigation"
        >

            <a
                href="<?php echo $home; ?>"
                class="<?php echo femtrackActive('index.php', $currentPage); ?>"
            >
                Home
            </a>

            <a
                href="<?php echo $dashboard; ?>"
                class="<?php echo femtrackActive('dashboard.php', $currentPage); ?>"
            >
                Dashboard
            </a>

            <a
                href="<?php echo $symptoms; ?>"
                class="<?php echo femtrackActive('track-symptoms.php', $currentPage); ?>"
            >
                Track Symptoms
            </a>

            <a
                href="<?php echo $period; ?>"
                class="<?php echo femtrackActive('period-log.php', $currentPage); ?>"
            >
                Period Log
            </a>

            <a
                href="<?php echo $reports; ?>"
                class="<?php echo femtrackActive('reports.php', $currentPage); ?>"
            >
                Reports
            </a>

            <a
                href="<?php echo $yoga; ?>"
                class="<?php echo femtrackActive('yoga-exercise.php', $currentPage); ?>"
            >
                Yoga &amp; Exercise
            </a>

            <a
                href="<?php echo $about; ?>"
                class="<?php echo femtrackActive('about.php', $currentPage); ?>"
            >
                About Us
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>

                <a
                    href="<?php echo $logout; ?>"
                    class="ft-logout"
                >
                    Logout
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<style>

/* ==========================================================
   FEMTRACK NAVBAR
   Completely isolated from page CSS
   ========================================================== */

.ft-navbar,
.ft-navbar * {
    box-sizing: border-box;
}


/* ==========================================================
   NAVBAR
   ========================================================== */

.ft-navbar {

    width: 100%;
    min-height: 70px;

    background: rgba(255, 250, 252, 0.97);

    border-bottom: 1px solid #f4dbe5;

    box-shadow:
        0 5px 18px rgba(102, 45, 78, 0.08);

    position: sticky;
    top: 0;

    z-index: 99999;

    margin: 0;
    padding: 0;

    font-family:
        Inter,
        ui-sans-serif,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
}


/* ==========================================================
   INNER
   ========================================================== */

.ft-navbar-inner {

    width: 100%;
    max-width: 1200px;

    min-height: 70px;

    margin: 0 auto;

    padding:
        12px
        18px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 24px;
}


/* ==========================================================
   BRAND
   ========================================================== */

.ft-brand {

    display: inline-flex;

    align-items: center;

    gap: 10px;

    flex-shrink: 0;

    color: #3d1934 !important;

    text-decoration: none !important;

    font-size: 1.35rem;

    font-weight: 900;

    letter-spacing: -0.06em;

    line-height: 1;
}


.ft-brand:hover {

    color: #3d1934 !important;

    text-decoration: none !important;
}


.ft-brand img {

    display: block;

    width: 44px !important;
    height: 44px !important;

    min-width: 44px !important;
    min-height: 44px !important;

    max-width: 44px !important;
    max-height: 44px !important;

    object-fit: cover;

    border-radius: 50%;

    border: 0 !important;

    box-shadow: none !important;

    margin: 0 !important;

    padding: 0 !important;
}


.ft-brand-name {

    display: inline-block;

    color: #3d1934 !important;

    font-size: 1.35rem;

    font-weight: 900;

    line-height: 1;

    letter-spacing: -0.06em;

    white-space: nowrap;
}


.ft-brand-name span {

    color: #d75b93 !important;
}


/* ==========================================================
   DESKTOP NAV
   ========================================================== */

.ft-nav {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: clamp(12px, 2vw, 30px);

    margin-left: auto;

    flex-wrap: nowrap;
}


.ft-nav a {

    position: relative;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 40px;

    padding: 9px 0 !important;

    margin: 0 !important;

    border: 0 !important;

    border-radius: 0 !important;

    background: transparent !important;

    color: #70485f !important;

    font-family:
        Inter,
        ui-sans-serif,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif !important;

    font-size: 0.92rem !important;

    font-weight: 750 !important;

    line-height: 1.25 !important;

    letter-spacing: normal;

    text-decoration: none !important;

    white-space: nowrap;

    box-shadow: none !important;

    transition:
        color 0.2s ease,
        opacity 0.2s ease;
}


/* HOVER */

.ft-nav a:hover {

    color: #c74d86 !important;

    background: transparent !important;

    text-decoration: none !important;

    box-shadow: none !important;
}


/* ACTIVE */

.ft-nav a.is-active {

    color: #c64e86 !important;

    background: transparent !important;

    font-weight: 750 !important;
}


/* ACTIVE LINE */

.ft-nav a.is-active::after {

    content: "";

    position: absolute;

    left: 50%;

    bottom: 3px;

    width: 24px;

    height: 2px;

    transform: translateX(-50%);

    background: #c64e86;

    border-radius: 999px;
}


/* ==========================================================
   LOGOUT
   ========================================================== */

.ft-nav a.ft-logout {

    min-height: auto;

    padding:
        11px
        18px !important;

    margin-left: 0 !important;

    border-radius: 999px !important;

    background: #3d1934 !important;

    color: #ffffff !important;

    font-weight: 750 !important;
}


.ft-nav a.ft-logout:hover {

    background: #6d2a5a !important;

    color: #ffffff !important;

    text-decoration: none !important;
}


.ft-nav a.ft-logout::after {

    display: none !important;
}


/* ==========================================================
   MOBILE BUTTON
   ========================================================== */

.ft-menu-button {

    display: none;

    width: 42px;
    height: 42px;

    padding: 8px;

    margin: 0;

    border: 0;

    border-radius: 10px;

    background: transparent;

    cursor: pointer;

    flex-shrink: 0;

    align-items: center;

    justify-content: center;

    flex-direction: column;

    gap: 5px;
}


.ft-menu-button:hover {

    background: rgba(198, 78, 134, 0.08);
}


.ft-menu-button span {

    display: block;

    width: 24px;
    height: 2px;

    background: #3d1934;

    border-radius: 999px;

    transition:
        transform 0.25s ease,
        opacity 0.25s ease;
}


/* ==========================================================
   MOBILE
   ========================================================== */

@media (max-width: 900px) {

    .ft-navbar-inner {

        min-height: 68px;

        padding:
            12px
            18px;
    }


    .ft-menu-button {

        display: flex;

        margin-left: auto;
    }


    .ft-nav {

        display: none;

        position: absolute;

        top: 68px;

        right: 18px;

        width: min(
            280px,
            calc(100vw - 36px)
        );

        padding: 10px;

        margin: 0;

        background: #fffafb;

        border:
            1px solid
            #f4dbe5;

        border-radius: 15px;

        box-shadow:
            0 14px 35px
            rgba(102, 45, 78, 0.14);

        flex-direction: column;

        align-items: stretch;

        gap: 3px;

        z-index: 100000;
    }


    .ft-nav.is-open {

        display: flex;
    }


    .ft-nav a {

        width: 100%;

        min-height: 42px;

        padding:
            10px 12px !important;

        justify-content: flex-start;

        border-radius: 9px !important;

        font-size: 0.9rem !important;

        text-align: left;
    }


    .ft-nav a.is-active {

        background:
            rgba(198, 78, 134, 0.08) !important;
    }


    .ft-nav a.is-active::after {

        display: none;
    }


    .ft-nav a.ft-logout {

        margin-top: 5px !important;

        justify-content: center;

        padding:
            10px 14px !important;
    }


    /* Hamburger -> X */

    .ft-menu-button.is-open span:nth-child(1) {

        transform:
            translateY(7px)
            rotate(45deg);
    }


    .ft-menu-button.is-open span:nth-child(2) {

        opacity: 0;
    }


    .ft-menu-button.is-open span:nth-child(3) {

        transform:
            translateY(-7px)
            rotate(-45deg);
    }

}


/* ==========================================================
   SMALL PHONES
   ========================================================== */

@media (max-width: 420px) {

    .ft-navbar-inner {

        padding-left: 13px;
        padding-right: 13px;
    }


    .ft-brand-name {

        font-size: 1.2rem;
    }


    .ft-brand img {

        width: 38px !important;
        height: 38px !important;

        min-width: 38px !important;
        min-height: 38px !important;

        max-width: 38px !important;
        max-height: 38px !important;
    }


    .ft-nav {

        right: 13px;

        width:
            calc(100vw - 26px);
    }

}


/* ==========================================================
   PREVENT COMMON PROJECT CSS FROM CHANGING THE NAVBAR
   ========================================================== */

.ft-navbar a,
.ft-navbar a:hover,
.ft-navbar a:focus,
.ft-navbar a:active {

    text-decoration: none !important;
}


.ft-navbar img {

    max-width: none;
}


.ft-navbar button {

    font-family: inherit;
}

</style>


<script>

document.addEventListener("DOMContentLoaded", function () {

    const menuButton =
        document.getElementById("ftMenuButton");

    const navigation =
        document.getElementById("ftNavigation");


    if (!menuButton || !navigation) {
        return;
    }


    function closeMenu() {

        navigation.classList.remove("is-open");

        menuButton.classList.remove("is-open");

        menuButton.setAttribute(
            "aria-expanded",
            "false"
        );
    }


    menuButton.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();

            const open =
                navigation.classList.toggle("is-open");

            menuButton.classList.toggle(
                "is-open",
                open
            );

            menuButton.setAttribute(
                "aria-expanded",
                open ? "true" : "false"
            );

        }
    );


    navigation
        .querySelectorAll("a")
        .forEach(function (link) {

            link.addEventListener(
                "click",
                closeMenu
            );

        });


    document.addEventListener(
        "click",
        function (event) {

            if (
                !navigation.contains(event.target) &&
                !menuButton.contains(event.target)
            ) {

                closeMenu();

            }

        }
    );


    window.addEventListener(
        "resize",
        function () {

            if (window.innerWidth > 900) {

                closeMenu();

            }

        }
    );

});

</script>