<?php if (!defined('ABSPATH')) exit; ?>
<aside class="debug-box" role="region" aria-label="Debug controls">
    <div class="debug-section">
        <div class="debug-label">Weather · Sorocaba</div>
        <div class="weather" data-weather aria-live="polite">
            <span class="weather-icon" aria-hidden="true">·</span>
            <span class="weather-temp">--°</span>
            <span class="weather-condition">…</span>
        </div>
    </div>
    <?php if (is_front_page()): /* Hour slider drives day↔night swap on the
        hero — hidden on other pages where the side is locked by post category. */ ?>
    <div class="debug-section">
        <div class="debug-row">
            <span class="debug-label">Hour</span>
            <span class="debug-value" id="time-display">--:--</span>
        </div>
        <input type="range" id="time-slider" min="0" max="23.999" step="0.05" value="12" aria-label="Hour of the day" />
    </div>
    <?php endif; ?>
    <div class="debug-section">
        <div class="debug-row">
            <span class="debug-label">Moon phase</span>
            <span class="debug-value" id="phase-display">—</span>
        </div>
        <input type="range" id="phase-slider" min="0" max="1" step="0.01" value="0.5" aria-label="Moon phase" />
    </div>
    <div class="debug-section">
        <div class="debug-row">
            <span class="debug-label">Weather sim</span>
        </div>
        <select id="weather-override" class="debug-select" aria-label="Override weather">
            <option value="">Auto (live)</option>
            <option value="0">Clear sky (0)</option>
            <option value="1">Mainly clear (1)</option>
            <option value="2">Partly cloudy (2)</option>
            <option value="3">Overcast (3)</option>
            <option value="45">Fog (45)</option>
            <option value="48">Rime fog (48)</option>
            <option value="51">Light drizzle (51)</option>
            <option value="53">Moderate drizzle (53)</option>
            <option value="55">Dense drizzle (55)</option>
            <option value="61">Light rain (61)</option>
            <option value="63">Moderate rain (63)</option>
            <option value="65">Heavy rain (65)</option>
            <option value="71">Light snow (71)</option>
            <option value="73">Moderate snow (73)</option>
            <option value="75">Heavy snow (75)</option>
            <option value="80">Slight showers (80)</option>
            <option value="81">Moderate showers (81)</option>
            <option value="82">Violent showers (82)</option>
            <option value="95">Thunderstorm (95)</option>
        </select>
    </div>
</aside>
