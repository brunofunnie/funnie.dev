<?php
if (!defined('ABSPATH')) exit;

$wordmark_top    = funnie_settings('hero_wordmark_top',    'funnie');
$wordmark_bottom = funnie_settings('hero_wordmark_bottom', 'dev');
$day_label       = funnie_settings('hero_day_label',   'DAY');
$night_label     = funnie_settings('hero_night_label', 'NIGHT');
$day_tagline     = funnie_settings('hero_day_tagline',   '// professional');
$night_tagline   = funnie_settings('hero_night_tagline', '// personal');
?>
<main id="hero" class="relative flex h-screen w-screen overflow-hidden">
    <!-- Weather effects layer (rain, snow, fog, storm, extra clouds) -->
    <div class="weather-fx" aria-hidden="true">
        <div class="fx-clouds-extra"></div>
        <div class="fx-rain"></div>
        <div class="fx-snow"></div>
        <div class="fx-fog"></div>
        <div class="fx-storm-tint"></div>
        <div class="fx-lightning"></div>
    </div>

    <!-- DAY SIDE -->
    <section
        id="day-side"
        data-side="day"
        class="relative flex flex-1 flex-col justify-between text-day-text transition-[flex-grow] duration-700 ease-out"
    >
        <div class="scenery" aria-hidden="true">
            <div class="sun-rays"></div>

            <div class="cloud cloud-a">
                <svg width="160" height="60" viewBox="0 0 160 60" fill="none">
                    <ellipse cx="40" cy="40" rx="40" ry="18" fill="#fffaf0" fill-opacity="0.95"/>
                    <ellipse cx="80" cy="32" rx="48" ry="22" fill="#fffaf0" fill-opacity="0.95"/>
                    <ellipse cx="120" cy="40" rx="36" ry="16" fill="#fffaf0" fill-opacity="0.95"/>
                </svg>
            </div>
            <div class="cloud cloud-b">
                <svg width="220" height="70" viewBox="0 0 220 70" fill="none">
                    <ellipse cx="50" cy="48" rx="50" ry="20" fill="#fff5e3" fill-opacity="0.85"/>
                    <ellipse cx="110" cy="38" rx="60" ry="26" fill="#fff5e3" fill-opacity="0.85"/>
                    <ellipse cx="170" cy="48" rx="45" ry="20" fill="#fff5e3" fill-opacity="0.85"/>
                </svg>
            </div>
            <div class="cloud cloud-c">
                <svg width="120" height="48" viewBox="0 0 120 48" fill="none">
                    <ellipse cx="32" cy="32" rx="32" ry="14" fill="#ffeed4" fill-opacity="0.8"/>
                    <ellipse cx="64" cy="26" rx="40" ry="18" fill="#ffeed4" fill-opacity="0.8"/>
                    <ellipse cx="92" cy="32" rx="28" ry="13" fill="#ffeed4" fill-opacity="0.8"/>
                </svg>
            </div>
            <div class="cloud cloud-d">
                <svg width="180" height="55" viewBox="0 0 180 55" fill="none">
                    <ellipse cx="45" cy="38" rx="45" ry="16" fill="#fffaf0" fill-opacity="0.7"/>
                    <ellipse cx="100" cy="28" rx="55" ry="22" fill="#fffaf0" fill-opacity="0.7"/>
                    <ellipse cx="145" cy="38" rx="35" ry="14" fill="#fffaf0" fill-opacity="0.7"/>
                </svg>
            </div>

            <div class="birds" aria-hidden="true"></div>

            <div class="day-hills">
                <svg viewBox="0 0 1200 400" preserveAspectRatio="none" fill="none">
                    <path d="M0,260 C150,210 300,250 450,225 C600,200 750,250 900,220 C1050,195 1140,225 1200,210 L1200,400 L0,400 Z" fill="#d97a4a" fill-opacity="0.55"/>
                    <path d="M0,310 C200,270 350,310 520,285 C680,260 820,305 980,280 C1100,260 1160,285 1200,275 L1200,400 L0,400 Z" fill="#b85a25" fill-opacity="0.75"/>
                    <path d="M0,360 C160,330 320,360 500,340 C680,320 840,355 1000,335 C1120,320 1170,340 1200,335 L1200,400 L0,400 Z" fill="#7a3210"/>
                    <g fill="#3a1606" fill-opacity="0.4">
                        <rect x="79.6" y="241" width="0.8" height="2"/><polygon points="78,241 80,237 82,241"/>
                        <rect x="139.6" y="235" width="0.8" height="2"/><polygon points="138,235 140,231 142,235"/>
                        <rect x="209.6" y="234" width="0.8" height="2"/><polygon points="208,234 210,230 212,234"/>
                        <rect x="289.6" y="234" width="0.8" height="2"/><polygon points="288,234 290,230 292,234"/>
                        <rect x="369.6" y="233" width="0.8" height="2"/><polygon points="368,233 370,229 372,233"/>
                        <rect x="454.6" y="224" width="0.8" height="2"/><polygon points="453,224 455,220 457,224"/>
                        <rect x="539.6" y="218" width="0.8" height="2"/><polygon points="538,218 540,214 542,218"/>
                        <rect x="624.6" y="221" width="0.8" height="2"/><polygon points="623,221 625,217 627,221"/>
                        <rect x="699.6" y="226" width="0.8" height="2"/><polygon points="698,226 700,222 702,226"/>
                        <rect x="789.6" y="230" width="0.8" height="2"/><polygon points="788,230 790,226 792,230"/>
                        <rect x="879.6" y="223" width="0.8" height="2"/><polygon points="878,223 880,219 882,223"/>
                        <rect x="969.6" y="212" width="0.8" height="2"/><polygon points="968,212 970,208 972,212"/>
                        <rect x="1059.6" y="210" width="0.8" height="2"/><polygon points="1058,210 1060,206 1062,210"/>
                        <rect x="1149.6" y="214" width="0.8" height="2"/><polygon points="1148,214 1150,210 1152,214"/>
                    </g>
                    <g fill="#3a1606" fill-opacity="0.72">
                        <rect x="59.4" y="300" width="1.2" height="3"/><polygon points="57,300 60,293 63,300"/>
                        <rect x="169.4" y="292" width="1.2" height="3"/><polygon points="167,292 170,285 173,292"/>
                        <rect x="269.4" y="294" width="1.2" height="3"/><polygon points="267,294 270,287 273,294"/>
                        <rect x="399.4" y="296" width="1.2" height="3"/><polygon points="397,296 400,289 403,296"/>
                        <rect x="509.4" y="287" width="1.2" height="3"/><polygon points="507,287 510,280 513,287"/>
                        <rect x="639.4" y="278" width="1.2" height="3"/><polygon points="637,278 640,271 643,278"/>
                        <rect x="749.4" y="283" width="1.2" height="3"/><polygon points="747,283 750,276 753,283"/>
                        <rect x="869.4" y="288" width="1.2" height="3"/><polygon points="867,288 870,281 873,288"/>
                        <rect x="989.4" y="280" width="1.2" height="3"/><polygon points="987,280 990,273 993,280"/>
                        <rect x="1109.4" y="273" width="1.2" height="3"/><polygon points="1107,273 1110,266 1113,273"/>
                        <rect x="1179.4" y="278" width="1.2" height="3"/><polygon points="1177,278 1180,271 1183,278"/>
                    </g>
                    <g fill="#3a1606">
                        <rect x="69.25" y="354" width="1.5" height="4"/><polygon points="66,354 70,344 74,354"/>
                        <rect x="183.25" y="346" width="1.5" height="4"/><polygon points="180,346 184,336 188,346"/>
                        <rect x="224.25" y="346" width="1.5" height="4"/><polygon points="220,346 225,334 230,346"/>
                        <rect x="274.25" y="350" width="1.5" height="4"/><polygon points="271,350 275,340 279,350"/>
                        <rect x="434.25" y="345" width="1.5" height="4"/><polygon points="430,345 435,332 440,345"/>
                        <rect x="509.25" y="340" width="1.5" height="4"/><polygon points="506,340 510,330 514,340"/>
                        <rect x="614.25" y="335" width="1.5" height="4"/><polygon points="610,335 615,323 620,335"/>
                        <rect x="784.25" y="339" width="1.5" height="4"/><polygon points="780,339 785,326 790,339"/>
                        <rect x="829.25" y="343" width="1.5" height="4"/><polygon points="826,343 830,331 834,343"/>
                        <rect x="954.25" y="340" width="1.5" height="4"/><polygon points="950,340 955,328 960,340"/>
                        <rect x="1014.25" y="335" width="1.5" height="4"/><polygon points="1011,335 1015,323 1019,335"/>
                        <rect x="1084.25" y="332" width="1.5" height="4"/><polygon points="1080,332 1085,319 1090,332"/>
                        <rect x="118.75" y="349" width="2.5" height="6"/><polygon points="114,349 120,333 126,349"/>
                        <rect x="353.75" y="349" width="2.5" height="6"/><polygon points="349,349 355,333 361,349"/>
                        <rect x="718.75" y="338" width="2.5" height="6"/><polygon points="714,338 720,322 726,338"/>
                        <rect x="1138.75" y="334" width="2.5" height="6"/><polygon points="1134,334 1140,318 1146,334"/>
                    </g>
                </svg>
            </div>

        </div>

        <div class="sun-wrap" role="button" tabindex="0" aria-label="Switch to day mode"><div id="sun"></div></div>

        <header class="full-only side-header">
            <nav aria-label="Day sections" class="flex flex-wrap gap-6">
                <button type="button" data-open-panel="about" class="nav-link day-nav">About</button>
                <button type="button" data-open-panel="resume" class="nav-link day-nav">Resume</button>
                <button type="button" data-open-panel="blog-day" class="nav-link day-nav">Blog</button>
            </nav>
        </header>

        <nav class="collapsed-only sidebar-nav" aria-label="Day sections (compact)">
            <button type="button" data-open-panel="about" class="icon-btn icon-day" data-tooltip="About" aria-label="About">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
            </button>
            <button type="button" data-open-panel="resume" class="icon-btn icon-day" data-tooltip="Resume" aria-label="Resume">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="14" y2="16"/></svg>
            </button>
            <button type="button" data-open-panel="blog-day" class="icon-btn icon-day" data-tooltip="Blog" aria-label="Blog">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
        </nav>

        <div class="full-only wordmark wordmark-day" aria-hidden="true">
            <span class="wordmark-line"><?php echo esc_html($wordmark_top); ?></span>
            <span class="wordmark-line"><?php echo esc_html($wordmark_bottom); ?></span>
        </div>

        <div class="full-only big-heading">
            <h1 class="text-5xl font-bold tracking-tight md:text-7xl"><?php echo esc_html($day_label); ?></h1>
        </div>

        <div class="full-only day-footer side-footer">
            <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html($day_tagline); ?></div>
        </div>
    </section>

    <!-- NIGHT SIDE -->
    <section
        id="night-side"
        data-side="night"
        class="relative flex flex-1 flex-col justify-between text-night-text transition-[flex-grow] duration-700 ease-out"
    >
        <div class="scenery" aria-hidden="true">
            <div class="aurora"></div>

            <div class="stars">
                <span class="star" style="top:8%;left:12%;width:2px;height:2px;animation-delay:0s"></span>
                <span class="star" style="top:5%;left:28%;width:1.5px;height:1.5px;animation-delay:0.4s"></span>
                <span class="star" style="top:12%;left:44%;width:3px;height:3px;animation-delay:0.9s"></span>
                <span class="star" style="top:18%;left:62%;width:2px;height:2px;animation-delay:1.4s"></span>
                <span class="star" style="top:6%;left:78%;width:1.5px;height:1.5px;animation-delay:0.2s"></span>
                <span class="star" style="top:14%;left:88%;width:2.5px;height:2.5px;animation-delay:1.8s"></span>
                <span class="star" style="top:22%;left:8%;width:1.5px;height:1.5px;animation-delay:2.2s"></span>
                <span class="star" style="top:24%;left:36%;width:2px;height:2px;animation-delay:0.6s"></span>
                <span class="star" style="top:30%;left:55%;width:2.5px;height:2.5px;animation-delay:1.1s"></span>
                <span class="star" style="top:34%;left:72%;width:1.5px;height:1.5px;animation-delay:1.6s"></span>
                <span class="star" style="top:38%;left:18%;width:2px;height:2px;animation-delay:2.5s"></span>
                <span class="star" style="top:40%;left:42%;width:1.5px;height:1.5px;animation-delay:0.3s"></span>
                <span class="star" style="top:44%;left:64%;width:2px;height:2px;animation-delay:1.9s"></span>
                <span class="star" style="top:46%;left:82%;width:2.5px;height:2.5px;animation-delay:0.8s"></span>
                <span class="star" style="top:10%;left:5%;width:1px;height:1px;animation-delay:2.7s"></span>
                <span class="star" style="top:20%;left:50%;width:1px;height:1px;animation-delay:0.1s"></span>
                <span class="star" style="top:32%;left:25%;width:1px;height:1px;animation-delay:1.3s"></span>
                <span class="star" style="top:42%;left:32%;width:1px;height:1px;animation-delay:2.0s"></span>
                <span class="star" style="top:16%;left:70%;width:1px;height:1px;animation-delay:0.5s"></span>
                <span class="star" style="top:26%;left:90%;width:1px;height:1px;animation-delay:1.7s"></span>
            </div>

            <div class="city">
                <svg viewBox="0 0 1200 380" preserveAspectRatio="none" fill="none">
                    <g fill="#0a2a3e" fill-opacity="0.85">
                        <rect x="0"   y="180" width="80"  height="200"/>
                        <rect x="80"  y="155" width="55"  height="225"/>
                        <rect x="135" y="200" width="70"  height="180"/>
                        <rect x="205" y="170" width="40"  height="210"/>
                        <rect x="245" y="190" width="80"  height="190"/>
                        <rect x="325" y="160" width="60"  height="220"/>
                        <rect x="385" y="195" width="50"  height="185"/>
                        <rect x="435" y="175" width="70"  height="205"/>
                        <rect x="505" y="200" width="45"  height="180"/>
                        <rect x="550" y="165" width="65"  height="215"/>
                        <rect x="615" y="185" width="55"  height="195"/>
                        <rect x="670" y="150" width="50"  height="230"/>
                        <rect x="720" y="190" width="70"  height="190"/>
                        <rect x="790" y="170" width="55"  height="210"/>
                        <rect x="845" y="200" width="80"  height="180"/>
                        <rect x="925" y="160" width="60"  height="220"/>
                        <rect x="985" y="195" width="50"  height="185"/>
                        <rect x="1035" y="175" width="70" height="205"/>
                        <rect x="1105" y="190" width="45" height="190"/>
                        <rect x="1150" y="165" width="50" height="215"/>
                    </g>
                    <g fill="#021624">
                        <rect x="0"   y="240" width="120" height="140"/>
                        <rect x="120" y="215" width="80"  height="165"/>
                        <rect x="200" y="245" width="60"  height="135"/>
                        <rect x="260" y="225" width="100" height="155"/>
                        <rect x="360" y="200" width="70"  height="180"/>
                        <rect x="430" y="240" width="90"  height="140"/>
                        <rect x="520" y="220" width="80"  height="160"/>
                        <rect x="600" y="195" width="60"  height="185"/>
                        <rect x="660" y="230" width="110" height="150"/>
                        <rect x="770" y="210" width="70"  height="170"/>
                        <rect x="840" y="245" width="80"  height="135"/>
                        <rect x="920" y="215" width="100" height="165"/>
                        <rect x="1020" y="200" width="60" height="180"/>
                        <rect x="1080" y="240" width="120" height="140"/>
                    </g>
                    <g class="city-window" fill="#ffd66e" style="animation-delay:0s">
                        <rect x="20"   y="265" width="6" height="6"/>
                        <rect x="40"   y="265" width="6" height="6"/>
                        <rect x="60"   y="285" width="6" height="6"/>
                        <rect x="380" y="220" width="5" height="5"/>
                        <rect x="400" y="240" width="5" height="5"/>
                        <rect x="610" y="215" width="5" height="5"/>
                        <rect x="690" y="250" width="5" height="5"/>
                        <rect x="710" y="270" width="5" height="5"/>
                        <rect x="940" y="240" width="5" height="5"/>
                        <rect x="960" y="260" width="5" height="5"/>
                    </g>
                    <g class="city-window" fill="#6ee7ff" style="animation-delay:1.5s">
                        <rect x="80"  y="245" width="5" height="5"/>
                        <rect x="170" y="280" width="5" height="5"/>
                        <rect x="280" y="245" width="5" height="5"/>
                        <rect x="460" y="265" width="5" height="5"/>
                        <rect x="540" y="245" width="5" height="5"/>
                        <rect x="790" y="240" width="5" height="5"/>
                        <rect x="870" y="270" width="5" height="5"/>
                        <rect x="1050" y="225" width="5" height="5"/>
                        <rect x="1110" y="265" width="5" height="5"/>
                    </g>
                    <g class="city-window" fill="#a78bfa" style="animation-delay:3s">
                        <rect x="140" y="235" width="5" height="5"/>
                        <rect x="320" y="260" width="5" height="5"/>
                        <rect x="500" y="225" width="5" height="5"/>
                        <rect x="630" y="220" width="5" height="5"/>
                        <rect x="820" y="245" width="5" height="5"/>
                        <rect x="990" y="220" width="5" height="5"/>
                        <rect x="1140" y="245" width="5" height="5"/>
                    </g>
                    <circle cx="60"  y="180" cy="180" r="2" fill="#ff5577"/>
                    <circle cx="600" y="195" cy="195" r="2" fill="#ff5577"/>
                    <circle cx="1020" y="200" cy="200" r="2" fill="#ff5577"/>
                </svg>
            </div>

            <div class="lamps">
                <div class="lamp" style="left:15%;">
                    <div class="lamp-glow" style="left:50%;top:18%;animation-delay:0s"></div>
                    <svg width="40" height="220" viewBox="0 0 40 220" fill="none">
                        <line x1="20" y1="40" x2="20" y2="220" stroke="#1a2a3a" stroke-width="3"/>
                        <circle cx="20" cy="34" r="6" fill="#ffe9a8"/>
                        <circle cx="20" cy="34" r="3" fill="#fffaf0"/>
                        <line x1="14" y1="40" x2="26" y2="40" stroke="#1a2a3a" stroke-width="2"/>
                    </svg>
                </div>
                <div class="lamp" style="left:48%;">
                    <div class="lamp-glow" style="left:50%;top:14%;animation-delay:1.5s"></div>
                    <svg width="40" height="260" viewBox="0 0 40 260" fill="none">
                        <line x1="20" y1="40" x2="20" y2="260" stroke="#1a2a3a" stroke-width="3"/>
                        <circle cx="20" cy="34" r="6" fill="#a78bfa"/>
                        <circle cx="20" cy="34" r="3" fill="#e8ecff"/>
                        <line x1="14" y1="40" x2="26" y2="40" stroke="#1a2a3a" stroke-width="2"/>
                    </svg>
                </div>
                <div class="lamp" style="left:80%;">
                    <div class="lamp-glow" style="left:50%;top:18%;animation-delay:0.8s"></div>
                    <svg width="40" height="200" viewBox="0 0 40 200" fill="none">
                        <line x1="20" y1="40" x2="20" y2="200" stroke="#1a2a3a" stroke-width="3"/>
                        <circle cx="20" cy="34" r="6" fill="#6ee7ff"/>
                        <circle cx="20" cy="34" r="3" fill="#fffaf0"/>
                        <line x1="14" y1="40" x2="26" y2="40" stroke="#1a2a3a" stroke-width="2"/>
                    </svg>
                </div>
            </div>

        </div>

        <div class="moon-wrap" role="button" tabindex="0" aria-label="Switch to night mode">
            <div id="moon"><div class="moon-phase-shadow" aria-hidden="true"></div></div>
        </div>

        <div class="shooting-stars" aria-hidden="true"></div>

        <header class="full-only side-header" style="justify-content: flex-end;">
            <nav aria-label="Night sections" class="flex flex-wrap gap-6">
                <button type="button" data-open-panel="hardware" class="nav-link night-nav">Hardware</button>
                <button type="button" data-open-panel="blog-night" class="nav-link night-nav">Blog</button>
                <button type="button" data-open-panel="socials" class="nav-link night-nav">Socials</button>
            </nav>
        </header>

        <nav class="collapsed-only sidebar-nav" aria-label="Night sections (compact)">
            <button type="button" data-open-panel="hardware" class="icon-btn icon-night" data-tooltip="Hardware" aria-label="Hardware">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="2" x2="9" y2="4"/><line x1="15" y1="2" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="22"/><line x1="15" y1="20" x2="15" y2="22"/><line x1="20" y1="9" x2="22" y2="9"/><line x1="20" y1="15" x2="22" y2="15"/><line x1="2" y1="9" x2="4" y2="9"/><line x1="2" y1="15" x2="4" y2="15"/></svg>
            </button>
            <button type="button" data-open-panel="blog-night" class="icon-btn icon-night" data-tooltip="Blog" aria-label="Blog">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button type="button" data-open-panel="socials" class="icon-btn icon-night" data-tooltip="Socials" aria-label="Socials">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            </button>
        </nav>

        <div class="full-only wordmark wordmark-night" aria-hidden="true">
            <span class="wordmark-line"><?php echo esc_html($wordmark_top); ?></span>
            <span class="wordmark-line"><?php echo esc_html($wordmark_bottom); ?></span>
        </div>

        <div class="full-only big-heading">
            <h1 class="text-5xl font-bold tracking-tight md:text-7xl"><?php echo esc_html($night_label); ?></h1>
        </div>

        <div class="full-only side-footer" style="justify-content: flex-end;">
            <div class="font-mono text-xs uppercase tracking-[0.2em] text-night-muted"><?php echo esc_html($night_tagline); ?></div>
        </div>
    </section>

</main>
