<?php

session_start();

require_once __DIR__ . "/config/database.php";


/*
|--------------------------------------------------------------------------
| ALREADY LOGGED IN CHECK
|--------------------------------------------------------------------------
| If the user is already logged in, check their role and send them
| to the correct dashboard.
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["user_id"])) {

    $user_id = $_SESSION["user_id"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT role FROM users WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $logged_in_user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    if ($logged_in_user && $logged_in_user["role"] === "admin") {

        header("Location: user/admin.php");
        exit;

    } else {

        header("Location: user/dashboard.php");
        exit;
    }
}


$message = "";


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, password, role
             FROM users
             WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);


        /*
        |--------------------------------------------------------------------------
        | CHECK PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            $user &&
            password_verify(
                $password,
                $user["password"]
            )
        ) {


            /*
            |--------------------------------------------------------------------------
            | REGENERATE SESSION ID
            |--------------------------------------------------------------------------
            | Helps protect the login session.
            |--------------------------------------------------------------------------
            */

            session_regenerate_id(true);


            /*
            |--------------------------------------------------------------------------
            | CREATE LOGIN SESSION
            |--------------------------------------------------------------------------
            */

            $_SESSION["user_id"] = $user["id"];

            $_SESSION["role"] = $user["role"];


            /*
            |--------------------------------------------------------------------------
            | ADMIN LOGIN
            |--------------------------------------------------------------------------
            */

            if ($user["role"] === "admin") {

                header("Location: user/admin.php");
                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | NORMAL MEMBER LOGIN
            |--------------------------------------------------------------------------
            */

            $redirect = $_SESSION["redirect_after_login"]
                ?? "user/dashboard.php";


            unset($_SESSION["redirect_after_login"]);


            header("Location: " . $redirect);
            exit;


        } else {

            $message = "Invalid email or password.";
        }


        mysqli_stmt_close($stmt);
    }
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

    <title>Login - FemTrack</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body class="auth-page">


    <main class="auth-card">


        <div class="brand">
            FemTrack
        </div>


        <h1>
            Welcome back
        </h1>


        <p class="lead">
            Log in to keep your cycle information private,
            organized, and easy to follow.
        </p>


        <?php if (!empty($message)): ?>

            <p class="notice">
                <?php
                echo htmlspecialchars($message);
                ?>
            </p>

        <?php endif; ?>


        <form
            method="POST"
            class="form-stack"
        >


            <label>

                Email

                <input
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                >

            </label>


            <label>

                Password

                <input
                    type="password"
                    name="password"
                    placeholder="Your password"
                    required
                >

            </label>


            <button
                class="button"
                type="submit"
            >
                Log in
            </button>


        </form>


        <p class="text-center muted">

            New to FemTrack?

            <a href="register.php">
                Create an account
            </a>

        </p>


    </main>


</body>

</html>