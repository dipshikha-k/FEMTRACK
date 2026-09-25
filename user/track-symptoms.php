
<?php

session_start();
date_default_timezone_set("Asia/Kathmandu");

require_once __DIR__ . "/../config/database.php";

/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$message = "";
$message_type = "";

$femtrackOverview = "";
$femtrackCare = "";
$femtrackHype = "";

$symptom_date = date("Y-m-d");


/*
|--------------------------------------------------------------------------
| CREATE SYMPTOM TABLE IF IT DOES NOT EXIST
|--------------------------------------------------------------------------
*/

$conn->query("
    CREATE TABLE IF NOT EXISTS symptom_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        symptom_date DATE NOT NULL,
        symptoms TEXT,
        severity VARCHAR(50),
        mood VARCHAR(50),
        mood_swings VARCHAR(20),
        cravings VARCHAR(50),
        bloating VARCHAR(50),
        sleep VARCHAR(50),
        energy VARCHAR(50),
        pain VARCHAR(50),
        skin VARCHAR(50),
        medication VARCHAR(255),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");


/*
|--------------------------------------------------------------------------
| ADD MISSING COLUMNS
|--------------------------------------------------------------------------
*/

$requiredColumns = [
    "symptom_date" => "DATE NOT NULL",
    "symptoms" => "TEXT",
    "severity" => "VARCHAR(50)",
    "mood" => "VARCHAR(50)",
    "mood_swings" => "VARCHAR(20)",
    "cravings" => "VARCHAR(50)",
    "bloating" => "VARCHAR(50)",
    "sleep" => "VARCHAR(50)",
    "energy" => "VARCHAR(50)",
    "pain" => "VARCHAR(50)",
    "skin" => "VARCHAR(50)",
    "medication" => "VARCHAR(255)",
    "notes" => "TEXT",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
];

foreach ($requiredColumns as $column => $definition) {

    $checkColumn = $conn->query("
        SHOW COLUMNS FROM symptom_logs LIKE '$column'
    ");

    if ($checkColumn && $checkColumn->num_rows == 0) {

        $conn->query("
            ALTER TABLE symptom_logs
            ADD COLUMN `$column` $definition
        ");
    }
}


/*
|--------------------------------------------------------------------------
| FEMTRACK ANALYSIS FUNCTION
|--------------------------------------------------------------------------
*/

function generateFemTrackAnalysis(
    $symptoms,
    $severity,
    $mood,
    $mood_swings,
    $cravings,
    $bloating,
    $sleep,
    $energy,
    $pain,
    $skin,
    $medication
) {

    $symptoms = array_filter($symptoms);

    $physicalConcern = false;
    $emotionalConcern = false;
    $recoveryConcern = false;

    $careTips = [];
    $overview = "";
    $hype = "";


    /*
    |--------------------------------------------------------------------------
    | PHYSICAL SYMPTOMS
    |--------------------------------------------------------------------------
    */

    if (in_array("Cramps", $symptoms)) {

        $physicalConcern = true;

        $careTips[] =
            "Try a warm heating pad, warm shower, and a little gentle stretching for the cramps.";
    }

    if (in_array("Headache", $symptoms)) {

        $physicalConcern = true;

        $careTips[] =
            "Keep sipping water and give your eyes a small screen break if your head feels heavy.";
    }

    if (in_array("Back Pain", $symptoms)) {

        $physicalConcern = true;

        $careTips[] =
            "A warm compress and some slow back stretches can help your body relax.";
    }

    if (in_array("Nausea", $symptoms)) {

        $physicalConcern = true;

        $careTips[] =
            "Go easy on your stomach today. Small light meals and slow sips of water may feel better.";
    }

    if (in_array("Breast Tenderness", $symptoms)) {

        $physicalConcern = true;

        $careTips[] =
            "Give yourself some extra comfort today and choose a supportive, comfortable bra.";
    }

    if (in_array("Fatigue", $symptoms)) {

        $recoveryConcern = true;

        $careTips[] =
            "Your body may be asking for a slower day, so don't feel guilty about taking proper rest.";
    }


    /*
    |--------------------------------------------------------------------------
    | SEVERITY
    |--------------------------------------------------------------------------
    */

    if ($severity === "Very Uncomfortable") {

        $physicalConcern = true;

        $careTips[] =
            "Your symptoms sound pretty intense today, so prioritize rest and avoid pushing yourself too hard.";
    }

    if ($severity === "Uncomfortable") {

        $physicalConcern = true;

        $careTips[] =
            "Keep today a little gentler than usual and listen to what your body is asking for.";
    }


    /*
    |--------------------------------------------------------------------------
    | PAIN
    |--------------------------------------------------------------------------
    */

    if ($pain === "Moderate") {

        $physicalConcern = true;

        $careTips[] =
            "Moderate pain deserves some extra care today—try warmth, hydration, and comfortable movement.";
    }

    if ($pain === "Severe") {

        $physicalConcern = true;

        $careTips[] =
            "Since you marked the pain as severe, take it seriously and consider getting medical advice if it is unusual, persistent, or interfering with normal activities.";
    }


    /*
    |--------------------------------------------------------------------------
    | BLOATING
    |--------------------------------------------------------------------------
    */

    if ($bloating === "Moderate") {

        $physicalConcern = true;

        $careTips[] =
            "For bloating, stay hydrated and try lighter meals if your stomach feels uncomfortable.";
    }

    if ($bloating === "Severe") {

        $physicalConcern = true;

        $careTips[] =
            "Your bloating sounds strong today, so keep meals comfortable and pay attention if it continues or becomes unusually painful.";
    }


    /*
    |--------------------------------------------------------------------------
    | MOOD
    |--------------------------------------------------------------------------
    */

    $negativeMoods = [
        "Sad",
        "Irritated",
        "Anxious",
        "Stressed",
        "Low"
    ];

    if (in_array($mood, $negativeMoods)) {

        $emotionalConcern = true;

        if ($mood === "Anxious") {

            $careTips[] =
                "Take a few slow breaths and give yourself a little quiet time instead of trying to power through everything.";
        }

        elseif ($mood === "Stressed") {

            $careTips[] =
                "Your brain deserves a break too. Pick one small task at a time and give yourself permission to pause.";
        }

        elseif ($mood === "Irritated") {

            $careTips[] =
                "If everything feels extra annoying today, step away for a few minutes, breathe, and give yourself some space.";
        }

        elseif ($mood === "Sad" || $mood === "Low") {

            $careTips[] =
                "Keep today soft. Talk to someone you trust, watch something comforting, or do one tiny thing that normally makes you smile.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MOOD SWINGS
    |--------------------------------------------------------------------------
    */

    if ($mood_swings === "Yes") {
        

        $emotionalConcern = true;

        $careTips[] =
            "Mood swings can make a day feel unpredictable, so don't judge yourself for feeling different from one moment to the next.";
    }


    /*
    |--------------------------------------------------------------------------
    | SLEEP
    |--------------------------------------------------------------------------
    */

    if (
        $sleep === "Less than 5 hours" ||
        $sleep === "Poor"
    ) {

        $recoveryConcern = true;

        $careTips[] =
            "Your sleep looks low today, so if you can, make tonight a proper recharge night.";
    }

    elseif ($sleep === "5–6 hours") {

        $recoveryConcern = true;

        $careTips[] =
            "You got a little less sleep than ideal, so keep your schedule lighter if your energy starts dropping.";
    }


    /*
    |--------------------------------------------------------------------------
    | ENERGY
    |--------------------------------------------------------------------------
    */

    if (
        $energy === "Low" ||
        $energy === "Very Low"
    ) {

        $recoveryConcern = true;

        $careTips[] =
            "Your energy is running low, so hydration, food, and a little extra rest should be priorities today.";
    }


    /*
    |--------------------------------------------------------------------------
    | CRAVINGS
    |--------------------------------------------------------------------------
    */

    if ($cravings !== "None" && !empty($cravings)) {

        $careTips[] =
            "Cravings are totally okay. Try to enjoy what you're craving while still getting a proper meal and enough water.";
    }


    /*
    |--------------------------------------------------------------------------
    | SKIN
    |--------------------------------------------------------------------------
    */

    if ($skin === "Breakouts") {

        $careTips[] =
            "Your skin is having a moment, so keep your routine simple and avoid picking at breakouts.";
    }

    elseif ($skin === "Oily") {

        $careTips[] =
            "Keep your skincare light today and avoid piling on too many products.";
    }

    elseif ($skin === "Dry") {

        $careTips[] =
            "Your skin seems a little dry, so gentle cleansing and enough hydration can help keep things comfortable.";
    }


    /*
    |--------------------------------------------------------------------------
    | COMBINATION ANALYSIS
    |--------------------------------------------------------------------------
    */

    if (
        $physicalConcern &&
        $emotionalConcern &&
        $recoveryConcern
    ) {

        $overview =
            "Okayyy, your check-in says your body, mood, and energy are all asking for a little extra attention today. This is definitely a slower-day moment, not a push-through-everything moment.";
    }

    elseif (
        $physicalConcern &&
        $emotionalConcern
    ) {

        $overview =
            "Your body and mood both seem a little sensitive today. When physical discomfort and emotions show up together, giving yourself extra comfort can make the whole day feel easier.";
    }

    elseif (
        $physicalConcern &&
        $recoveryConcern
    ) {

        $overview =
            "Your body seems to be doing a lot today, especially with the physical symptoms plus lower recovery signals. Keep things gentle and let yourself recharge.";
    }

    elseif (
        $emotionalConcern &&
        $recoveryConcern
    ) {

        $overview =
            "Your mood and energy are both giving 'please slow down' energy today. You don't need to be productive every second—rest is part of taking care of yourself.";
    }

    elseif (count($symptoms) >= 3) {

        $overview =
            "You've got a few physical symptoms showing up at once today, so your body is clearly asking for some extra care. Keep things comfortable and don't overdo it.";
    }

    elseif ($physicalConcern) {

        $overview =
            "Your check-in shows a few physical discomfort signals today. Nothing says you have to power through—make comfort and recovery part of your plan.";
    }

    elseif ($emotionalConcern) {

        $overview =
            "Your emotional check-in is giving a softer-day vibe. Be kind to yourself and don't expect yourself to feel exactly the same every day.";
    }

    elseif ($recoveryConcern) {

        $overview =
            "Your energy and recovery signals look like they could use some attention today. A little more rest, food, water, and downtime can go a long way.";
    }

    else {

        $overview =
            "Your check-in looks pretty balanced today. Keep listening to your body and enjoy the little things that make your day feel good.";
    }


    /*
    |--------------------------------------------------------------------------
    | MEDICATION
    |--------------------------------------------------------------------------
    */

    if (!empty(trim($medication))) {

        $careTips[] =
            "You logged medication today. Keep following the instructions given to you and don't change the dose on your own.";
    }


    $careTips = array_values(array_unique($careTips));

    $careTips = array_slice($careTips, 0, 4);


    /*
    |--------------------------------------------------------------------------
    | HYPE MESSAGE
    |--------------------------------------------------------------------------
    */

    $hypeMessages = [

        "Tiny mission for today: drink some water, put on your favorite outfit or lip gloss, and romanticize the next 10 minutes. ✨",

        "Your body is doing enough already. Put on your comfort song, get cozy, and give yourself permission to have a soft-girl moment. 🎧💗",

        "Your rest matters too. Grab your favorite drink, freshen up, and do one tiny thing that makes you feel like yourself again. 🫶",

        "Today's vibe: less pressure, more comfort. Maybe cute nails, skincare, a funny show, or texting the person who always makes you laugh. 💅",

        "Consider this your official permission slip to slow down. Comfy clothes, your favorite playlist, and absolutely zero guilt. 🎀",

        "Main-character maintenance check: hydrate, breathe, get comfy, and do something small that makes you feel cute and happy. ✨"
    ];

    $hype = $hypeMessages[array_rand($hypeMessages)];


    if ($mood === "Low" || $mood === "Sad") {

        $hype =
            "Today doesn't have to be perfect. Put on something comfy, play a comfort song or show, and stay close to something—or someone—that makes you feel safe. 💗";
    }


    return [
        "overview" => $overview,
        "care" => $careTips,
        "hype" => $hype
    ];
}


/*
|--------------------------------------------------------------------------
| HANDLE FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $symptom_date = date("Y-m-d");

    $symptoms = isset($_POST["symptoms"])
        ? $_POST["symptoms"]
        : [];

    $severity = isset($_POST["severity"])
        ? trim($_POST["severity"])
        : "";

    $mood = isset($_POST["mood"])
        ? trim($_POST["mood"])
        : "";

    $mood_swings = isset($_POST["mood_swings"])
        ? trim($_POST["mood_swings"])
        : "";

    $cravings = isset($_POST["cravings"])
        ? trim($_POST["cravings"])
        : "";

    $bloating = isset($_POST["bloating"])
        ? trim($_POST["bloating"])
        : "";

    $sleep = isset($_POST["sleep"])
        ? trim($_POST["sleep"])
        : "";

    $energy = isset($_POST["energy"])
        ? trim($_POST["energy"])
        : "";

    $pain = isset($_POST["pain"])
        ? trim($_POST["pain"])
        : "";

    $skin = isset($_POST["skin"])
        ? trim($_POST["skin"])
        : "";

    $medication = isset($_POST["medication"])
        ? trim($_POST["medication"])
        : "";


    if (
        empty($severity) ||
        empty($mood) ||
        empty($mood_swings) ||
        empty($cravings) ||
        empty($bloating) ||
        empty($sleep) ||
        empty($energy) ||
        empty($pain) ||
        empty($skin)
    ) {

        $message =
            "Please complete your check-in before saving it. 💗";

        $message_type = "error";

    } else {

        $analysis = generateFemTrackAnalysis(
            $symptoms,
            $severity,
            $mood,
            $mood_swings,
            $cravings,
            $bloating,
            $sleep,
            $energy,
            $pain,
            $skin,
            $medication
        );


        $generatedNote =
            $analysis["overview"] .
            "\n\nCare for yourself:\n• " .
            implode("\n• ", $analysis["care"]) .
            "\n\n" .
            $analysis["hype"];


        $symptomsString = implode(", ", $symptoms);


        /*
        |--------------------------------------------------------------------------
        | INSERT CHECK-IN
        |--------------------------------------------------------------------------
        | Multiple check-ins on the same day ARE allowed.
        */

        $stmt = $conn->prepare("
            INSERT INTO symptom_logs
            (
                user_id,
                symptom_date,
                symptoms,
                severity,
                mood,
                mood_swings,
                cravings,
                bloating,
                sleep,
                energy,
                pain,
                skin,
                medication,
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");


        if ($stmt) {

            $stmt->bind_param(
                "isssssssssssss",
                $user_id,
                $symptom_date,
                $symptomsString,
                $severity,
                $mood,
                $mood_swings,
                $cravings,
                $bloating,
                $sleep,
                $energy,
                $pain,
                $skin,
                $medication,
                $generatedNote
            );


            if ($stmt->execute()) {

                $_SESSION["femtrack_note"] = [
                    "overview" => $analysis["overview"],
                    "care" => $analysis["care"],
                    "hype" => $analysis["hype"],
                    "date" => date("F j, Y"),
                    "time" => date("h:i A")
                ];

                $_SESSION["femtrack_success"] =
                    "Your check-in was saved successfully! 💗";

                header("Location: track-symptoms.php?saved=1");
                exit;

            } else {

                $message =
                    "Something went wrong while saving your check-in.";

                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message =
                "Unable to prepare the check-in. Please try again.";

            $message_type = "error";
        }
    }
}


/*
|--------------------------------------------------------------------------
| SUCCESS MESSAGE
|--------------------------------------------------------------------------
*/

if (isset($_GET["saved"]) && $_GET["saved"] == "1") {

    if (isset($_SESSION["femtrack_success"])) {

        $message = $_SESSION["femtrack_success"];

        $message_type = "success";

        unset($_SESSION["femtrack_success"]);
    }
}


/*
|--------------------------------------------------------------------------
| POPUP ANALYSIS
|--------------------------------------------------------------------------
*/

$showAnalysisPopup = false;

if (
    isset($_GET["saved"]) &&
    $_GET["saved"] == "1" &&
    isset($_SESSION["femtrack_note"])
) {

    $femtrackNote = $_SESSION["femtrack_note"];

    $femtrackOverview = $femtrackNote["overview"];
    $femtrackCare = $femtrackNote["care"];
    $femtrackHype = $femtrackNote["hype"];

    $savedDate = $femtrackNote["date"];
    $savedTime = $femtrackNote["time"];

    $showAnalysisPopup = true;

    unset($_SESSION["femtrack_note"]);
}


/*
|--------------------------------------------------------------------------
| SEVERITY STATISTICS
|--------------------------------------------------------------------------
*/

$severityCounts = [
    "Mild" => 0,
    "Uncomfortable" => 0,
    "Very Uncomfortable" => 0
];

$severityQuery = $conn->prepare("
    SELECT severity, COUNT(*) AS total
    FROM symptom_logs
    WHERE user_id = ?
    GROUP BY severity
");

if ($severityQuery) {

    $severityQuery->bind_param("i", $user_id);

    $severityQuery->execute();

    $result = $severityQuery->get_result();

    while ($row = $result->fetch_assoc()) {

        $severityName = $row["severity"];

        if (isset($severityCounts[$severityName])) {

            $severityCounts[$severityName] =
                (int)$row["total"];
        }
    }

    $severityQuery->close();
}


/*
|--------------------------------------------------------------------------
| TOTAL CHECK-INS
|--------------------------------------------------------------------------
*/

$totalCheckins = 0;

$totalQuery = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM symptom_logs
    WHERE user_id = ?
");

if ($totalQuery) {

    $totalQuery->bind_param("i", $user_id);

    $totalQuery->execute();

    $totalResult = $totalQuery->get_result();

    if ($totalRow = $totalResult->fetch_assoc()) {

        $totalCheckins = (int)$totalRow["total"];
    }

    $totalQuery->close();
}


/*
|--------------------------------------------------------------------------
| RECENT ACTIVITY
|--------------------------------------------------------------------------
*/

$recentCheckins = [];

$recentQuery = $conn->prepare("
    SELECT
        id,
        symptom_date,
        created_at,
        mood,
        severity,
        symptoms,
        energy,
        pain
    FROM symptom_logs
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");

if ($recentQuery) {

    $recentQuery->bind_param("i", $user_id);

    $recentQuery->execute();

    $recentResult = $recentQuery->get_result();

    while ($row = $recentResult->fetch_assoc()) {

        $recentCheckins[] = $row;
    }

    $recentQuery->close();
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

    <title>Track Symptoms | FemTrack</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Belleza&family=Playfair+Display:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<style>

/* =========================================================
   TRACK SYMPTOMS ONLY
   IMPORTANT: NOTHING HERE TARGETS THE NAVBAR
========================================================= */

.track-symptoms-page,
.track-symptoms-page * {
    box-sizing: border-box;
}

.track-symptoms-page {
    font-family: "Belleza", sans-serif;
    color: #3f2a36;
}


/* =========================================================
   CONTAINER
========================================================= */

.track-symptoms-page .page-container {
    max-width: 1250px;
    margin: 0 auto;
    padding: 38px 25px 60px;
}


/* =========================================================
   HEADER
========================================================= */

.track-symptoms-page .page-header {
    margin-bottom: 28px;
}

.track-symptoms-page .page-header h1 {
    margin: 0;
    font-family: "Playfair Display", serif;
    font-size: 38px;
    color: #623a5a;
}

.track-symptoms-page .page-header p {
    margin: 7px 0 0;
    color: #776b82;
    font-size: 16px;
}

.track-symptoms-page .today-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-top: 14px;
    padding: 8px 14px;
    border-radius: 30px;
    background: #f2dce9;
    color: #713e5b;
    font-size: 13px;
}


/* =========================================================
   ALERT
========================================================= */

.track-symptoms-page .alert {
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 22px;
    font-size: 14px;
}

.track-symptoms-page .alert-success {
    background: #e8f6ed;
    color: #277244;
}

.track-symptoms-page .alert-error {
    background: #fde9ee;
    color: #9b3457;
}


/* =========================================================
   MAIN GRID
========================================================= */

.track-symptoms-page .symptom-layout {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 25px;
    align-items: start;
}


/* =========================================================
   CARDS
========================================================= */

.track-symptoms-page .card {
    background: rgba(255,255,255,0.88);
    border: 1px solid rgba(155,93,229,0.10);
    border-radius: 24px;
    padding: 27px;
    box-shadow: 0 15px 45px rgba(105,75,120,0.08);
}

.track-symptoms-page .card-title {
    font-family: "Playfair Display", serif;
    font-size: 23px;
    color: #4f3d60;
    margin-bottom: 5px;
}

.track-symptoms-page .card-subtitle {
    color: #887b91;
    font-size: 14px;
    margin-bottom: 24px;
}


/* =========================================================
   FORM
========================================================= */

.track-symptoms-page .form-section {
    margin-bottom: 25px;
}

.track-symptoms-page .form-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: bold;
    color: #574365;
    margin-bottom: 12px;
}

.track-symptoms-page .form-section-title i {
    color: #9b5de5;
}

.track-symptoms-page .checkbox-grid,
.track-symptoms-page .option-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.track-symptoms-page .option-grid.three {
    grid-template-columns: repeat(3, 1fr);
}

.track-symptoms-page .option {
    position: relative;
}

.track-symptoms-page .option input {
    position: absolute;
    opacity: 0;
}

.track-symptoms-page .option label {
    display: block;
    padding: 12px 13px;
    background: #faf6fb;
    border: 1px solid #eee3f1;
    border-radius: 13px;
    color: #66586e;
    cursor: pointer;
    transition: 0.2s ease;
    font-size: 14px;
}

.track-symptoms-page .option label:hover {
    border-color: #cba9dd;
    transform: translateY(-1px);
}

.track-symptoms-page .option input:checked + label {
    background: #f1dcec;
    border-color: #b879ce;
    color: #633f70;
    font-weight: bold;
}


/* =========================================================
   TEXTAREA
========================================================= */

.track-symptoms-page textarea {
    width: 100%;
    min-height: 95px;
    resize: vertical;
    padding: 13px;
    border-radius: 13px;
    border: 1px solid #e8dce9;
    background: #fcf9fd;
    font-family: inherit;
    color: #45394d;
    outline: none;
}

.track-symptoms-page textarea:focus {
    border-color: #b879ce;
    box-shadow: 0 0 0 3px rgba(184,121,206,0.10);
}


/* =========================================================
   SUBMIT BUTTON
========================================================= */

.track-symptoms-page .submit-btn {
    width: 100%;
    border: none;
    border-radius: 15px;
    padding: 15px;
    background: linear-gradient(
        135deg,
        #b65f91,
        #8055a6
    );
    color: white;
    font-family: inherit;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.25s ease;
}

.track-symptoms-page .submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(128,85,166,0.25);
}


/* =========================================================
   RIGHT COLUMN
========================================================= */

.track-symptoms-page .right-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
}


/* =========================================================
   DONUT CHART
========================================================= */

.track-symptoms-page .chart-wrapper {
    width: 100%;
    max-width: 330px;
    height: 300px;
    margin: 5px auto 0;
    position: relative;
}

.track-symptoms-page .chart-center {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -52%);
    text-align: center;
    pointer-events: none;
}

.track-symptoms-page .chart-center strong {
    display: block;
    font-family: "Playfair Display", serif;
    font-size: 35px;
    color: #5d416d;
}

.track-symptoms-page .chart-center span {
    font-size: 12px;
    color: #8a7b91;
}


/* =========================================================
   LEGEND
========================================================= */

.track-symptoms-page .chart-legend {
    display: flex;
    flex-direction: column;
    gap: 9px;
    margin-top: 12px;
}

.track-symptoms-page .legend-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    color: #66596e;
}

.track-symptoms-page .legend-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.track-symptoms-page .legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.track-symptoms-page .legend-count {
    font-weight: bold;
    color: #564060;
}


/* =========================================================
   RECENT ACTIVITY
========================================================= */

.track-symptoms-page .activity-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.track-symptoms-page .activity-item {
    padding: 13px;
    border-radius: 14px;
    background: #faf7fb;
    border: 1px solid #eee5f0;
}

.track-symptoms-page .activity-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.track-symptoms-page .activity-date {
    font-size: 13px;
    font-weight: bold;
    color: #5d4669;
}

.track-symptoms-page .activity-time {
    font-size: 11px;
    color: #97899d;
}

.track-symptoms-page .activity-details {
    margin-top: 7px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.track-symptoms-page .activity-tag {
    padding: 5px 9px;
    border-radius: 20px;
    background: #eee3f4;
    color: #674b75;
    font-size: 11px;
}

.track-symptoms-page .empty-state {
    text-align: center;
    color: #96899d;
    font-size: 14px;
    padding: 20px 5px;
}


/* =========================================================
   POPUP
========================================================= */

.track-symptoms-page .femtrack-modal {
    position: fixed;
    inset: 0;
    z-index: 99999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 20px;

    background: rgba(43, 28, 50, 0.48);

    backdrop-filter: blur(9px);
    -webkit-backdrop-filter: blur(9px);

    animation: trackSymptomsModalFade 0.25s ease;
}

@keyframes trackSymptomsModalFade {

    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}


.track-symptoms-page .femtrack-modal-card {
    width: min(650px, 100%);
    max-height: 90vh;
    overflow-y: auto;

    background: linear-gradient(
        145deg,
        #fffafd,
        #f8effc
    );

    border-radius: 27px;
    padding: 31px;

    box-shadow:
        0 25px 80px rgba(48,35,57,0.30);

    animation: trackSymptomsPopupUp 0.35s ease;
}

@keyframes trackSymptomsPopupUp {

    from {
        opacity: 0;
        transform: translateY(25px) scale(0.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}


.track-symptoms-page .popup-icon {
    width: 62px;
    height: 62px;

    margin: 0 auto 13px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #f1d9eb;
    color: #9b5280;

    font-size: 25px;
}

.track-symptoms-page .popup-title {
    text-align: center;
    font-family: "Playfair Display", serif;
    font-size: 29px;
    color: #503b60;
    margin: 0;
}

.track-symptoms-page .popup-date {
    text-align: center;
    margin-top: 6px;
    color: #95869b;
    font-size: 12px;
}

.track-symptoms-page .popup-section {
    margin-top: 22px;
}

.track-symptoms-page .popup-section h3 {
    margin: 0 0 8px;
    color: #644770;
    font-size: 16px;
}

.track-symptoms-page .popup-overview {
    color: #64596c;
    line-height: 1.65;
    font-size: 14px;
}

.track-symptoms-page .popup-care {
    padding: 15px 17px;
    border-radius: 16px;
    background: #f5e9f8;
}

.track-symptoms-page .popup-care ul {
    margin: 0;
    padding-left: 20px;
}

.track-symptoms-page .popup-care li {
    margin-bottom: 8px;
    color: #66586e;
    line-height: 1.5;
    font-size: 13px;
}

.track-symptoms-page .popup-hype {
    padding: 15px 17px;
    border-radius: 16px;
    background: #f8e7ef;
    color: #734a60;
    line-height: 1.55;
    font-size: 14px;
}

.track-symptoms-page .popup-close {
    width: 100%;
    margin-top: 23px;

    border: none;
    border-radius: 14px;

    padding: 13px;

    background: #654a72;
    color: white;

    font-family: inherit;
    font-size: 14px;
    font-weight: bold;

    cursor: pointer;
    transition: 0.2s ease;
}

.track-symptoms-page .popup-close:hover {
    background: #513b5d;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .track-symptoms-page .symptom-layout {
        grid-template-columns: 1fr;
    }

    .track-symptoms-page .right-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
}


@media (max-width: 650px) {

    .track-symptoms-page .page-container {
        padding: 25px 15px 45px;
    }

    .track-symptoms-page .page-header h1 {
        font-size: 31px;
    }

    .track-symptoms-page .card {
        padding: 20px;
        border-radius: 20px;
    }

    .track-symptoms-page .checkbox-grid,
    .track-symptoms-page .option-grid,
    .track-symptoms-page .option-grid.three {
        grid-template-columns: 1fr;
    }

    .track-symptoms-page .right-column {
        display: flex;
    }

    .track-symptoms-page .femtrack-modal {
        padding: 12px;
    }

    .track-symptoms-page .femtrack-modal-card {
        padding: 22px;
        border-radius: 22px;
        max-height: 92vh;
    }

    .track-symptoms-page .popup-title {
        font-size: 24px;
    }
}

</style>

</head>


<body>


<?php include "../includes/nav.php"; ?>


<!-- =========================================================
     IMPORTANT:
     EVERYTHING BELOW THE NAV IS INSIDE THIS WRAPPER.
     THE NAV IS NOT AFFECTED BY THIS PAGE'S CSS.
========================================================= -->

<div class="track-symptoms-page">


<div class="page-container">


    <!-- HEADER -->

    <div class="page-header">

        <h1>
            Track Your Symptoms
        </h1>

        <p>
            Check in with your body, mood and energy today.
        </p>

        <div class="today-badge">

            <i class="fa-regular fa-calendar"></i>

            <?php echo date("F j, Y"); ?>

        </div>

    </div>


    <!-- MESSAGE -->

    <?php if (!empty($message)): ?>

        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- MAIN LAYOUT -->

    <div class="symptom-layout">


        <!-- FORM -->

        <div class="card">

            <div class="card-title">
                Today's Check-in
            </div>

            <div class="card-subtitle">
                You can check in multiple times throughout the same day.
                Your entries will all be saved separately. 💗
            </div>


            <form method="POST" action="">


                <!-- SYMPTOMS -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-heart-pulse"></i>

                        Physical Symptoms

                    </div>


                    <div class="checkbox-grid">

                        <?php

                        $symptomOptions = [
                            "Cramps",
                            "Headache",
                            "Back Pain",
                            "Nausea",
                            "Breast Tenderness",
                            "Fatigue"
                        ];

                        foreach ($symptomOptions as $symptom):

                        ?>

                            <div class="option">

                                <input
                                    type="checkbox"
                                    name="symptoms[]"
                                    value="<?php echo htmlspecialchars($symptom); ?>"
                                    id="symptom_<?php echo md5($symptom); ?>"
                                >

                                <label
                                    for="symptom_<?php echo md5($symptom); ?>"
                                >
                                    <?php echo htmlspecialchars($symptom); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- SEVERITY -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-gauge-high"></i>

                        Overall Severity

                    </div>


                    <div class="option-grid three">

                        <?php

                        $severityOptions = [
                            "Mild",
                            "Uncomfortable",
                            "Very Uncomfortable"
                        ];

                        foreach ($severityOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="severity"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="severity_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="severity_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- MOOD -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-regular fa-face-smile"></i>

                        Mood

                    </div>


                    <div class="option-grid">

                        <?php

                        $moodOptions = [
                            "Happy",
                            "Calm",
                            "Good",
                            "Sad",
                            "Irritated",
                            "Anxious",
                            "Stressed",
                            "Low"
                        ];

                        foreach ($moodOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="mood"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="mood_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="mood_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- MOOD SWINGS -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-wave-square"></i>

                        Mood Swings

                    </div>


                    <div class="option-grid">

                        <div class="option">

                            <input
                                type="radio"
                                name="mood_swings"
                                value="Yes"
                                id="mood_yes"
                                required
                            >

                            <label for="mood_yes">
                                Yes
                            </label>

                        </div>


                        <div class="option">

                            <input
                                type="radio"
                                name="mood_swings"
                                value="No"
                                id="mood_no"
                                required
                            >

                            <label for="mood_no">
                                No
                            </label>

                        </div>

                    </div>

                </div>


                <!-- CRAVINGS -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-cookie-bite"></i>

                        Cravings

                    </div>


                    <div class="option-grid">

                        <?php

                        $cravingOptions = [
                            "None",
                            "Sweet",
                            "Salty",
                            "Carbs",
                            "Other"
                        ];

                        foreach ($cravingOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="cravings"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="craving_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="craving_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- BLOATING -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-circle-dot"></i>

                        Bloating

                    </div>


                    <div class="option-grid">

                        <?php

                        $bloatingOptions = [
                            "None",
                            "Mild",
                            "Moderate",
                            "Severe"
                        ];

                        foreach ($bloatingOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="bloating"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="bloating_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="bloating_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- SLEEP -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-regular fa-moon"></i>

                        Sleep

                    </div>


                    <div class="option-grid">

                        <?php

                        $sleepOptions = [
                            "Good",
                            "7–8 hours",
                            "5–6 hours",
                            "Less than 5 hours",
                            "Poor"
                        ];

                        foreach ($sleepOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="sleep"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="sleep_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="sleep_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- ENERGY -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-bolt"></i>

                        Energy

                    </div>


                    <div class="option-grid">

                        <?php

                        $energyOptions = [
                            "High",
                            "Normal",
                            "Low",
                            "Very Low"
                        ];

                        foreach ($energyOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="energy"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="energy_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="energy_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- PAIN -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-hand-holding-heart"></i>

                        Pain Level

                    </div>


                    <div class="option-grid">

                        <?php

                        $painOptions = [
                            "None",
                            "Mild",
                            "Moderate",
                            "Severe"
                        ];

                        foreach ($painOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="pain"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="pain_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="pain_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- SKIN -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-wand-magic-sparkles"></i>

                        Skin

                    </div>


                    <div class="option-grid">

                        <?php

                        $skinOptions = [
                            "None",
                            "Breakouts",
                            "Oily",
                            "Dry"
                        ];

                        foreach ($skinOptions as $option):

                        ?>

                            <div class="option">

                                <input
                                    type="radio"
                                    name="skin"
                                    value="<?php echo htmlspecialchars($option); ?>"
                                    id="skin_<?php echo md5($option); ?>"
                                    required
                                >

                                <label
                                    for="skin_<?php echo md5($option); ?>"
                                >
                                    <?php echo htmlspecialchars($option); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- MEDICATION -->

                <div class="form-section">

                    <div class="form-section-title">

                        <i class="fa-solid fa-pills"></i>

                        Medication

                        <span
                            style="
                                font-size:11px;
                                font-weight:normal;
                                color:#9a8d9f;
                            "
                        >
                            Optional
                        </span>

                    </div>


                    <textarea
                        name="medication"
                        placeholder="Anything you took today? You can leave this blank."
                    ></textarea>

                </div>


                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="submit-btn"
                >

                    <i class="fa-solid fa-heart"></i>

                    Save Check-in

                </button>


            </form>

        </div>


        <!-- RIGHT COLUMN -->

        <div class="right-column">


            <!-- DONUT CHART -->

            <div class="card">

                <div class="card-title">
                    Check-in Overview
                </div>

                <div class="card-subtitle">
                    Your symptom severity history
                </div>


                <div class="chart-wrapper">

                    <canvas id="severityChart"></canvas>

                    <div class="chart-center">

                        <strong>
                            <?php echo $totalCheckins; ?>
                        </strong>

                        <span>
                            Check-ins
                        </span>

                    </div>

                </div>


                <div class="chart-legend">

                    <div class="legend-item">

                        <div class="legend-left">

                            <span
                                class="legend-dot"
                                style="background:#E45D91;"
                            ></span>

                            Mild

                        </div>

                        <span class="legend-count">

                            <?php echo $severityCounts["Mild"]; ?>

                        </span>

                    </div>


                    <div class="legend-item">

                        <div class="legend-left">

                            <span
                                class="legend-dot"
                                style="background:#8B5FBF;"
                            ></span>

                            Uncomfortable

                        </div>

                        <span class="legend-count">

                            <?php echo $severityCounts["Uncomfortable"]; ?>

                        </span>

                    </div>


                    <div class="legend-item">

                        <div class="legend-left">

                            <span
                                class="legend-dot"
                                style="background:#5B4B9A;"
                            ></span>

                            Very Uncomfortable

                        </div>

                        <span class="legend-count">

                            <?php echo $severityCounts["Very Uncomfortable"]; ?>

                        </span>

                    </div>

                </div>

            </div>


            <!-- RECENT ACTIVITY -->

            <div class="card">

                <div class="card-title">
                    Recent Activity
                </div>

                <div class="card-subtitle">
                    Your latest symptom check-ins
                </div>


                <div class="activity-list">

                    <?php if (empty($recentCheckins)): ?>

                        <div class="empty-state">

                            No check-ins yet. Your first one will appear here. 💗

                        </div>

                    <?php else: ?>


                        <?php foreach ($recentCheckins as $activity): ?>

                            <div class="activity-item">

                                <div class="activity-top">

                                    <div class="activity-date">

                                        <?php

                                        echo date(
                                            "M j, Y",
                                            strtotime($activity["symptom_date"])
                                        );

                                        ?>

                                    </div>


                                    <div class="activity-time">

                                        <?php

                                        if (!empty($activity["created_at"])) {

                                            echo date(
                                                "h:i A",
                                                strtotime($activity["created_at"])
                                            );
                                        }

                                        ?>

                                    </div>

                                </div>


                                <div class="activity-details">


                                    <?php if (!empty($activity["mood"])): ?>

                                        <span class="activity-tag">

                                            Mood:
                                            <?php
                                            echo htmlspecialchars(
                                                $activity["mood"]
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>


                                    <?php if (!empty($activity["severity"])): ?>

                                        <span class="activity-tag">

                                            <?php
                                            echo htmlspecialchars(
                                                $activity["severity"]
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>


                                    <?php if (!empty($activity["energy"])): ?>

                                        <span class="activity-tag">

                                            Energy:
                                            <?php
                                            echo htmlspecialchars(
                                                $activity["energy"]
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>


                                    <?php if (!empty($activity["pain"])): ?>

                                        <span class="activity-tag">

                                            Pain:
                                            <?php
                                            echo htmlspecialchars(
                                                $activity["pain"]
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>


                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- FEMTRACK POPUP -->

<?php if ($showAnalysisPopup === true): ?>

    <div
        class="femtrack-modal"
        id="femtrackModal"
    >

        <div class="femtrack-modal-card">


            <div class="popup-icon">

                <i class="fa-solid fa-heart"></i>

            </div>


            <h2 class="popup-title">
                Your FemTrack Note 💗
            </h2>


            <div class="popup-date">

                Check-in saved on
                <?php echo htmlspecialchars($savedDate); ?>

                ·

                <?php echo htmlspecialchars($savedTime); ?>

            </div>


            <div class="popup-section">

                <h3>
                    <i class="fa-solid fa-sparkles"></i>
                    What your check-in says
                </h3>

                <div class="popup-overview">

                    <?php

                    echo nl2br(
                        htmlspecialchars($femtrackOverview)
                    );

                    ?>

                </div>

            </div>


            <?php if (!empty($femtrackCare)): ?>

                <div class="popup-section">

                    <h3>
                        <i class="fa-solid fa-heart-circle-check"></i>
                        Little ways to care for yourself
                    </h3>


                    <div class="popup-care">

                        <ul>

                            <?php foreach ($femtrackCare as $tip): ?>

                                <li>

                                    <?php
                                    echo htmlspecialchars($tip);
                                    ?>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </div>

                </div>

            <?php endif; ?>


            <div class="popup-section">

                <h3>
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    FemTrack Hype
                </h3>


                <div class="popup-hype">

                    <?php
                    echo htmlspecialchars($femtrackHype);
                    ?>

                </div>

            </div>


            <button
                type="button"
                class="popup-close"
                onclick="closeFemTrackNote()"
            >

                Got it 💗

            </button>


        </div>

    </div>

<?php endif; ?>


</div><!-- END .track-symptoms-page -->


<script>

/* =========================================================
   SEVERITY DONUT CHART
========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const chartCanvas =
        document.getElementById("severityChart");


    if (chartCanvas) {

        new Chart(chartCanvas, {

            type: "doughnut",

            data: {

                labels: [
                    "Mild",
                    "Uncomfortable",
                    "Very Uncomfortable"
                ],

                datasets: [{

                    data: [
                        <?php echo $severityCounts["Mild"]; ?>,
                        <?php echo $severityCounts["Uncomfortable"]; ?>,
                        <?php echo $severityCounts["Very Uncomfortable"]; ?>
                    ],

                    backgroundColor: [
                        "#E45D91",
                        "#8B5FBF",
                        "#5B4B9A"
                    ],

                    borderWidth: 0,

                    hoverOffset: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: "72%",

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                return (
                                    " " +
                                    context.label +
                                    ": " +
                                    context.raw
                                );

                            }

                        }

                    }

                }

            }

        });

    }


    /* =====================================================
       OPEN POPUP
    ====================================================== */

    const modal =
        document.getElementById("femtrackModal");


    if (modal) {

        document.body.style.overflow = "hidden";

    }


    /* =====================================================
       ESCAPE KEY
    ====================================================== */

    document.addEventListener("keydown", function(event) {

        if (event.key === "Escape") {

            closeFemTrackNote();

        }

    });

});


/* =========================================================
   CLOSE POPUP
========================================================= */

function closeFemTrackNote() {

    const modal =
        document.getElementById("femtrackModal");


    if (!modal) {
        return;
    }


    modal.style.opacity = "0";


    setTimeout(function() {

        modal.remove();

        document.body.style.overflow = "";

    }, 200);

}

</script>


</body>

</html>

