<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event registration</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
:root{
--primary:#00f3ff;
--secondary:#ff00ff;
--bg:#000000;
--card-bg:rgba(10,10,15,0.98);
--font-hdr:'Orbitron',sans-serif;
--font-mono:'JetBrains Mono',monospace;
}

*{box-sizing:border-box;margin:0;padding:0;}

body{
background-color:var(--bg);
color:#fff;
font-family:var(--font-mono);
overflow-x:hidden;
scroll-behavior:smooth;
}

/* --- FIXED CYBERPUNK BACKGROUND SYMBOL --- */
.hologram-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: -10; /* Lowered to stay behind everything */
    pointer-events: none;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100vw;
    height: 100vh;
}

.hologram-symbol {
    font-family: var(--font-hdr);
    font-size: clamp(5rem, 15vw, 12rem); /* Size adjusted for readability */
    font-weight: 900;
    color: rgba(0, 243, 255, 0.05); /* Very faint fill */
    -webkit-text-stroke: 1.5px var(--primary);
    filter: drop-shadow(0 0 20px var(--primary));
    text-transform: uppercase;
    letter-spacing: 15px;
    animation: masterPulse 6s infinite ease-in-out;
    position: relative;
    opacity: 0.4; /* Stronger visibility */
}

/* Secondary glowing layer */
.hologram-symbol::after {
    content: "Online event registration system";
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    -webkit-text-stroke: 1px var(--secondary);
    filter: blur(8px);
    opacity: 0.6;
    animation: glitchStatic 4s infinite linear;
}

@keyframes masterPulse {
    0%, 100% { transform: scale(1); opacity: 0.3; filter: hue-rotate(0deg) drop-shadow(0 0 10px var(--primary)); }
    50% { transform: scale(1.05); opacity: 0.6; filter: hue-rotate(15deg) drop-shadow(0 0 40px var(--primary)); }
}

@keyframes glitchStatic {
    0% { transform: translate(0); }
    2% { transform: translate(5px, -2px); }
    4% { transform: translate(-5px, 2px); }
    6% { transform: translate(0); }
    100% { transform: translate(0); }
}

