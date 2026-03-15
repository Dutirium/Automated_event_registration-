
<?php
session_start();

if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYSTEM_EVENT_REGISTRATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=JetBrains+Mono:wght@300;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-cyan: #00f3ff;
            --neon-pink: #ff00ff;
            --bg-black: #050505;
            --card-bg: rgba(15, 15, 15, 0.9);
            --font-main: 'JetBrains Mono', monospace;
            --font-header: 'Orbitron', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0; 
            padding: 20px 15px 140px 15px; /* Adjusted for mobile breathing room */
            font-family: var(--font-main);
            background-color: var(--bg-black);
            color: #eee;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Background Effects */
        .grid-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0, 243, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 243, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px; z-index: -1;
        }

        .scanlines {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.05) 50%);
            background-size: 100% 2px; pointer-events: none; z-index: 10;
        }

        form { max-width: 1000px; margin: 0 auto; position: relative; z-index: 2; }

        .terminal-header {
            text-align: center; margin-bottom: 30px;
            border: 1px solid rgba(255, 0, 255, 0.1);
            padding: 15px; background: rgba(0,0,0,0.5);
        }

        .terminal-header h1 { 
            font-size: 1.2rem; 
            margin: 0 0 10px 0;
            letter-spacing: 2px;
        }

        .typewriter {
            font-size: 0.7rem; color: var(--neon-cyan);
            overflow: hidden; white-space: nowrap;
            border-right: 2px solid var(--neon-cyan);
            width: 0; animation: typing 4s steps(40, end) forwards, blink 0.8s infinite;
        }

        .section-title {
            font-family: var(--font-header);
            font-size: 1rem;
            margin: 30px 0 15px 0;
            color: var(--neon-cyan);
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: ""; width: 20px; height: 2px;
            background: var(--neon-pink);
            box-shadow: 0 0 8px var(--neon-pink);
        }

        .options {
            display: grid;
            /* Adaptive grid: 1 column on tiny screens, 2 on phablets, 3+ on desktop */
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        label { cursor: pointer; -webkit-tap-highlight-color: transparent; }
        input[type="checkbox"] { display: none; }

        .option-box { 
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 60px;
            padding: 10px; 
            background: var(--card-bg);
            border: 1px solid rgba(0, 243, 255, 0.2);
            color: #888;
            font-size: 0.75rem;
            transition: 0.2s ease;
            /* Modified clip-path for better mobile text containment */
            clip-path: polygon(0 0, 92% 0, 100% 25%, 100% 100%, 8% 100%, 0 75%);
        }

        input[type="checkbox"]:checked + .option-box { 
            background: var(--neon-cyan);
            color: #000;
            font-weight: bold;
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.3);
            border-color: var(--neon-cyan);
        }

        /* Floating Total Bar - Mobile Optimized */
        .total-bar {
            position: fixed;
            bottom: 0; left: 0; width: 100%;
            background: rgba(5, 5, 5, 0.95);
            border-top: 2px solid var(--neon-pink);
            padding: 15px 10px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .total-display {
            font-family: var(--font-header);
            font-size: 0.9rem;
            color: var(--neon-pink);
            margin-left: 10px;
        }

        .submit-btn {
            background: transparent;
            color: var(--neon-cyan);
            border: 1px solid var(--neon-cyan);
            padding: 12px 20px;
            font-family: var(--font-header);
            font-size: 0.8rem;
            cursor: pointer;
            clip-path: polygon(10% 0, 100% 0, 100% 70%, 90% 100%, 0 100%, 0 30%);
            transition: 0.3s;
            margin-right: 10px;
        }

        /* Adjustments for desktop */
        @media (min-width: 768px) {
            .options { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
            .section-title { font-size: 1.4rem; }
            .terminal-header h1 { font-size: 2rem; }
            .total-display { font-size: 1.2rem; }
            .submit-btn { padding: 10px 30px; font-size: 1rem; }
            .typewriter { font-size: 0.8rem; }
        }

        @keyframes typing { from { width: 0 } to { width: 100% } }
        @keyframes blink { 50% { border-color: transparent } }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanlines"></div>

<div class="terminal-header">
    <h1 style="font-family: var(--font-header); color: var(--neon-pink);">EVENT_DATABASE_V2.0</h1>
    <div style="display: flex; justify-content: center;">
        <p class="typewriter">> SELECT_REQUIRED_MODULES_</p>
    </div>
</div>

<form id="eventForm" method="POST" action="create_order.php">

    <div class="section-title">Technical_Events</div>
    <div class="options">
        <label><input type="checkbox" name="technical[]" value="Coding" data-price="100"><span class="option-box">Coding (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="Model Presentation" data-price="100"><span class="option-box">Model Pres. (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="Prompt Engineering" data-price="100"><span class="option-box">Prompt Eng. (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="Short Video Recording" data-price="100"><span class="option-box">Video Rec. (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="Waste to Best" data-price="150"><span class="option-box">Waste 2 Best (₹150)</span></label>
        <label><input type="checkbox" name="technical[]" value="Blogging Competition" data-price="100"><span class="option-box">Blogging (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="View with Review" data-price="100"><span class="option-box">Review (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="Debate Competition" data-price="100"><span class="option-box">Debate (₹100)</span></label>
        <label><input type="checkbox" name="technical[]" value="Drone Storm" data-price="200"><span class="option-box">Drone Storm (₹200)</span></label>
    </div>

    <div class="section-title">Fun_&_Games</div>
    <div class="options">
        <label><input type="checkbox" name="fun_games[]" value="BGMI" data-price="200"><span class="option-box">BGMI (₹200)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Free Fire" data-price="200"><span class="option-box">Free Fire (₹200)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Chess" data-price="50"><span class="option-box">Chess (₹50)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Musical Chair" data-price="50"><span class="option-box">Musical Chair (₹50)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Six Shot Basket" data-price="60"><span class="option-box">6-Shot Basket (₹60)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Gully Cricket" data-price="300"><span class="option-box">Gully Cricket (₹300)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Badminton" data-price="50"><span class="option-box">Badminton (₹50)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Badminton Mixed" data-price="100"><span class="option-box">Badminton Mixed (₹100)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Bar Hanging" data-price="30"><span class="option-box">Bar Hanging (₹30)</span></label>
        <label><input type="checkbox" name="fun_games[]" value="Battle Coordination" data-price="100"><span class="option-box">Battle Coord. (₹100)</span></label>
    </div>

    <div class="section-title">Fine_Arts</div>
    <div class="options">
        <label><input type="checkbox" name="fine_arts[]" value="Poetry" data-price="50"><span class="option-box">Poetry (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Shayari" data-price="50"><span class="option-box">Shayari (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Open Mic" data-price="50"><span class="option-box">Open Mic (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Rangoli" data-price="50"><span class="option-box">Rangoli (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Spell Bee" data-price="50"><span class="option-box">Spell Bee (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Debate" data-price="50"><span class="option-box">Debate (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Extempore" data-price="50"><span class="option-box">Extempore (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Mock Press Conference + Situational Analysis" data-price="100"><span class="option-box">Mock Press (₹100)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Live Sketch" data-price="50"><span class="option-box">Live Sketch (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Mehndi" data-price="50"><span class="option-box">Mehndi (₹50)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Business Idea + Logo Making + Presentation" data-price="100"><span class="option-box">Biz Idea (₹100)</span></label>
        <label><input type="checkbox" name="fine_arts[]" value="Art Exhibition + Eco Art + Poster Making" data-price="100"><span class="option-box">Art Exhibit (₹100)</span></label>
    </div>

    <div class="section-title">Cultural_Events</div>
    <div class="options">
        <label><input type="checkbox" name="cultural[]" value="Solo Singing" data-price="80"><span class="option-box">Solo Singing (₹80)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Solo Dancing" data-price="80"><span class="option-box">Solo Dancing (₹80)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Duel Singing Competition" data-price="120"><span class="option-box">Duel Singing (₹120)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Duet Dance" data-price="120"><span class="option-box">Duet Dance (₹120)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Rap Battle Competition" data-price="70"><span class="option-box">Rap Battle (₹70)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Instrumental Performance" data-price="70"><span class="option-box">Instrumental (₹70)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Battle of Bands" data-price="1200"><span class="option-box">Band Battle (₹1200)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Group Dance Competition" data-price="240"><span class="option-box">Group Dance (₹240)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Skit/Drama Performance" data-price="140"><span class="option-box">Skit/Drama (₹140)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Themed Fashion Show" data-price="80"><span class="option-box">Fashion (₹80)</span></label>
        <label><input type="checkbox" name="cultural[]" value="Beat Boxing Championship" data-price="60"><span class="option-box">Beat Boxing (₹60)</span></label>
    </div>

    <div class="total-bar">
        <div class="total-display">TOTAL: ₹<span id="liveTotal">0</span></div>
        <button type="submit" class="submit-btn">PAY_NOW</button>
    </div>

</form>

<script>
    const totalDisplay = document.getElementById('liveTotal');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');

    function calculateTotal() {
        let total = 0;
        checkboxes.forEach(box => {
            if (box.checked) {
                total += parseInt(box.getAttribute('data-price')) || 0;
            }
        });
        
        const currentTotal = parseInt(totalDisplay.innerText);
        if (currentTotal !== total) {
            animateValue(totalDisplay, currentTotal, total, 300);
        }
    }

    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    checkboxes.forEach(box => {
        box.addEventListener('change', calculateTotal);
    });

    // Parallax background (disabled on touch for performance)
    if (window.matchMedia("(min-width: 1024px)").matches) {
        document.addEventListener('mousemove', (e) => {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            document.querySelector('.grid-bg').style.transform = `translate(${x * 15}px, ${y * 15}px)`;
        });
    }
</script>

</body>
</html>