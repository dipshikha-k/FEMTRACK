<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yoga & Exercise - FemTrack</title>
<link rel="stylesheet" href="../css/style.css">

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    background: #fff9fb;
    color: #54243d;
    font-family: Arial, Helvetica, sans-serif;
}

/* =========================
   HERO — same look as reference
========================= */
.yoga-hero {
    height: 187px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background:
        radial-gradient(ellipse at 10% 90%, #f6d0df 0 16%, transparent 17%),
        radial-gradient(ellipse at 84% 100%, #f6d8e3 0 17%, transparent 18%),
        linear-gradient(90deg, #f9e0e9 0%, #fff7fa 48%, #fffafa 73%, #f8e6ed 100%);
}

.yoga-hero::before {
    content: "";
    position: absolute;
    width: 420px;
    height: 150px;
    left: -105px;
    bottom: -92px;
    border-radius: 50%;
    background: #f5c9db;
    opacity: .55;
}

.hero-copy {
    position: relative;
    z-index: 3;
    width: 650px;
    margin-right: 145px;
    margin-top: -2px;
}

.eyebrow {
    color: #d35b8d;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.2px;
    margin-bottom: 7px;
}

.hero-copy h1 {
    margin: 0 0 8px;
    color: #54203f;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 43px;
    line-height: 1.05;
}

.hero-copy p {
    margin: 0 auto;
    max-width: 650px;
    color: #79566b;
    font-size: 15px;
    line-height: 1.5;
}

.hero-photo {
    position: absolute;
    z-index: 2;
    right: 0;
    top: 0;
    width: 390px;
    height: 187px;
    overflow: hidden;
    opacity: .97;
}

.hero-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* =========================
   MAIN
========================= */
.yoga-main {
    width: 96%;
    max-width: 1600px;
    margin: 16px auto 29px;
}

.exercise-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 13px;
}

/* =========================
   EXACT CARD STRUCTURE
   image left + content right
========================= */
.exercise-card {
    height: 280px;
    background: #fff;
    border: 1px solid #f0dce6;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(108, 46, 78, .055);
    overflow: hidden;
    display: grid;
    grid-template-columns: 37% 63%;
}

.card-media {
    padding: 13px 8px 12px 17px;
    display: flex;
    flex-direction: column;
}

.exercise-image {
    width: 100%;
    height: 187px;
    border-radius: 11px;
    overflow: hidden;
    background: #f8e8ef;
}

.exercise-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.how-button {
    border: 0;
    cursor: pointer;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-self: flex-start;
    margin: auto 0 0 2px;
    padding: 8px 14px;
    border-radius: 20px;
    background: #742952;
    color: #fff;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05px;
}

.card-content {
    padding: 14px 18px 10px 13px;
    min-width: 0;
}

.card-content h2 {
    margin: 0 0 9px;
    color: #602343;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 18px;
    line-height: 1.2;
    font-weight: 700;
}

.exercise-info {
    display: flex;
    gap: 8px;
    margin-bottom: 9px;
}

.info {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 9px;
    border-radius: 15px;
    background: #fff0f6;
    color: #a53f70;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

.card-description {
    margin: 0 0 9px;
    color: #73596a;
    font-size: 11.5px;
    line-height: 1.45;
}

.why-box {
    margin-bottom: 8px;
    padding: 7px 9px;
    border-radius: 7px;
    border-left: 2px solid #e16b9b;
    background: #fff2f7;
}

.why-box strong,
.how-to strong {
    display: block;
    margin-bottom: 3px;
    color: #6e2a4d;
    font-size: 11px;
}

.why-box p,
.how-to p {
    margin: 0;
    color: #73596a;
    font-size: 10.5px;
    line-height: 1.4;
}

.how-to {
    margin-top: 0;
}

/* =========================
   BOTTOM STRIP
========================= */
.body-reminder {
    min-height: 83px;
    margin-top: 15px;
    padding: 13px 25px;
    border-radius: 14px;
    background: linear-gradient(100deg, #fdeaf2, #fff7fa);
    border: 1px solid #f2dce6;
    display: grid;
    grid-template-columns: 320px 1fr 510px;
    align-items: center;
    gap: 20px;
}

.reminder-title {
    display: flex;
    align-items: center;
    gap: 13px;
    height: 55px;
    padding-right: 20px;
    border-right: 1px solid #efcbdc;
}

.heart {
    width: 43px;
    height: 43px;
    min-width: 43px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #fbd8e7;
    color: #c83e7d;
    font-size: 27px;
}

.reminder-title h2 {
    margin: 0;
    color: #57203f;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 22px;
    font-style: italic;
    white-space: nowrap;
}

.reminder-text {
    color: #7d6070;
    font-size: 10.5px;
    line-height: 1.5;
}

.reminder-items {
    height: 55px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
}

.reminder-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding-left: 18px;
    border-left: 1px solid #efcbdc;
}

.reminder-icon {
    width: 37px;
    height: 37px;
    min-width: 37px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #fbd8e7;
    color: #a83c70;
    font-size: 18px;
}

.reminder-item strong {
    display: block;
    color: #6a294c;
    font-size: 11px;
    margin-bottom: 2px;
}

.reminder-item span {
    color: #927383;
    font-size: 9px;
}


.pose-modal{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(71,25,52,.42);backdrop-filter:blur(5px)}
.pose-modal.show{display:flex}.pose-modal-box{width:min(600px,100%);max-height:90vh;overflow-y:auto;background:#fff;border:1px solid #f1d8e4;border-radius:25px;box-shadow:0 25px 70px rgba(72,28,53,.24);padding:30px;position:relative}.pose-modal-close{position:absolute;right:17px;top:15px;width:36px;height:36px;border:0;border-radius:50%;background:#fff0f6;color:#70274f;font-size:22px;cursor:pointer}.modal-eyebrow{color:#c45182;font-size:12px;letter-spacing:1.4px;font-weight:800;margin-bottom:8px}.pose-modal h2{margin:0 45px 8px 0;color:#55203c;font-family:Georgia,serif;font-size:30px}.modal-time{display:inline-block;background:#fff0f6;color:#8f315f;border:1px solid #f1cada;border-radius:18px;padding:7px 12px;font-size:12px;font-weight:800;margin-bottom:16px}.modal-section{margin-top:15px}.modal-section h3{margin:0 0 7px;color:#5d2343;font-size:15px}.modal-section p,.modal-section ol{margin:0;color:#4f3b46;font-size:14px;line-height:1.55}.modal-section p{max-width:540px}.modal-section ol{padding-left:23px}.modal-section li{margin-bottom:6px}.timer-box{margin-top:20px;padding:22px;border-radius:19px;background:linear-gradient(135deg,#fff0f6,#fbdfea);border:1px solid #edc7d8;text-align:center}.timer-label{color:#652746;font-size:14px;font-weight:800;margin-bottom:4px}.timer-display{color:#4e1e39;font-family:Georgia,serif;font-size:48px;font-weight:700;letter-spacing:1px;margin:3px 0 14px}.timer-actions{display:flex;justify-content:center;gap:10px}.timer-btn{border:0;border-radius:20px;padding:10px 20px;background:#742952;color:#fff;font-size:13px;font-weight:800;cursor:pointer}.timer-btn.secondary{background:#fff;color:#742952;border:1px solid #dfb7ca}.modal-note{margin-top:12px;color:#654b58;font-size:12px;line-height:1.45;text-align:center}
/* =========================
   RESPONSIVE
========================= */
@media (max-width: 1200px) {
    .exercise-grid { grid-template-columns: repeat(2, 1fr); }
    .body-reminder { grid-template-columns: 270px 1fr; }
    .reminder-items { grid-column: 1 / -1; }
    .hero-copy { margin-right: 70px; }
}

@media (max-width: 760px) {
    .yoga-hero {
        height: auto;
        min-height: 205px;
        padding: 30px 20px;
    }

    .hero-copy {
        width: 100%;
        margin: 0;
    }

    .hero-copy h1 { font-size: 34px; }
    .hero-copy p { font-size: 13px; }
    .hero-photo { display: none; }

    .exercise-grid {
        grid-template-columns: 1fr;
    }

    .exercise-card { height: auto; min-height: 280px; }

    .body-reminder {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 14px;
    }

    .reminder-title {
        justify-content: center;
        border-right: 0;
        border-bottom: 1px solid #efcbdc;
        padding: 0 0 12px;
    }

    .reminder-items {
        width: 100%;
    }

    .reminder-item {
        justify-content: center;
    }
}

@media (max-width: 500px) {
    .exercise-card {
        grid-template-columns: 1fr;
        height: auto;
    }

    .card-media {
        padding: 12px;
    }

    .exercise-image {
        height: 200px;
    }

    .how-button {
        margin-top: 10px;
    }

    .card-content {
        padding: 0 15px 18px;
    }

    .reminder-items {
        grid-template-columns: 1fr;
        height: auto;
        gap: 8px;
    }

    .reminder-item {
        border-left: 0;
        border-top: 1px solid #efcbdc;
        padding: 9px 0 0;
    }
}
</style>
</head>

<body>

<?php include "../includes/nav.php"; ?>

<section class="yoga-hero">
    <div class="hero-copy">
        <div class="eyebrow">♡ MOVE • BREATHE • CARE</div>
        <h1>Yoga &amp; Exercise</h1>
        <p>
            Gentle movement and relaxation can be a simple way to care for your body and<br>
            support your well-being throughout your cycle.
        </p>
    </div>

    <div class="hero-photo">
        <img src="data:image/jpeg;base64,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" alt="Yoga and relaxation">
    </div>
</section>

<main class="yoga-main">

<section class="exercise-grid">

    <!-- 1 -->
    <article class="exercise-card">
        <div class="card-media">
            <div class="exercise-image">
                <img src="data:image/jpeg;base64,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" alt="Cat-Cow Stretch">
            </div>
            <button type="button" class="how-button" data-pose="cat-cow">How to do it&nbsp; →</button>
        </div>

        <div class="card-content">
            <h2>Cat-Cow Stretch</h2>
            <div class="exercise-info">
                <span class="info">◷ &nbsp;5–10 min</span>
                <span class="info">♙ &nbsp;Beginner</span>
            </div>
            <p class="card-description">
                A gentle movement that alternates between arching and rounding the spine.
            </p>
            <div class="why-box">
                <strong>Why it may help</strong>
                <p>It can release tension in the back and abdominal area, improve flexibility and encourage relaxation.</p>
            </div>
            <div class="how-to">
                <strong>How to do it</strong>
                <p>Start on your hands and knees. Slowly arch your back while breathing in, then round your back while breathing out.</p>
            </div>
        </div>
    </article>

    <!-- 2 -->
    <article class="exercise-card">
        <div class="card-media">
            <div class="exercise-image">
                <img src="data:image/jpeg;base64,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" alt="Child's Pose">
            </div>
            <button type="button" class="how-button" data-pose="child-pose">How to do it&nbsp; →</button>
        </div>

        <div class="card-content">
            <h2>Child's Pose</h2>
            <div class="exercise-info">
                <span class="info">◷ &nbsp;5 min</span>
                <span class="info">♙ &nbsp;Beginner</span>
            </div>
            <p class="card-description">
                A relaxing position that encourages slow breathing and rest.
            </p>
            <div class="why-box">
                <strong>Why it may help</strong>
                <p>Gently stretches the lower back and hips, releases tension and promotes calmness.</p>
            </div>
            <div class="how-to">
                <strong>How to do it</strong>
                <p>Kneel comfortably, sit back toward your heels and slowly fold your upper body forward.</p>
            </div>
        </div>
    </article>

    <!-- 3 -->
    <article class="exercise-card">
        <div class="card-media">
            <div class="exercise-image">
                <img src="data:image/jpeg;base64,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" alt="Butterfly Pose">
            </div>
            <button type="button" class="how-button" data-pose="butterfly">How to do it&nbsp; →</button>
        </div>

        <div class="card-content">
            <h2>Butterfly Pose</h2>
            <div class="exercise-info">
                <span class="info">◷ &nbsp;5–10 min</span>
                <span class="info">♙ &nbsp;Beginner</span>
            </div>
            <p class="card-description">
                A seated stretch that opens the hips and encourages relaxed breathing.
            </p>
            <div class="why-box">
                <strong>Why it may help</strong>
                <p>Stretches the inner thighs and hips, improves circulation and can ease pelvic tension.</p>
            </div>
            <div class="how-to">
                <strong>How to do it</strong>
                <p>Sit comfortably, bring the soles of your feet together and gently let your knees fall to the sides.</p>
            </div>
        </div>
    </article>

    <!-- 4 -->
    <article class="exercise-card">
        <div class="card-media">
            <div class="exercise-image">
                <img src="data:image/jpeg;base64,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" alt="Legs-Up-the-Wall">
            </div>
            <button type="button" class="how-button" data-pose="legs-wall">How to do it&nbsp; →</button>
        </div>

        <div class="card-content">
            <h2>Legs-Up-the-Wall</h2>
            <div class="exercise-info">
                <span class="info">◷ &nbsp;5–10 min</span>
                <span class="info">♙ &nbsp;Easy</span>
            </div>
            <p class="card-description">
                A restorative position that allows your body to rest while you breathe slowly.
            </p>
            <div class="why-box">
                <strong>Why it may help</strong>
                <p>Encourages relaxation, reduces tiredness and can ease pressure in the lower body.</p>
            </div>
            <div class="how-to">
                <strong>How to do it</strong>
                <p>Lie on your back near a wall, extend your legs upward and keep your body comfortable. Breathe slowly.</p>
            </div>
        </div>
    </article>

    <!-- 5 -->
    <article class="exercise-card">
        <div class="card-media">
            <div class="exercise-image">
                <img src="data:image/jpeg;base64,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" alt="Reclining Bound Angle Pose">
            </div>
            <button type="button" class="how-button" data-pose="reclining">How to do it&nbsp; →</button>
        </div>

        <div class="card-content">
            <h2>Reclining Bound Angle Pose</h2>
            <div class="exercise-info">
                <span class="info">◷ &nbsp;5–10 min</span>
                <span class="info">♙ &nbsp;Beginner</span>
            </div>
            <p class="card-description">
                A gentle pose that opens the hips and calms the mind.
            </p>
            <div class="why-box">
                <strong>Why it may help</strong>
                <p>Relieves tension in the hips and lower back, and supports a relaxed resting position.</p>
            </div>
            <div class="how-to">
                <strong>How to do it</strong>
                <p>Lie on your back, bring the soles of your feet together, let your knees fall to the sides and breathe deeply.</p>
            </div>
        </div>
    </article>

    <!-- 6 -->
    <article class="exercise-card">
        <div class="card-media">
            <div class="exercise-image">
                <img src="data:image/jpeg;base64,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" alt="Seated Forward Bend">
            </div>
            <button type="button" class="how-button" data-pose="forward-bend">How to do it&nbsp; →</button>
        </div>

        <div class="card-content">
            <h2>Seated Forward Bend</h2>
            <div class="exercise-info">
                <span class="info">◷ &nbsp;5–10 min</span>
                <span class="info">♙ &nbsp;Beginner</span>
            </div>
            <p class="card-description">
                A gentle stretch that relaxes the back and hamstrings.
            </p>
            <div class="why-box">
                <strong>Why it may help</strong>
                <p>Eases lower back tension, calms the nervous system and can reduce period pain.</p>
            </div>
            <div class="how-to">
                <strong>How to do it</strong>
                <p>Sit with your legs extended, hinge forward from the hips and reach toward your feet. Breathe slowly.</p>
            </div>
        </div>
    </article>

</section>

<section class="body-reminder">
    <div class="reminder-title">
        <div class="heart">♡</div>
        <h2>Listen to your body</h2>
    </div>

    <div class="reminder-text">
        Choose an activity according to how you feel. Keep your movements gentle,
        stay hydrated and stop if an exercise causes pain or makes you feel unwell.<br>
        These activities are intended for gentle movement and relaxation, not as a replacement for medical care.
    </div>

    <div class="reminder-items">
        <div class="reminder-item">
            <div class="reminder-icon">♧</div>
            <div><strong>Relax</strong><span>Calm your mind</span></div>
        </div>
        <div class="reminder-item">
            <div class="reminder-icon">♡</div>
            <div><strong>Stretch</strong><span>Ease your body</span></div>
        </div>
        <div class="reminder-item">
            <div class="reminder-icon">≋</div>
            <div><strong>Breathe</strong><span>Find your balance</span></div>
        </div>
    </div>
</section>


<div class="pose-modal" id="poseModal" aria-hidden="true"><div class="pose-modal-box" role="dialog" aria-modal="true"><button type="button" class="pose-modal-close" id="closePoseModal">×</button><div class="modal-eyebrow">GENTLE MOVEMENT GUIDE</div><h2 id="modalPoseTitle">Pose</h2><div class="modal-time" id="modalTime">5 min</div><div class="modal-section"><h3>Why it may help</h3><p id="modalWhy"></p></div><div class="modal-section"><h3>How to do it</h3><ol id="modalSteps"></ol></div><div class="timer-box"><div class="timer-label">5-MINUTE GENTLE TIMER</div><div class="timer-display" id="timerDisplay">05:00</div><div class="timer-actions"><button type="button" class="timer-btn" id="startTimer">Start</button><button type="button" class="timer-btn secondary" id="resetTimer">Reset</button></div></div><div class="modal-note">Stop if you feel pain or discomfort.</div></div></div>
</main>
<script>const poseData={"cat-cow":{title:"Cat-Cow Stretch",time:"5–10 min",why:"Gentle spinal movement may help release tension in the back while encouraging relaxed breathing.",steps:["Start on your hands and knees with your back comfortable.","Breathe in and gently lift your chest while arching your back.","Breathe out and slowly round your back.","Repeat slowly and keep the movement comfortable."]},"child-pose":{title:"Child's Pose",time:"5 min",why:"A restful position gently stretches the lower back and hips and gives the body a quiet moment to relax.",steps:["Kneel comfortably and sit back toward your heels.","Slowly fold your upper body forward.","Rest your arms wherever they feel comfortable.","Breathe slowly and stay relaxed."]},"butterfly":{title:"Butterfly Pose",time:"5–10 min",why:"This seated stretch gently works around the inner thighs and hips without requiring strong movement.",steps:["Sit comfortably with your back relaxed.","Bring the soles of your feet together.","Let your knees move outward naturally.","Breathe slowly and do not force the stretch."]},"legs-wall":{title:"Legs-Up-the-Wall",time:"5–10 min",why:"A restorative resting position can be useful when you want a quiet, low-effort movement break.",steps:["Lie on your back near a wall.","Gently extend your legs upward against or near the wall.","Let your shoulders and arms relax.","Breathe slowly and remain comfortable."]},"reclining":{title:"Reclining Bound Angle Pose",time:"5–10 min",why:"A calm resting pose gently opens the hips while giving you time to breathe and relax.",steps:["Lie comfortably on your back.","Bring the soles of your feet together.","Allow your knees to fall outward naturally.","Keep your breathing slow and comfortable."]},"forward-bend":{title:"Seated Forward Bend",time:"5–10 min",why:"A gentle forward stretch can relax the back of the legs and lower back when performed without forcing the body.",steps:["Sit with your legs extended comfortably.","Keep your back relaxed and hinge forward from your hips.","Reach toward your legs or feet only as far as comfortable.","Breathe slowly and avoid bouncing."]}};const modal=document.getElementById('poseModal'),close=document.getElementById('closePoseModal'),title=document.getElementById('modalPoseTitle'),time=document.getElementById('modalTime'),why=document.getElementById('modalWhy'),steps=document.getElementById('modalSteps'),display=document.getElementById('timerDisplay'),start=document.getElementById('startTimer'),reset=document.getElementById('resetTimer');let interval=null,remaining=300,current=null;function showTime(s){display.textContent=String(Math.floor(s/60)).padStart(2,'0')+':'+String(s%60).padStart(2,'0')}function stop(){clearInterval(interval);interval=null;start.textContent='Start'}function openPose(k){const p=poseData[k];if(!p)return;current=p;remaining=300;title.textContent=p.title;time.textContent=p.time;why.textContent=p.why;steps.innerHTML=p.steps.map(x=>'<li>'+x+'</li>').join('');showTime(remaining);stop();modal.classList.add('show');modal.setAttribute('aria-hidden','false');document.body.style.overflow='hidden'}function closePose(){stop();modal.classList.remove('show');modal.setAttribute('aria-hidden','true');document.body.style.overflow=''}document.querySelectorAll('.how-button').forEach(b=>b.addEventListener('click',()=>openPose(b.dataset.pose)));close.addEventListener('click',closePose);modal.addEventListener('click',e=>{if(e.target===modal)closePose()});document.addEventListener('keydown',e=>{if(e.key==='Escape')closePose()});start.addEventListener('click',()=>{if(interval){stop();return}start.textContent='Pause';interval=setInterval(()=>{remaining--;showTime(remaining);if(remaining<=0){stop();start.textContent='Finished ✓'}},1000)});reset.addEventListener('click',()=>{stop();remaining=300;showTime(remaining)});</script>
</body>
</html>
