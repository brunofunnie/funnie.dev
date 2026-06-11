(() => {
  'use strict';

  // Debug toolbox is hidden unless ?debug=1 is in the URL.
  if (new URLSearchParams(location.search).get('debug') === '1') {
    document.body.classList.add('debug');
  }

  const hero = document.getElementById('hero');
  const daySide = document.getElementById('day-side');
  const nightSide = document.getElementById('night-side');

  // Last weather code returned by the API. Declared up here (before the
  // no-hero early-exit) so async fetchWeather callbacks can write to it on
  // single-post pages without hitting the temporal dead zone.
  let lastApiCode = 0;

  // Reusable lightsaber cursor — runs on every page that includes
  // <div class="cursor-saber"> (rendered by footer.php). Browsers cap CSS
  // cursor: url(...) at ~128px, so we render the original 310x310 GIFs as a
  // position:fixed overlay that follows the mouse. Idle + ignited images
  // live in the DOM stacked on top of each other; we toggle a class to fade
  // between them so there's no src-swap GIF reload flash.
  function initCursorSaber() {
    const saber = document.querySelector('.cursor-saber');
    if (!saber || !matchMedia('(hover: hover)').matches) return;
    // Source idle GIF: visible saber bounding box starts at (34, 17) of 310px.
    // At 64px display that's ~(7, 4); the rounded blade tip's center sits a
    // few pixels further in, around (10, 5). Subtracting these offsets puts
    // that exact pixel directly under the system mouse position.
    const HOTSPOT_X = 10;
    const HOTSPOT_Y = 5;
    const POINTER_SEL = 'button, a, [role="button"], .nav-link, [data-open-panel], [data-scroll-to], .sun-wrap, .moon-wrap';

    const setActive = (active) => saber.classList.toggle('is-active', active);

    // `e.target` is always an Element for real DOM mouse events, but be
    // defensive against synthetic dispatches (e.g. tests, devtools) where it
    // might land on `document` and lack `.closest`.
    const overPointer = (e) => !!(e.target && typeof e.target.closest === 'function' && e.target.closest(POINTER_SEL));

    document.addEventListener('mousemove', (e) => {
      saber.style.transform = `translate3d(${e.clientX - HOTSPOT_X}px, ${e.clientY - HOTSPOT_Y}px, 0)`;
      saber.classList.add('is-visible');
      setActive(overPointer(e));
    }, { passive: true });

    document.addEventListener('mouseleave', () => saber.classList.remove('is-visible'));
    document.addEventListener('mouseenter', () => saber.classList.add('is-visible'));
    document.addEventListener('mousedown', () => setActive(true));
    document.addEventListener('mouseup',   (e) => setActive(overPointer(e)));
  }
  initCursorSaber();

  function modeForHour(h) { return (h >= 6 && h < 18) ? 'day' : 'night'; }

  // ── Time-driven sky / hills palette
  const SUNRISE_SKY  = ['#fde2c2', '#ffd1a3', '#ffb56b', '#ff9558', '#f0703a'];
  const SUNRISE_HILL = { far: '#6fa66b', mid: '#4a8950', near: '#2e5d34', pine: '#163020' };
  const DAYLIGHT_SKY  = ['#9fd3ff', '#6fbcef', '#5ea7d8', '#9bd982', '#5ea64e'];
  const DAYLIGHT_HILL = { far: '#6fa66b', mid: '#4a8950', near: '#2e5d34', pine: '#0f2114' };
  const NIGHT_SKY    = ['#050816', '#0c1238', '#142a5a', '#0e5460', '#0a7864'];
  // Night hills stay in the green family (deep forest) so the sunset transition
  // shifts from bright green → dark green rather than green → blue-grey.
  const NIGHT_HILL   = { far: '#1f3a26', mid: '#15291a', near: '#0c1c10', pine: '#040c06' };

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
    // During night hours the day side is collapsed to a sidebar; show the
    // daylight palette there so the mini scene mirrors the full day-side
    // (same blue sky + green hills) instead of jumping to a dawn preview.
    if (hour < 6 || hour >= 18) {
      sky = DAYLIGHT_SKY; hill = DAYLIGHT_HILL; rays = 0;
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
  // Swap the favicon to the day or night icon. URLs are passed in via the
  // `FUNNIE` localized object so the theme directory isn't hardcoded here.
  // Falls through silently if the link or URLs are missing (e.g. theme
  // mis-enqueued in a child theme override).
  function updateFavicon(mode) {
    const link = document.getElementById('favicon');
    const cfg = window.FUNNIE || {};
    const href = mode === 'night' ? cfg.nightIcon : cfg.dayIcon;
    if (link && href) link.href = href;
  }

  function applyTime(t) {
    hero.dataset.time = t;
    document.body.dataset.time = t;
    updateFavicon(t);
  }
  let timeOverride = false;

  // Position sun/moon along an arc based on hour. Day arc 06–18, night arc 18–06.
  // On mobile (<=767px) the arc is skipped — CSS centers the active celestial
  // and we just clear any stale inline style so the CSS rules win cleanly.
  // The "big" celestial is picked from `hero.dataset.time` (which side is
  // expanded), NOT from `hour` — so the user can flip sides via hash or click
  // and we still position the right wrap. When hero mode disagrees with the
  // natural mode for `hour` (user is viewing the opposite side of the clock),
  // the big celestial sits in a neutral centered/near-top default instead of
  // an arbitrary arc point.
  const isMobileView = () => window.matchMedia('(max-width: 767px)').matches;
  function placeCelestials(hour) {
    const sunWrap = document.querySelector('.sun-wrap');
    const moonWrap = document.querySelector('.moon-wrap');
    if (!sunWrap || !moonWrap) return;
    // Always reset inline so collapsed-mode CSS rules apply for the small
    // sidebar celestial (and so mobile gets a clean slate for the CSS
    // centering rule).
    sunWrap.removeAttribute('style');
    moonWrap.removeAttribute('style');
    if (isMobileView()) return;

    const isDayHour = hour >= 6 && hour < 18;
    const big = hero.dataset.time === 'day' ? sunWrap : moonWrap;
    const matchesNatural = (hero.dataset.time === 'day') === isDayHour;

    if (matchesNatural) {
      const f = isDayHour
        ? (hour - 6) / 12
        : ((hour - 18 + 24) % 24) / 12;
      const xPct = 8 + f * 80;             // 8% (rise) to 88% (set)
      const yPct = 70 - Math.sin(Math.PI * f) * 62; // 70% horizon, 8% peak
      big.style.left = `calc(${xPct}% - 110px)`;
      big.style.top = `${yPct}%`;
      big.style.right = 'auto';
    } else {
      // Default position: horizontally centered, near the top of the panel.
      big.style.left = '50%';
      big.style.top = '8%';
      big.style.right = 'auto';
      big.style.transform = 'translateX(-50%)';
    }
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
    // Set on :root so every moon-phase-shadow on the page picks it up via
    // CSS variable inheritance — the hero moon AND the sticky-bar mini moon.
    document.documentElement.style.setProperty('--moon-shadow-x', x + '%');
    const display = document.getElementById('phase-display');
    if (display) display.textContent = phaseName(phase);
  }

  // Pages without the hero (single posts, archives, search…) get a stripped-
  // down setup: real-clock sky/hill palette, current moon phase, weather
  // particles inside the sticky bar, and smooth scroll for in-page anchors.
  // Placed after the palette consts and helper functions are initialized so
  // applySkyAndHills/fetchWeather don't hit the temporal dead zone.
  if (!hero) {
    // body[data-time] is set by single.php's inline script before main.js
    // runs, so we just sync the favicon to whatever side the post locked us
    // to. Front-page handles this through applyTime() instead.
    updateFavicon(document.body.dataset.time || 'day');
    const initHourNH = realHour();
    applySkyAndHills(initHourNH);
    const initPhaseNH = moonPhase();
    applyMoonPhase(initPhaseNH);
    fetchWeather(-23.5015, -47.4526);

    // Debug toolbox controls — same wiring as the front page, minus the
    // celestial arc placement (no #hero on these pages). Sky tones, moon
    // shadow, and weather all flow through CSS vars / body[data-weather],
    // so the bar + footer scenery react live to the toolbox sliders.
    const sliderNH       = document.getElementById('time-slider');
    const timeDisplayNH  = document.getElementById('time-display');
    const phaseSliderNH  = document.getElementById('phase-slider');
    const weatherOverNH  = document.getElementById('weather-override');
    function fmtClockNH(h) {
      const hh = Math.floor(h);
      const mm = Math.floor((h - hh) * 60);
      return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
    }
    if (timeDisplayNH) timeDisplayNH.textContent = fmtClockNH(initHourNH);
    if (sliderNH) {
      sliderNH.value = initHourNH;
      sliderNH.addEventListener('input', () => {
        const h = parseFloat(sliderNH.value);
        if (timeDisplayNH) timeDisplayNH.textContent = fmtClockNH(h);
        applySkyAndHills(h);
      });
    }
    if (phaseSliderNH) {
      phaseSliderNH.value = initPhaseNH;
      phaseSliderNH.addEventListener('input', () => applyMoonPhase(parseFloat(phaseSliderNH.value)));
    }
    if (weatherOverNH) {
      weatherOverNH.addEventListener('change', () => {
        if (weatherOverNH.value === '') applyWeather(lastApiCode);
        else applyWeather(parseInt(weatherOverNH.value, 10));
      });
    }

    document.querySelectorAll('[data-open-panel], [data-scroll-to]').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        const id = e.currentTarget.dataset.openPanel || e.currentTarget.dataset.scrollTo;
        if (!id) return;
        const target = document.getElementById(id);
        if (!target) return; // let the browser handle off-page hash links
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
    return;
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

  // Side from URL hash: only opens the requested side. The browser
  // handles the scroll natively. Sky / sun / moon / timeOverride are
  // strictly off-limits here — the existing sun/moon click handlers
  // already own "manual side switch" semantics.
  const SIDE_BY_HASH = {
    '#blog-day': 'day',  '#about':    'day',  '#resume':  'day',
    '#blog-night': 'night', '#hardware': 'night', '#socials': 'night',
  };
  function syncSideFromHash() {
    const side = SIDE_BY_HASH[(location.hash || '').toLowerCase()];
    if (!side) return;
    applyTime(side);
    // Re-place celestials so the sidebar small one and the big one match the
    // new mode. Without this, stale inline arc styles leave the sidebar
    // moon (or sun) at a random arc position when the hash flips us across
    // sides without going through setHour.
    placeCelestials(slider ? parseFloat(slider.value) : realHour());
  }
  syncSideFromHash();
  window.addEventListener('hashchange', syncSideFromHash);

  // Re-sync to clock every minute (only if the user hasn't taken control).
  setInterval(() => {
    if (timeOverride) return;
    const h = realHour();
    if (slider) slider.value = h;
    setHour(h, false);
  }, 60_000);

  // Re-place the celestial when crossing the mobile breakpoint so the
  // mobile-centered position and the desktop arc swap cleanly on resize.
  window.addEventListener('resize', () => {
    placeCelestials(slider ? parseFloat(slider.value) : realHour());
  });

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


  // In-page navigation: clicking any [data-open-panel] or [data-scroll-to]
  // smooth-scrolls to the section whose id matches the value. The "panel"
  // attribute name is kept on the hero buttons for backwards compatibility
  // with the previous modal-based markup; it now means "scroll target id".
  function scrollToSection(id) {
    if (!id) return;
    const target = id === 'hero'
      ? document.getElementById('hero') || document.body
      : document.getElementById(id);
    if (!target) return;
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  document.querySelectorAll('[data-open-panel], [data-scroll-to]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const id = e.currentTarget.dataset.openPanel || e.currentTarget.dataset.scrollTo;
      // For real anchor tags let the browser handle hash navigation too —
      // but smooth-scroll first to override the default jump.
      if (e.currentTarget.tagName === 'A') e.preventDefault();
      scrollToSection(id);
    });
  });

  // Reveal the sticky bar when the hero's side-header has scrolled out of
  // view. Threshold is recomputed from the currently-visible side-header
  // (the inactive side's header is display:none, so we pick whichever has
  // a layout box). Recomputed on resize and on day↔night swap.
  function getHeaderThreshold() {
    const headers = document.querySelectorAll('.side-header.full-only');
    let visible = null;
    headers.forEach((h) => { if (h.offsetParent !== null) visible = h; });
    if (!visible) return 100;
    const r = visible.getBoundingClientRect();
    return Math.max(0, Math.round(r.bottom + window.scrollY));
  }
  let heroThreshold = getHeaderThreshold();
  function updateHeroPassed() {
    const passed = window.scrollY > heroThreshold;
    document.body.classList.toggle('hero-passed', passed);
  }
  window.addEventListener('scroll', updateHeroPassed, { passive: true });
  window.addEventListener('resize', () => {
    heroThreshold = getHeaderThreshold();
    updateHeroPassed();
  });
  // Recompute when the time mode flips, since which header has layout changes.
  const heroEl = document.getElementById('hero');
  if (heroEl) {
    new MutationObserver(() => {
      heroThreshold = getHeaderThreshold();
      updateHeroPassed();
    }).observe(heroEl, { attributes: true, attributeFilter: ['data-time'] });
  }
  updateHeroPassed();

  // Scroll-spy: highlight the bar nav button matching the section the user
  // is reading. Rule: the section whose top has most recently crossed the
  // sticky-bar bottom (~150px from viewport top) wins. The inactive
  // content-block has display:none so its sections have no layout — they're
  // skipped automatically.
  const SCROLLSPY_OFFSET = 150;
  const barLinks = Array.from(document.querySelectorAll('.bar-nav .nav-link[data-scroll-to]'));
  const sectionTargets = [];
  barLinks.forEach((link) => {
    const id = link.dataset.scrollTo;
    if (!id || id === 'hero') return;
    const el = document.getElementById(id);
    if (el && !sectionTargets.includes(el)) sectionTargets.push(el);
  });
  if (sectionTargets.length) {
    function updateScrollspy() {
      const trigger = window.scrollY + SCROLLSPY_OFFSET;
      // When the user has scrolled to the very bottom, the last section's
      // top may never reach the trigger line — fall back to picking the
      // last visible section so it still highlights.
      const atBottom = (window.scrollY + window.innerHeight) >= document.documentElement.scrollHeight - 4;
      let bestId = null;
      let bestTop = -Infinity;
      sectionTargets.forEach((el) => {
        // offsetParent is null when the element (or an ancestor) is
        // display:none — skip those so the inactive side doesn't activate
        // its bar links by mistake.
        if (el.offsetParent === null) return;
        const top = el.getBoundingClientRect().top + window.scrollY;
        const eligible = atBottom ? true : top <= trigger;
        if (eligible && top > bestTop) { bestTop = top; bestId = el.id; }
      });
      barLinks.forEach((l) => l.classList.toggle('is-current', l.dataset.scrollTo === bestId));
    }
    window.addEventListener('scroll', updateScrollspy, { passive: true });
    window.addEventListener('resize', updateScrollspy);
    // Re-run when day/night flips (different sections become visible).
    new MutationObserver(updateScrollspy).observe(heroEl, { attributes: true, attributeFilter: ['data-time'] });
    updateScrollspy();
  }

  // Mobile hamburger toggle for each side-header. Closing routes through a
  // `closing` state so the staggered fade-out animation can play before the
  // nav is actually hidden. Total close duration must match the longest CSS
  // animation-delay (210ms) plus the animation duration (220ms).
  const NAV_CLOSE_MS = 450;
  function closeMenu(header) {
    if (!header) return;
    header.setAttribute('data-open', 'closing');
    const toggle = header.querySelector('.nav-toggle');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
    setTimeout(() => {
      // Guard against the user re-opening the menu mid-animation.
      if (header.getAttribute('data-open') === 'closing') {
        header.setAttribute('data-open', 'false');
      }
    }, NAV_CLOSE_MS);
  }
  document.querySelectorAll('.nav-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const header = btn.closest('.side-header');
      if (!header) return;
      const isOpen = header.getAttribute('data-open') === 'true';
      if (isOpen) {
        closeMenu(header);
      } else {
        header.setAttribute('data-open', 'true');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });
  document.querySelectorAll('.side-header .nav-link').forEach((link) => {
    link.addEventListener('click', () => closeMenu(link.closest('.side-header')));
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

  // ── Hardware lightbox: clicking a card pops the product image at full
  // size with the kind/name/note overlaid on a dark scrim at the bottom.
  // Card data-* attributes drive the lightbox content so this handler is
  // generic — adding more cards needs no JS changes.
  const hardwareLightbox = document.getElementById('hardware-lightbox');
  if (hardwareLightbox) {
    const lbImg  = hardwareLightbox.querySelector('.hardware-lightbox-img');
    const lbKind = hardwareLightbox.querySelector('.hardware-lightbox-kind');
    const lbName = hardwareLightbox.querySelector('.hardware-lightbox-name');
    const lbNote = hardwareLightbox.querySelector('.hardware-lightbox-note');
    let lbLastTrigger = null;

    function openHardwareLightbox(card) {
      lbImg.src = card.dataset.image || '';
      lbImg.alt = card.dataset.name || '';
      lbKind.textContent = card.dataset.kind || '';
      lbName.textContent = card.dataset.name || '';
      lbNote.textContent = card.dataset.note || '';
      hardwareLightbox.hidden = false;
      document.body.style.overflow = 'hidden';
      lbLastTrigger = card;
      const closeBtn = hardwareLightbox.querySelector('.hardware-lightbox-close');
      if (closeBtn) closeBtn.focus();
    }
    function closeHardwareLightbox() {
      hardwareLightbox.hidden = true;
      document.body.style.overflow = '';
      // Drop the src so a re-open animates the new image in cleanly.
      lbImg.removeAttribute('src');
      if (lbLastTrigger) { lbLastTrigger.focus(); lbLastTrigger = null; }
    }

    document.addEventListener('click', (e) => {
      const card = e.target.closest('[data-hardware-open]');
      if (card) { openHardwareLightbox(card); return; }
      if (e.target.closest('[data-hardware-close]')) closeHardwareLightbox();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !hardwareLightbox.hidden) closeHardwareLightbox();
    });
  }

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

    // Update every widget on the page (debug-box, hero corners, sticky bars).
    // Temp is set separately by fetchWeather.
    document.querySelectorAll('.weather[data-weather]').forEach((widget) => {
      widget.querySelector('.weather-icon').textContent = info.icon;
      widget.querySelector('.weather-condition').textContent = info.label;
    });

    // Particles spawn into EVERY .fx-rain / .fx-snow host on the page —
    // there's one in the hero scenery and one in each sticky bar so rain
    // shows in both places. Counts scale with intensity (storm gets the most).
    const rainHosts = document.querySelectorAll('.fx-rain');
    const snowHosts = document.querySelectorAll('.fx-snow');
    const isRainBucket = /^(drizzle|rain|showers)-/.test(info.bucket) || info.bucket === 'storm';
    const isSnowBucket = /^snow-/.test(info.bucket);
    rainHosts.forEach((host) => {
      if (isRainBucket) {
        // Bars are tiny (~90px tall) — keep them lighter so they don't get
        // overwhelmed with drops. The hero gets the full count.
        const isBar = host.closest('.bar-weather-fx') !== null;
        const full = info.bucket.startsWith('drizzle') ? 30
                   : info.bucket === 'storm'           ? 240
                   : info.bucket.startsWith('showers') ? 60 + info.intensity * 50
                   :                                     50 + info.intensity * 60;
        const count = isBar ? Math.max(8, Math.round(full * 0.18)) : full;
        spawnParticles(host, count, () => buildRain(info.intensity));
      } else {
        host.innerHTML = '';
      }
    });
    snowHosts.forEach((host) => {
      if (isSnowBucket) {
        const isBar = host.closest('.bar-weather-fx') !== null;
        const full = 30 + info.intensity * 35;
        const count = isBar ? Math.max(6, Math.round(full * 0.25)) : full;
        spawnParticles(host, count, buildSnow);
      } else {
        host.innerHTML = '';
      }
    });
  }

  function renderWeather(temp, code) {
    applyWeather(code);
    const t = Math.round(temp) + '°';
    document.querySelectorAll('.weather[data-weather]').forEach((widget) => {
      widget.querySelector('.weather-temp').textContent = t;
    });
  }
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
        const t = Math.round(cw.temperature) + '°';
        document.querySelectorAll('.weather[data-weather]').forEach((w) => {
          w.querySelector('.weather-temp').textContent = t;
        });
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
  // Multiple hosts now: the hero's .shooting-stars + the site-footer's. Pick
  // one at random per spawn so meteors appear across both regions.
  function getShootingHosts() {
    return Array.from(document.querySelectorAll('.shooting-stars'))
      .filter((el) => el.offsetParent !== null);
  }

  function spawnShootingStar() {
    if (document.body.dataset.time !== 'night') return;
    if (document.hidden) return;
    const hosts = getShootingHosts();
    if (!hosts.length) return;
    const shootingContainer = hosts[Math.floor(Math.random() * hosts.length)];

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

})();
