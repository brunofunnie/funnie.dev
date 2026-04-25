(() => {
  'use strict';

  // Debug toolbox is hidden unless ?debug=1 is in the URL.
  if (new URLSearchParams(location.search).get('debug') === '1') {
    document.body.classList.add('debug');
  }

  const hero = document.getElementById('hero');
  const daySide = document.getElementById('day-side');
  const nightSide = document.getElementById('night-side');

  function modeForHour(h) { return (h >= 6 && h < 18) ? 'day' : 'night'; }

  // ── Time-driven sky / hills palette
  const SUNRISE_SKY  = ['#fde2c2', '#ffd1a3', '#ffb56b', '#ff9558', '#f0703a'];
  const SUNRISE_HILL = { far: '#d97a4a', mid: '#b85a25', near: '#7a3210', pine: '#3a1606' };
  const DAYLIGHT_SKY  = ['#9fd3ff', '#6fbcef', '#5ea7d8', '#9bd982', '#5ea64e'];
  const DAYLIGHT_HILL = { far: '#5a8c5a', mid: '#3e6e3e', near: '#244824', pine: '#0e2a14' };
  const NIGHT_SKY    = ['#050816', '#0c1238', '#142a5a', '#0e5460', '#0a7864'];
  const NIGHT_HILL   = { far: '#1a2638', mid: '#101a2a', near: '#070d1a', pine: '#020610' };

  function blendHex(a, b, t) {
    const pa = [parseInt(a.slice(1,3),16), parseInt(a.slice(3,5),16), parseInt(a.slice(5,7),16)];
    const pb = [parseInt(b.slice(1,3),16), parseInt(b.slice(3,5),16), parseInt(b.slice(5,7),16)];
    const m = pa.map((v, i) => Math.round(v + (pb[i] - v) * t));
    return '#' + m.map((v) => v.toString(16).padStart(2, '0')).join('');
  }
  function blendArr(a, b, t) { return a.map((c, i) => blendHex(c, b[i], t)); }
  function blendHill(a, b, t) {
    return { far: blendHex(a.far, b.far, t), mid: blendHex(a.mid, b.mid, t), near: blendHex(a.near, b.near, t), pine: blendHex(a.pine, b.pine, t) };
  }

  function applySkyAndHills(hour) {
    let sky, hill, rays;
    // During night hours the day side is collapsed to a sidebar; show the warm
    // sunrise palette there as the default "day" preview.
    if (hour < 6 || hour >= 18) {
      sky = SUNRISE_SKY; hill = SUNRISE_HILL; rays = 1;
    } else if (hour < 8) {
      sky = SUNRISE_SKY; hill = SUNRISE_HILL; rays = 1;
    } else if (hour < 17) {
      sky = DAYLIGHT_SKY; hill = DAYLIGHT_HILL; rays = 0;
    } else { // 17:00 – 17:59 sunset blend toward night
      const t = hour - 17;
      sky = blendArr(DAYLIGHT_SKY, NIGHT_SKY, t);
      hill = blendHill(DAYLIGHT_HILL, NIGHT_HILL, t);
      rays = t;
    }
    const r = document.documentElement.style;
    sky.forEach((c, i) => r.setProperty(`--sky-${i + 1}`, c));
    r.setProperty('--hill-far', hill.far);
    r.setProperty('--hill-mid', hill.mid);
    r.setProperty('--hill-near', hill.near);
    r.setProperty('--hill-pine', hill.pine);
    r.setProperty('--rays-opacity', String(rays));
  }

  function realHour() {
    const d = new Date();
    return d.getHours() + d.getMinutes() / 60;
  }
  function applyTime(t) {
    hero.dataset.time = t;
    document.body.dataset.time = t;
  }
  let timeOverride = false;

  // Position sun/moon along an arc based on hour. Day arc 06–18, night arc 18–06.
  function placeCelestials(hour) {
    const isDay = hour >= 6 && hour < 18;
    // Reset inline so collapsed-mode CSS rules apply for the non-dominant celestial.
    document.querySelector('.sun-wrap').removeAttribute('style');
    document.querySelector('.moon-wrap').removeAttribute('style');
    const wrap = isDay ? document.querySelector('#day-side .sun-wrap')
                       : document.querySelector('#night-side .moon-wrap');
    if (!wrap) return;
    const f = isDay
      ? (hour - 6) / 12
      : ((hour - 18 + 24) % 24) / 12;
    const xPct = 8 + f * 80;             // 8% (rise) to 88% (set)
    const yPct = 70 - Math.sin(Math.PI * f) * 62; // 70% horizon, 8% peak
    wrap.style.left = `calc(${xPct}% - 110px)`;
    wrap.style.top = `${yPct}%`;
    wrap.style.right = 'auto';
  }

  // Render moon phase from a date. 0=new, 0.5=full.
  function moonPhase(date = new Date()) {
    const ref = Date.UTC(2000, 0, 6, 18, 14, 0);  // known new moon
    const lunar = 29.530588853 * 86400000;
    const diff = date.getTime() - ref;
    return ((diff % lunar) + lunar) % lunar / lunar;
  }
  function phaseName(p) {
    if (p < 0.03 || p > 0.97) return 'New';
    if (p < 0.22) return 'Waxing crescent';
    if (p < 0.28) return 'First quarter';
    if (p < 0.47) return 'Waxing gibbous';
    if (p < 0.53) return 'Full';
    if (p < 0.72) return 'Waning gibbous';
    if (p < 0.78) return 'Last quarter';
    return 'Waning crescent';
  }
  function applyMoonPhase(phase) {
    let x;
    if (phase < 0.5) x = -phase * 200;          // waxing: shadow leaves to left
    else             x = (1 - phase) * 200;     // waning: shadow returns from right
    const moon = document.getElementById('moon');
    if (moon) moon.style.setProperty('--moon-shadow-x', x + '%');
    const display = document.getElementById('phase-display');
    if (display) display.textContent = phaseName(phase);
  }

  // Slider drives simulated hour (and optionally overrides clock time).
  const slider = document.getElementById('time-slider');
  const timeDisplay = document.getElementById('time-display');
  function fmtClock(h) {
    const hh = Math.floor(h);
    const mm = Math.floor((h - hh) * 60);
    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
  }
  function setHour(h, isOverride) {
    if (timeDisplay) timeDisplay.textContent = fmtClock(h);
    applyTime(modeForHour(h));
    placeCelestials(h);
    applySkyAndHills(h);
    if (isOverride) timeOverride = true;
  }

  // Initialize
  const initHour = realHour();
  if (slider) slider.value = initHour;
  setHour(initHour, false);
  const initPhase = moonPhase();
  applyMoonPhase(initPhase);
  const phaseSlider = document.getElementById('phase-slider');
  if (phaseSlider) {
    phaseSlider.value = initPhase;
    phaseSlider.addEventListener('input', () => applyMoonPhase(parseFloat(phaseSlider.value)));
  }

  if (slider) {
    slider.addEventListener('input', () => setHour(parseFloat(slider.value), true));
  }

  // Re-sync to clock every minute (only if the user hasn't taken control).
  setInterval(() => {
    if (timeOverride) return;
    const h = realHour();
    if (slider) slider.value = h;
    setHour(h, false);
  }, 60_000);

  // Clicking the collapsed celestial swaps day/night by jumping the slider.
  const sunWrap = daySide.querySelector('.sun-wrap');
  const moonWrap = nightSide.querySelector('.moon-wrap');

  // Clicking sun/moon: if real time matches the destination mode, snap to the
  // real-hour position and release the manual override; otherwise drop the
  // celestial in a sensible default position (noon for day, midnight for night)
  // and keep the override so the minute-tick won't yank it back.
  sunWrap.addEventListener('click', () => {
    if (hero.dataset.time !== 'night') return;
    const real = realHour();
    const realIsDay = real >= 6 && real < 18;
    const h = realIsDay ? real : 12;
    if (slider) slider.value = h;
    setHour(h, !realIsDay);
    if (realIsDay) timeOverride = false;
  });
  moonWrap.addEventListener('click', () => {
    if (hero.dataset.time !== 'day') return;
    const real = realHour();
    const realIsNight = real >= 18 || real < 6;
    const h = realIsNight ? real : 0;
    if (slider) slider.value = h;
    setHour(h, !realIsNight);
    if (realIsNight) timeOverride = false;
  });
  [sunWrap, moonWrap].forEach((el) => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });


  const backdrop = document.getElementById('panel-backdrop');
  const panels = {
    about: document.getElementById('panel-about'),
    resume: document.getElementById('panel-resume'),
    hardware: document.getElementById('panel-hardware'),
    'blog-day': document.getElementById('panel-blog-day'),
    'blog-night': document.getElementById('panel-blog-night'),
    socials: document.getElementById('panel-socials'),
  };
  let activePanelId = null;
  let lastTrigger = null;

  function openPanel(id, trigger) {
    if (activePanelId) closePanel({ restoreFocus: false });
    const panel = panels[id];
    if (!panel) return;
    lastTrigger = trigger || null;
    // Blog is the only panel reachable from both sides — retheme to match current mode.
    if (id === 'blog') {
      const isDay = document.body.dataset.time === 'day';
      panel.classList.toggle('panel-day', isDay);
      panel.classList.toggle('panel-night', !isDay);
      panel.dataset.side = isDay ? 'day' : 'night';
    }
    panel.hidden = false;
    requestAnimationFrame(() => panel.classList.add('is-open'));
    backdrop.classList.add('is-visible');
    activePanelId = id;
    const closeBtn = panel.querySelector('.panel-close');
    if (closeBtn) closeBtn.focus();
  }

  function closePanel({ restoreFocus = true } = {}) {
    if (!activePanelId) return;
    const panel = panels[activePanelId];
    panel.classList.remove('is-open');
    backdrop.classList.remove('is-visible');
    const onEnd = () => {
      panel.hidden = true;
      panel.removeEventListener('transitionend', onEnd);
    };
    panel.addEventListener('transitionend', onEnd);

    if (panel.matches('[data-blog-panel]')) {
      const blogBody = panel.querySelector('.panel-body');
      const listSection = blogBody.querySelector('[data-blog-section="list"]');
      const detailSection = blogBody.querySelector('[data-blog-section="detail"]');
      blogBody.dataset.blogView = 'list';
      if (listSection) listSection.hidden = false;
      if (detailSection) detailSection.hidden = true;
    }

    activePanelId = null;
    if (restoreFocus && lastTrigger) lastTrigger.focus();
    lastTrigger = null;
  }

  document.querySelectorAll('[data-open-panel]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      openPanel(e.currentTarget.dataset.openPanel, e.currentTarget);
    });
  });

  document.querySelectorAll('.panel-close').forEach((btn) => {
    btn.addEventListener('click', () => closePanel());
  });

  backdrop.addEventListener('click', () => closePanel());

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && activePanelId) closePanel();
  });

  document.querySelectorAll('[data-blog-panel]').forEach((blogPanel) => {
    const side = blogPanel.dataset.side || 'night';
    const blogBody = blogPanel.querySelector('.panel-body');
    const listSection = blogBody.querySelector('[data-blog-section="list"]');
    const detailSection = blogBody.querySelector('[data-blog-section="detail"]');
    const detailContent = blogBody.querySelector('[data-blog-detail-content]');
    const backBtn = blogBody.querySelector('[data-blog-back]');
    const postsRaw = blogPanel.querySelector('script[data-blog-posts]');
    const inlinePosts = postsRaw ? JSON.parse(postsRaw.textContent) : {};
    const restUrl = postsRaw ? postsRaw.dataset.restUrl : '';
    const cache = new Map();

    function renderDetail(post) {
      detailContent.innerHTML = `
        <div class="font-mono text-xs uppercase tracking-[0.2em] text-${side}-muted">${post.date}</div>
        <h3 class="mt-1 text-3xl font-bold tracking-tight">${post.title}</h3>
        <div class="mt-6 space-y-4 text-base leading-relaxed text-${side}-text">${post.body}</div>
      `;
    }

    function showBlogList() {
      blogBody.dataset.blogView = 'list';
      detailSection.hidden = true;
      listSection.hidden = false;
    }

    async function showBlogPost(id) {
      blogBody.dataset.blogView = 'detail';
      listSection.hidden = true;
      detailSection.hidden = false;
      backBtn.focus();

      if (cache.has(id)) {
        renderDetail(cache.get(id));
        return;
      }

      const inline = inlinePosts[id];
      if (inline && inline.body) {
        cache.set(id, inline);
        renderDetail(inline);
        return;
      }

      if (!restUrl) return;

      detailContent.innerHTML = `<p class="text-${side}-muted">Loading…</p>`;

      try {
        const res = await fetch(`${restUrl}/${encodeURIComponent(id)}?_fields=date,title,content`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        const post = {
          date: (data.date || '').slice(0, 10),
          title: data.title?.rendered || '',
          body: data.content?.rendered || '',
        };
        cache.set(id, post);
        renderDetail(post);
      } catch (err) {
        detailContent.innerHTML = `<p class="text-${side}-muted">Could not load this post.</p>`;
      }
    }

    blogBody.querySelectorAll('[data-blog-open]').forEach((btn) => {
      btn.addEventListener('click', (e) => showBlogPost(e.currentTarget.dataset.blogOpen));
    });
    if (backBtn) backBtn.addEventListener('click', showBlogList);
  });

  const discordModal = document.getElementById('discord-modal');
  document.querySelectorAll('[data-discord-open]').forEach((btn) => {
    btn.addEventListener('click', () => {
      discordModal.hidden = false;
      discordModal.querySelector('[data-discord-close]').focus();
    });
  });
  document.querySelectorAll('[data-discord-close]').forEach((btn) => {
    btn.addEventListener('click', () => {
      discordModal.hidden = true;
    });
  });
  discordModal.addEventListener('click', (e) => {
    if (e.target === discordModal) discordModal.hidden = true;
  });

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-copy-text]');
    if (!btn) return;
    const text = btn.dataset.copyText;
    try {
      await navigator.clipboard.writeText(text);
      const original = btn.textContent;
      btn.dataset.copied = 'true';
      btn.textContent = 'Copied';
      setTimeout(() => {
        btn.dataset.copied = 'false';
        btn.textContent = original;
      }, 1600);
    } catch (err) {
      console.warn('clipboard write failed', err);
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !discordModal.hidden) {
      discordModal.hidden = true;
    }
  });

  // ── Weather (Open-Meteo, no API key). Swap to Google Weather API by changing
  // the URL and reading the `currentConditions` field instead of `current_weather`.
  // WMO weather code → { bucket, intensity (0..3), icon, label }
  function classifyWeather(code) {
    const c = Number(code);
    if (c === 0)  return { bucket: 'clear',         intensity: 0, icon: '☀', label: 'Clear' };
    if (c === 1)  return { bucket: 'mainly-clear',  intensity: 0, icon: '🌤', label: 'Mainly clear' };
    if (c === 2)  return { bucket: 'partly-cloudy', intensity: 1, icon: '⛅', label: 'Partly cloudy' };
    if (c === 3)  return { bucket: 'overcast',      intensity: 3, icon: '☁', label: 'Overcast' };
    if (c === 45) return { bucket: 'fog',           intensity: 1, icon: '🌫', label: 'Fog' };
    if (c === 48) return { bucket: 'rime',          intensity: 2, icon: '🌫', label: 'Rime fog' };
    if (c === 51) return { bucket: 'drizzle-1',     intensity: 1, icon: '🌦', label: 'Light drizzle' };
    if (c === 53) return { bucket: 'drizzle-2',     intensity: 2, icon: '🌦', label: 'Drizzle' };
    if (c === 55) return { bucket: 'drizzle-3',     intensity: 3, icon: '🌦', label: 'Dense drizzle' };
    if (c === 56) return { bucket: 'drizzle-1',     intensity: 1, icon: '🌧', label: 'Freezing drizzle' };
    if (c === 57) return { bucket: 'drizzle-3',     intensity: 3, icon: '🌧', label: 'Freezing drizzle' };
    if (c === 61) return { bucket: 'rain-1',        intensity: 1, icon: '🌧', label: 'Light rain' };
    if (c === 63) return { bucket: 'rain-2',        intensity: 2, icon: '🌧', label: 'Rain' };
    if (c === 65) return { bucket: 'rain-3',        intensity: 3, icon: '🌧', label: 'Heavy rain' };
    if (c === 66) return { bucket: 'rain-1',        intensity: 1, icon: '🌧', label: 'Freezing rain' };
    if (c === 67) return { bucket: 'rain-3',        intensity: 3, icon: '🌧', label: 'Freezing rain' };
    if (c === 71) return { bucket: 'snow-1',        intensity: 1, icon: '🌨', label: 'Light snow' };
    if (c === 73) return { bucket: 'snow-2',        intensity: 2, icon: '🌨', label: 'Snow' };
    if (c === 75) return { bucket: 'snow-3',        intensity: 3, icon: '❄', label: 'Heavy snow' };
    if (c === 77) return { bucket: 'snow-1',        intensity: 1, icon: '❄', label: 'Snow grains' };
    if (c === 80) return { bucket: 'showers-1',     intensity: 1, icon: '🌧', label: 'Light showers' };
    if (c === 81) return { bucket: 'showers-2',     intensity: 2, icon: '🌧', label: 'Showers' };
    if (c === 82) return { bucket: 'showers-3',     intensity: 3, icon: '🌧', label: 'Violent showers' };
    if (c === 85) return { bucket: 'snow-1',        intensity: 1, icon: '🌨', label: 'Snow showers' };
    if (c === 86) return { bucket: 'snow-3',        intensity: 3, icon: '❄', label: 'Snow showers' };
    if (c >= 95)  return { bucket: 'storm',         intensity: 3, icon: '⛈', label: 'Thunderstorm' };
    return { bucket: 'clear', intensity: 0, icon: '·', label: '—' };
  }

  function spawnParticles(container, count, build) {
    container.innerHTML = '';
    for (let i = 0; i < count; i++) container.appendChild(build(i));
  }
  function buildRain(intensity) {
    const drop = document.createElement('span');
    drop.className = 'rain-drop';
    drop.style.left = (Math.random() * 102 - 1) + '%';
    drop.style.animationDuration = (0.5 + Math.random() * 0.5 - intensity * 0.05) + 's';
    drop.style.animationDelay = -Math.random() * 1.2 + 's';
    drop.style.opacity = (0.4 + Math.random() * 0.5);
    drop.style.height = (8 + intensity * 3 + Math.random() * 6) + 'px';
    return drop;
  }
  function buildSnow() {
    const flake = document.createElement('span');
    flake.className = 'snow-flake';
    flake.style.left = (Math.random() * 102 - 1) + '%';
    flake.style.animationDuration = (5 + Math.random() * 5) + 's';
    flake.style.animationDelay = -Math.random() * 6 + 's';
    flake.style.opacity = (0.4 + Math.random() * 0.6);
    const size = 2 + Math.random() * 4;
    flake.style.width = size + 'px';
    flake.style.height = size + 'px';
    return flake;
  }
  function applyWeather(code) {
    const info = classifyWeather(code);
    document.body.dataset.weather = info.bucket;

    // Update widget (icon + condition label). Temp is set separately by fetchWeather.
    const widget = document.querySelector('.weather[data-weather]');
    if (widget) {
      widget.querySelector('.weather-icon').textContent = info.icon;
      widget.querySelector('.weather-condition').textContent = info.label;
    }

    // Particles. Counts scale with intensity; storm and showers get extra.
    const rainHost = document.querySelector('.fx-rain');
    const snowHost = document.querySelector('.fx-snow');
    const isRainBucket = /^(drizzle|rain|showers)-/.test(info.bucket) || info.bucket === 'storm';
    const isSnowBucket = /^snow-/.test(info.bucket);
    if (rainHost) {
      if (isRainBucket) {
        const base = info.bucket.startsWith('drizzle') ? 30
                  : info.bucket === 'storm'           ? 240
                  : info.bucket.startsWith('showers') ? 60 + info.intensity * 50
                  :                                     50 + info.intensity * 60;
        spawnParticles(rainHost, base, () => buildRain(info.intensity));
      } else {
        rainHost.innerHTML = '';
      }
    }
    if (snowHost) {
      if (isSnowBucket) spawnParticles(snowHost, 30 + info.intensity * 35, buildSnow);
      else snowHost.innerHTML = '';
    }
  }

  function renderWeather(temp, code) {
    applyWeather(code);
    const widget = document.querySelector('.weather[data-weather]');
    if (widget) widget.querySelector('.weather-temp').textContent = Math.round(temp) + '°';
  }
  let lastApiCode = 0;
  function fetchWeather(lat, lon) {
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;
    fetch(url).then((r) => r.json()).then((data) => {
      const cw = data && data.current_weather;
      if (!cw) return;
      lastApiCode = cw.weathercode;
      const override = document.getElementById('weather-override');
      // Don't clobber a manual override; just remember the live code.
      if (!override || override.value === '') renderWeather(cw.temperature, cw.weathercode);
      else {
        const w = document.querySelector('.weather[data-weather]');
        if (w) w.querySelector('.weather-temp').textContent = Math.round(cw.temperature) + '°';
      }
    }).catch((err) => console.warn('weather fetch failed', err));
  }

  const weatherOverride = document.getElementById('weather-override');
  if (weatherOverride) {
    weatherOverride.addEventListener('change', () => {
      if (weatherOverride.value === '') applyWeather(lastApiCode);
      else applyWeather(parseInt(weatherOverride.value, 10));
    });
  }

  // Default location: Sorocaba, São Paulo, Brazil.
  fetchWeather(-23.5015, -47.4526);

  // ---- Animated birds (day side only) ----
  const birdsContainer = document.querySelector('.birds');
  function rand(min, max) { return Math.random() * (max - min) + min; }

  function spawnBird() {
    if (!birdsContainer) return;
    if (document.body.dataset.time !== 'day') return;
    if (document.hidden) return;

    const rtl = Math.random() < 0.4;
    const flight = document.createElement('div');
    flight.className = 'bird-flight' + (rtl ? ' rtl' : '');
    const top = rand(8, 55);                 // % from top — sky band
    const duration = rand(10, 20);           // seconds across screen
    const drift = rand(-90, 30);             // px vertical drift over flight
    const scale = rand(0.55, 1.15);
    const flap = rand(180, 320);             // ms flap rate (smaller = faster)
    flight.style.setProperty('--bird-top', top + '%');
    flight.style.setProperty('--bird-duration', duration + 's');
    flight.style.setProperty('--bird-drift', drift + 'px');

    const bird = document.createElement('div');
    bird.className = 'bird';
    bird.style.setProperty('--bird-scale', String(scale));

    // Inline SVG: a single V-shape bird; flap via CSS scaleY on the SVG.
    bird.innerHTML = `
      <svg class="bird-svg" width="28" height="14" viewBox="0 0 28 14" fill="none"
           style="animation-duration:${flap}ms">
        <path d="M2 11 Q8 2, 14 11 Q20 2, 26 11" stroke="#3a1a08"
              stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      </svg>`;
    flight.appendChild(bird);

    flight.addEventListener('animationend', () => flight.remove());
    birdsContainer.appendChild(flight);
  }

  function maybeSpawnFlock() {
    if (document.body.dataset.time !== 'day') return;
    // Occasionally spawn a small flock of 2–4 staggered birds.
    const count = Math.random() < 0.25 ? Math.floor(rand(2, 5)) : 1;
    for (let i = 0; i < count; i++) {
      setTimeout(spawnBird, i * rand(200, 700));
    }
  }

  function scheduleBirds() {
    const next = rand(2500, 9000);
    setTimeout(() => {
      if (Math.random() < 0.7) maybeSpawnFlock();
      scheduleBirds();
    }, next);
  }
  scheduleBirds();

  // ---- Shooting stars (night side only) ----
  const shootingContainer = document.querySelector('.shooting-stars');

  function spawnShootingStar() {
    if (!shootingContainer) return;
    if (document.body.dataset.time !== 'night') return;
    if (document.hidden) return;

    const star = document.createElement('span');
    star.className = 'shooting-star';
    // Start high in the sky on the right; fade out before reaching the city.
    const top = rand(2, 18);
    const left = rand(55, 95);
    const tilt = rand(15, 35);                  // degrees below horizontal — shallower arc
    const finalAngle = 180 - tilt;              // rotated +X points down-left (CSS Y is down)
    const duration = rand(900, 1600);           // ms
    const distance = rand(35, 60);              // vw — positive: head moves along rotated +X
    const tail = rand(90, 180);                 // px tail length

    star.style.setProperty('--ss-top', top + '%');
    star.style.setProperty('--ss-left', left + '%');
    star.style.setProperty('--ss-angle', finalAngle + 'deg');
    star.style.setProperty('--ss-duration', duration + 'ms');
    star.style.setProperty('--ss-distance', distance + 'vw');
    star.style.setProperty('--ss-tail', tail + 'px');

    star.addEventListener('animationend', () => star.remove());
    shootingContainer.appendChild(star);
  }

  function scheduleShootingStars() {
    const next = rand(4000, 14000);
    setTimeout(() => {
      if (Math.random() < 0.65) {
        spawnShootingStar();
        // Occasional double (meteor shower hint)
        if (Math.random() < 0.15) setTimeout(spawnShootingStar, rand(180, 600));
      }
      scheduleShootingStars();
    }, next);
  }
  scheduleShootingStars();

  // ---- Custom lightsaber cursor ----
  // Browsers cap CSS cursor: url(...) at ~128px, so the original 310x310 GIFs
  // are rendered as a position:fixed <div> that follows the mouse instead.
  // Both idle + ignited images live in the DOM stacked on top of each other;
  // we toggle a class to fade between them (no src swap → no GIF reload flash).
  const saber = document.querySelector('.cursor-saber');
  if (saber && matchMedia('(hover: hover)').matches) {
    // Source idle GIF: visible saber bounding box starts at (34, 17) of 310px.
    // At 64px display that's ~(7, 4); the rounded blade tip's center sits a
    // few pixels further in, around (10, 5). Subtracting these offsets puts
    // that exact pixel directly under the system mouse position.
    const HOTSPOT_X = 10;
    const HOTSPOT_Y = 5;
    const POINTER_SEL = 'button, a, [role="button"], .nav-link, .panel-close, [data-open-panel], .sun-wrap, .moon-wrap';

    function setActive(active) {
      saber.classList.toggle('is-active', active);
    }

    document.addEventListener('mousemove', (e) => {
      saber.style.transform = `translate3d(${e.clientX - HOTSPOT_X}px, ${e.clientY - HOTSPOT_Y}px, 0)`;
      saber.classList.add('is-visible');
      setActive(!!e.target.closest(POINTER_SEL));
    }, { passive: true });

    document.addEventListener('mouseleave', () => saber.classList.remove('is-visible'));
    document.addEventListener('mouseenter', () => saber.classList.add('is-visible'));

    // Mousedown briefly intensifies the blade (always ignited while pressed).
    document.addEventListener('mousedown', () => setActive(true));
    document.addEventListener('mouseup',   (e) => setActive(!!e.target.closest(POINTER_SEL)));
  }
})();