/* --- LAYERS --- */
.stars{
position:fixed;
width:100%;
height:100%;
background:radial-gradient(circle at 50% 50%,#0a0a1a 0%,#000 100%);
z-index:-15;
}

.mesh-bg {
position: fixed;
top: 0; left: 0; width: 100%; height: 100%;
background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 210 297'%3E%3Cg opacity='0.15' stroke='%2300f3ff' stroke-width='0.2' fill='none'%3E%3Cpath d='m49.526 235.53c34.973-0.037 92.123-0.037 127 0 34.877 0.037 6.2622 0.0672-63.588 0.0672-69.85 0-98.385-0.0303-63.412-0.0672z'/%3E%3Cpath d='m49.24 237.11c34.961-0.037 92.171-0.037 127.13 0s6.3566 0.0672-63.566 0.0672c-69.923 0-98.528-0.0303-63.566-0.0672z'/%3E%3C/g%3E%3C/svg%3E");
background-size: 300px;
z-index: -12;
}

.grid-overlay {
position: fixed;
top: 0; left: 0; width: 100%; height: 100%;
background-image: 
    linear-gradient(rgba(0, 243, 255, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0, 243, 255, 0.03) 1px, transparent 1px);
background-size: 50px 50px;
z-index: -11;
}

/* --- REST OF THE CODE REMAINS UNCHANGED --- */

nav{
display:flex;
justify-content:space-between;
align-items:center;
padding:15px 5%;
background:rgba(0,0,0,0.95);
position:sticky;
top:0;
z-index:2000;
border-bottom:2px solid var(--primary);
}

.logo{
font-family:var(--font-hdr);
font-weight:900;
color:var(--primary);
font-size: 1.2rem;
}

.admin-link{
padding:8px 18px;
font-size:0.75rem;
color:var(--primary);
border:1px solid var(--primary);
text-decoration:none;
font-family:var(--font-hdr);
font-weight:900;
background:rgba(0,243,255,0.05);
}

header{
height:35vh;
display:flex;
flex-direction:column;
justify-content:center;
align-items:center;
text-align:center;
padding: 0 20px;
}

.hero-title{
font-family:var(--font-hdr);
font-size:clamp(2.2rem,8vw,5rem);
font-weight:900;
text-shadow:0 0 15px var(--primary);
}

.mission-brief {
max-width: 900px;
margin: 0 auto 40px;
padding: 0 20px;
text-align: center;
}

.brief-text {
font-size: clamp(0.9rem, 4vw, 1.1rem);
line-height: 1.6;
color: #fff;
border: 1px solid rgba(0, 243, 255, 0.3);
border-left: 5px solid var(--primary);
padding: 25px;
display: inline-block;
text-align: left;
background: rgba(0, 243, 255, 0.05);
backdrop-filter: blur(5px);
}

.system-tag{
color:var(--secondary);
margin-top:15px;
padding:4px 12px;
border:1px solid var(--secondary);
font-size: 0.8rem;
font-weight: bold;
}

.mainframe-section{
max-width:1200px;
margin:0 auto;
padding:40px 0;
display:flex;
flex-direction:column;
align-items:center;
}

.category-core{
padding:12px 25px;
background:#000;
border:2px solid var(--secondary);
font-family:var(--font-hdr);
letter-spacing:3px;
box-shadow:0 0 15px var(--secondary);
margin-bottom:40px;
font-size: 0.9rem;
}

.nodes-container{
width:100%;
display:flex;
flex-direction:column;
gap:25px;
padding:0 20px;
}

.event-node{
width:46%;
padding:22px;
background:var(--card-bg);
border:1px solid rgba(0,243,255,0.4);
opacity:0;
transition:all 0.7s ease;
position: relative;
}

.event-node.left{align-self:flex-start;transform:translateX(-30px); border-right: 4px solid var(--primary);}
.event-node.right{align-self:flex-end;transform:translateX(30px); border-left: 4px solid var(--primary);}
.event-node.revealed{opacity:1;transform:translateX(0);}

.event-node:hover{
box-shadow:0 0 25px var(--primary);
transform:scale(1.02);
}

.node-name{
font-family:var(--font-hdr);
font-size:1.1rem;
color:var(--primary);
margin-bottom:12px;
}

.node-meta{
display:flex;
justify-content:space-between;
font-size:0.85rem;
padding-top:10px;
}

.price{
background:var(--primary);
color:#000;
padding:1px 8px;
font-weight:900;
}

.node-description{
margin-top:14px;
font-size:0.8rem;
line-height:1.5;
color:#ccc;
opacity:0;
max-height:0;
overflow:hidden;
transition:all 0.4s ease;
}

.event-node:hover .node-description{
opacity:1;
max-height:120px;
animation:neonPulse 0.8s linear;
}

@keyframes neonPulse{
0%,18%,22%,25%,53%,57%,100%{
text-shadow:0 0 4px var(--primary),0 0 10px var(--primary),0 0 20px var(--primary);
}
20%,24%,55%{text-shadow:none;}
}

@media (max-width: 768px) {
.admin-link { background: var(--primary); color: #000; padding: 6px 12px; font-size: 0.65rem; }
.logo { font-size: 1rem; }
.event-node { width: calc(100% - 50px); margin-left: 40px; align-self: flex-start !important; transform: translateX(20px) !important; border-left: 4px solid var(--primary) !important; border-right: 1px solid rgba(0,243,255,0.2) !important; }
}

.cta-container{text-align:center;padding:60px 20px 150px;}

.cta-main {
  padding: 22px 50px;
  border: 2px solid var(--primary);
  color: var(--primary);
  text-decoration: none;
  font-family: var(--font-hdr);
  font-weight: 700;
  letter-spacing: 4px;
  display: inline-block;
  position: relative;
  transition: 0.2s;
  background: rgba(0, 243, 255, 0.05);
  clip-path: polygon(10% 0, 100% 0, 100% 70%, 90% 100%, 0 100%, 0 30%);
  overflow: hidden;
}

.cta-main::before {
  content: "";
  position: absolute;
  top: -100%; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(transparent, rgba(0, 243, 255, 0.2), transparent);
  animation: scanline 3s linear infinite;
}

.cta-main:hover {
  background: var(--primary);
  color: #000;
  box-shadow: 0 0 40px var(--primary), 5px 0px 0px var(--secondary);
  transform: skew(-2deg);
  text-shadow: 2px 2px var(--secondary);
}

@keyframes scanline {
  0% { top: -100%; }
  100% { top: 100%; }
}

/* ========================= */
/* ===== MOBILE OPTIMIZATION ===== */
/* ========================= */

@media (max-width: 768px) {

    /* Improve body rendering */
    body {
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }

    /* Reduce hologram dominance on small screens */
    .hologram-symbol {
        font-size: clamp(3rem, 18vw, 6rem);
        letter-spacing: 8px;
        opacity: 0.25;
    }

    /* Navbar spacing */
    nav {
        padding: 12px 4%;
    }

    .logo {
        font-size: 0.9rem;
        letter-spacing: 1px;
    }

    .admin-link {
        font-size: 0.65rem;
        padding: 6px 10px;
    }

    /* Header */
    header {
        height: 28vh;
        padding: 0 15px;
    }

    .hero-title {
        font-size: clamp(1.8rem, 9vw, 3rem);
        line-height: 1.1;
    }

    .system-tag {
        font-size: 0.7rem;
        margin-top: 12px;
    }

    /* Mission Brief */
    .mission-brief {
        margin-bottom: 30px;
        padding: 0 15px;
    }

    .brief-text {
        font-size: 0.85rem;
        padding: 18px;
        line-height: 1.5;
    }

    /* Category Titles */
    .category-core {
        font-size: 0.75rem;
        padding: 10px 18px;
        letter-spacing: 2px;
        text-align: center;
    }

    /* Event Nodes – Full Width & Better Tap UX */
    .event-node {
        width: 100% !important;
        margin-left: 0 !important;
        align-self: center !important;
        transform: translateX(0) !important;
        border-left: 4px solid var(--primary) !important;
        border-right: 1px solid rgba(0,243,255,0.2) !important;
        padding: 18px;
    }

    .event-node:hover {
        transform: scale(1.01);
    }

    .node-name {
        font-size: 1rem;
    }

    .node-meta {
        font-size: 0.75rem;
        flex-direction: column;
        gap: 6px;
        align-items: flex-start;
    }

    .price {
        font-size: 0.75rem;
        padding: 2px 6px;
    }

    .node-description {
        font-size: 0.75rem;
        line-height: 1.4;
    }

    /* CTA Button */
    .cta-container {
        padding: 50px 20px 120px;
    }

    .cta-main {
        padding: 16px 28px;
        font-size: 0.75rem;
        letter-spacing: 2px;
        width: 100%;
        max-width: 320px;
        text-align: center;
    }

}

/* Extra optimization for very small screens */
@media (max-width: 420px) {

    .hero-title {
        font-size: 1.6rem;
    }

    .brief-text {
        font-size: 0.8rem;
    }

    .event-node {
        padding: 16px;
    }

    .cta-main {
        padding: 14px 22px;
        font-size: 0.7rem;
    }
}
</style>
</head>
<body>

<div class="hologram-container">
    <div class="hologram-symbol">Automation</div>
</div>

<div class="stars"></div>
<div class="mesh-bg"></div>
<div class="grid-overlay"></div>

<nav>
<div class="logo">Online event registration system</div>
<a href="backend/login.php" class="admin-link">ADMIN_PORTAL</a>
</nav>

<header>
<h1 class="hero-title">ANNUAL TECHNO FEST</h1>
<div class="system-tag">[ MAINFRAME_DATA_SYNC_ON ]</div>
</header>

<section class="mission-brief">
    <div class="brief-text">
        <span style="color: var(--primary); font-weight: bold;">> LOG:</span> Architect your future. This is not just a competition; it is a system-wide evolution. Whether you rewrite reality through code or dominate the stage—your legacy begins here.
    </div>
</section>

<section class="mainframe-section">
<div class="category-core">01 // TECHNICAL_DOMAIN</div>
<div class="nodes-container">
<div class="event-node left" data-name="Coding" data-price="100"></div>
<div class="event-node right" data-name="Model Presentation" data-price="100"></div>
<div class="event-node left" data-name="Prompt Engineering" data-price="100"></div>
<div class="event-node right" data-name="Short Video Recording" data-price="100"></div>
<div class="event-node left" data-name="Waste to Best" data-price="150"></div>
<div class="event-node right" data-name="Blogging Competition" data-price="100"></div>
<div class="event-node left" data-name="View with Review" data-price="100"></div>
<div class="event-node right" data-name="Debate Competition" data-price="100"></div>
<div class="event-node left" data-name="Drone Storm" data-price="200"></div>
</div>
</section>

<section class="mainframe-section">
<div class="category-core">02 // COMBAT_NETWORK</div>
<div class="nodes-container">
<div class="event-node left" data-name="BGMI" data-price="200"></div>
<div class="event-node right" data-name="Free Fire" data-price="200"></div>
<div class="event-node left" data-name="Chess" data-price="50"></div>
<div class="event-node right" data-name="Musical Chair" data-price="50"></div>
<div class="event-node left" data-name="Six Shot Basket" data-price="60"></div>
<div class="event-node right" data-name="Gully Cricket" data-price="300"></div>
<div class="event-node left" data-name="Badminton" data-price="50"></div>
<div class="event-node right" data-name="Badminton Mixed" data-price="100"></div>
<div class="event-node left" data-name="Bar Hanging" data-price="30"></div>
<div class="event-node right" data-name="Battle Coordination" data-price="100"></div>
</div>
</section>

<section class="mainframe-section">
<div class="category-core">03 // CULTURAL_RESONANCE</div>
<div class="nodes-container">
<div class="event-node left" data-name="Solo Singing" data-price="80"></div>
<div class="event-node right" data-name="Solo Dancing" data-price="80"></div>
<div class="event-node left" data-name="Duel Singing" data-price="120"></div>
<div class="event-node right" data-name="Rap Battle" data-price="70"></div>
<div class="event-node left" data-name="Instrumental" data-price="70"></div>
<div class="event-node right" data-name="Battle of Bands" data-price="1200"></div>
<div class="event-node left" data-name="Group Dance" data-price="240"></div>
<div class="event-node right" data-name="Skit/Drama" data-price="140"></div>
<div class="event-node left" data-name="Fashion Show" data-price="80"></div>
<div class="event-node right" data-name="Beat Boxing" data-price="60"></div>
<div class="event-node left" data-name="Duet Dance" data-price="120"></div>
</div>
</section>

<section class="mainframe-section">
<div class="category-core">04 // ARTISTIC_VISION</div>
<div class="nodes-container">
<div class="event-node left" data-name="Poetry" data-price="50"></div>
<div class="event-node right" data-name="Shayari" data-price="50"></div>
<div class="event-node left" data-name="Open Mic" data-price="50"></div>
<div class="event-node right" data-name="Rangoli" data-price="50"></div>
<div class="event-node left" data-name="Spell Bee" data-price="50"></div>
<div class="event-node right" data-name="Debate (Fine Arts)" data-price="50"></div>
<div class="event-node left" data-name="Extempore" data-price="50"></div>
<div class="event-node right" data-name="Mock Press" data-price="100"></div>
<div class="event-node left" data-name="Live Sketch" data-price="50"></div>
<div class="event-node right" data-name="Mehndi" data-price="50"></div>
<div class="event-node left" data-name="Business Idea" data-price="100"></div>
<div class="event-node right" data-name="Art Exhibition" data-price="100"></div>
</div>
</section>

<div class="cta-container">
<a href="backend/email_verification.php" class="cta-main">INITIALIZE_REGISTRATION</a>
</div>

<script>
const eventDescriptions = {
"Coding":"Enter the algorithmic arena where logic, speed, and precision collide. Participants solve complex programming problems under pressure, optimizing every line of code to outmaneuver competitors.",
"Model Presentation":"Present innovative prototypes and technical concepts. Demonstrate functionality, explain design logic, and convince the judges that your model is a glimpse into the future.",
"Prompt Engineering":"Master the art of commanding artificial intelligence. Craft strategic prompts that extract powerful, accurate, and creative responses from modern AI systems.",
"Short Video Recording":"Produce a short cinematic story with creativity and impact. Capture visuals, edit effectively, and deliver a message that resonates within a limited time frame.",
"Waste to Best":"Transform discarded materials into useful or artistic creations. Innovation, sustainability, and imagination combine to prove that waste can become value.",
"Blogging Competition":"Write compelling analytical articles on given themes. Structure ideas clearly, present arguments logically, and capture readers with powerful storytelling.",
"View with Review":"Watch, analyze, and critique content with intellectual depth. Participants evaluate themes, narratives, and perspectives while presenting structured reviews.",
"Debate Competition":"Enter a battlefield of ideas where logic, persuasion, and clarity determine victory. Present arguments, counter opponents, and defend your stance with precision.",
"Drone Storm":"Pilot drones through challenging aerial missions and precision tasks. Control, accuracy, and tactical maneuvering determine who dominates the skies.",
"BGMI":"Strategic battle royale where survival depends on teamwork, positioning, and tactical combat decisions in an intense digital battlefield.",
"Free Fire":"Fast-paced survival combat where quick reflexes, smart positioning, and tactical awareness determine the last player standing.",
"Chess":"A timeless contest of strategy and foresight. Every move shapes the battlefield as players calculate multiple possibilities ahead.",
"Musical Chair":"A test of alertness and reflexes. Participants must react instantly as music stops, racing to secure the remaining seat.",
"Six Shot Basket":"A precision basketball challenge where participants attempt rapid shots to score maximum baskets within a limited number of attempts.",
"Gully Cricket":"Street-style cricket played with raw energy and improvisation. Teams battle for supremacy using skill, strategy, and quick decision making.",
"Badminton":"High-speed rallies demand agility, accuracy, and stamina as players outmaneuver opponents across the court.",
"Bar Hanging":"A pure endurance challenge. Participants hang from a bar for as long as possible, pushing grip strength and mental resilience to the limit.",
"Battle Coordination":"Teams must synchronize strategy, communication, and rapid decision making to complete coordinated challenges successfully.",
"Solo Singing":"Take the stage and command attention with vocal talent, emotional expression, and musical control.",
"Solo Dancing":"Express rhythm, creativity, and technique through a solo dance performance that captivates the audience.",
"Duel Singing":"Two voices meet in a musical duel where harmony, vocal strength, and stage presence determine the winner.",
"Rap Battle":"A lyrical showdown where rhythm, wordplay, and improvisation ignite the stage in a battle of verbal dominance.",
"Instrumental":"Let music speak without words. Perform powerful melodies and rhythms using instruments alone.",
"Battle of Bands":"Multiple bands collide in a high-energy musical clash, blending instruments, vocals, and performance to dominate the stage.",
"Group Dance":"Teams perform synchronized choreography combining rhythm, coordination, and powerful stage energy.",
"Skit/Drama":"Bring stories to life through acting, dialogue, and stage presence in a theatrical performance.",
"Fashion Show":"Step onto the runway with confidence and style. Participants showcase creativity, attire, and personality through striking presentations.",
"Beat Boxing":"Create complex rhythms and beats using only the human voice. Control breath, sound patterns, and tempo to amaze the audience.",
"Poetry":"Craft powerful verses that express emotion, thought, and imagination through structured poetic performance.",
"Shayari":"Deliver poetic expressions rich with cultural flavor, emotion, and storytelling through traditional shayari.",
"Open Mic":"An open stage where performers share music, poetry, comedy, or storytelling in a raw and authentic performance.",
"Rangoli":"Design intricate patterns using colors and symmetry, transforming the floor into vibrant artistic expression.",
"Spell Bee":"A battle of vocabulary and linguistic precision where participants must spell challenging words accurately under pressure.",
"Debate (Fine Arts)":"Discuss artistic perspectives and cultural ideas through structured debate, blending creativity with intellectual argument.",
"Extempore":"Speak instantly on a given topic without preparation. Clarity of thought, confidence, and articulation define success.",
"Mock Press":"Simulate a press conference where participants respond to rapid-fire questions with clarity, confidence, and composure.",
"Live Sketch":"Create artwork in real time, demonstrating creativity, technique, and visual storytelling within a limited timeframe.",
"Mehndi":"Design intricate henna patterns with precision and creativity, turning hands into living canvases of art.",
"Business Idea":"Present a startup concept that solves real problems. Pitch innovation, strategy, and impact to convince the judges.",
"Art Exhibition":"Display creative artworks that communicate ideas, stories, and visual imagination to viewers.",
"Duet Dance":"Two performers unite in rhythm and coordination, delivering a dance performance built on harmony and partnership."
};

const obs=new IntersectionObserver(entries=>{
entries.forEach(en=>{
if(en.isIntersecting && !en.target.classList.contains('revealed')){
en.target.classList.add('revealed');
const name=en.target.getAttribute('data-name');
const price=en.target.getAttribute('data-price');
en.target.innerHTML=`
<span class="node-name">${name}</span>
<div class="node-meta">
<span>#DATA_LINK_${Math.floor(Math.random()*900)+100}</span>
<span class="price">CREDITS: ${price}</span>
</div>
<div class="node-description">> SYSTEM_INFO: ${eventDescriptions[name] || "Awaiting data input..."}</div>
`;
}
});
},{threshold:0.1});

document.querySelectorAll('.event-node').forEach(node=>obs.observe(node));
</script>
</body>
</html>