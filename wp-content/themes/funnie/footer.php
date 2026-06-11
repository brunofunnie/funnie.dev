<footer class="site-footer" role="contentinfo">
    <div class="site-footer-scenery site-footer-scenery-day" aria-hidden="true">
        <svg class="footer-hills" viewBox="0 0 1200 220" preserveAspectRatio="none" fill="none">
            <path d="M0,140 C160,90 320,140 500,110 C680,80 840,140 1000,100 C1120,80 1170,110 1200,108 L1200,220 L0,220 Z" fill="var(--hill-far, #6fa66b)"/>
            <path d="M0,170 C200,130 350,170 520,150 C680,130 820,165 980,140 C1100,125 1160,155 1200,150 L1200,220 L0,220 Z" fill="var(--hill-mid, #4a8950)"/>
            <path d="M0,196 C160,180 320,196 500,188 C680,180 840,194 1000,186 C1120,180 1170,190 1200,188 L1200,220 L0,220 Z" fill="var(--hill-near, #2e5d34)"/>
        </svg>
        <svg class="footer-trees" viewBox="0 0 1200 220" preserveAspectRatio="none" fill="none">
            <g fill="#3a2412">
                <rect x="93"   y="195" width="4" height="14"/>
                <rect x="218"  y="193" width="4" height="16"/>
                <rect x="363"  y="197" width="4" height="12"/>
                <rect x="538"  y="190" width="4" height="18"/>
                <rect x="714"  y="195" width="4" height="14"/>
                <rect x="900"  y="192" width="4" height="16"/>
                <rect x="1076" y="195" width="4" height="14"/>
            </g>
            <g fill="#1d3b22">
                <polygon points="88,200 95,158 102,200"/>
                <polygon points="210,200 220,148 230,200"/>
                <polygon points="358,200 365,165 372,200"/>
                <polygon points="530,200 540,142 550,200"/>
                <polygon points="708,200 716,160 724,200"/>
                <polygon points="891,200 902,150 913,200"/>
                <polygon points="1069,200 1078,158 1087,200"/>
            </g>
            <g fill="#2d1b0e">
                <rect x="156"  y="198" width="3" height="12"/>
                <rect x="306"  y="200" width="3" height="10"/>
                <rect x="626"  y="198" width="3" height="12"/>
                <rect x="816"  y="200" width="3" height="10"/>
                <rect x="996"  y="198" width="3" height="12"/>
            </g>
            <g fill="#163019">
                <polygon points="149,202 158,170 167,202"/>
                <polygon points="299,202 308,176 317,202"/>
                <polygon points="619,202 628,172 637,202"/>
                <polygon points="809,202 818,176 827,202"/>
                <polygon points="989,202 998,172 1007,202"/>
            </g>
        </svg>
    </div>
    <div class="site-footer-scenery site-footer-scenery-night" aria-hidden="true">
        <div class="footer-stars">
            <span class="star" style="top:18%;left:6%;width:2px;height:2px;animation-delay:0s"></span>
            <span class="star" style="top:12%;left:18%;width:1.5px;height:1.5px;animation-delay:0.5s"></span>
            <span class="star" style="top:28%;left:30%;width:2.5px;height:2.5px;animation-delay:1.0s"></span>
            <span class="star" style="top:8%;left:42%;width:1.5px;height:1.5px;animation-delay:1.6s"></span>
            <span class="star" style="top:22%;left:54%;width:2px;height:2px;animation-delay:0.3s"></span>
            <span class="star" style="top:14%;left:66%;width:3px;height:3px;animation-delay:2.1s"></span>
            <span class="star" style="top:32%;left:78%;width:1.5px;height:1.5px;animation-delay:0.9s"></span>
            <span class="star" style="top:10%;left:90%;width:2px;height:2px;animation-delay:1.4s"></span>
            <span class="star" style="top:40%;left:12%;width:1.5px;height:1.5px;animation-delay:2.4s"></span>
            <span class="star" style="top:46%;left:38%;width:2px;height:2px;animation-delay:0.7s"></span>
            <span class="star" style="top:50%;left:60%;width:1.5px;height:1.5px;animation-delay:1.8s"></span>
            <span class="star" style="top:42%;left:84%;width:2.5px;height:2.5px;animation-delay:0.2s"></span>
            <span class="star" style="top:58%;left:24%;width:1px;height:1px;animation-delay:1.2s"></span>
            <span class="star" style="top:62%;left:48%;width:1px;height:1px;animation-delay:0.4s"></span>
            <span class="star" style="top:54%;left:72%;width:1px;height:1px;animation-delay:2.6s"></span>
        </div>
        <div class="shooting-stars" aria-hidden="true"></div>
    </div>
    <div class="bar-weather-fx" aria-hidden="true">
        <div class="fx-clouds-extra"></div>
        <div class="fx-rain"></div>
        <div class="fx-snow"></div>
        <div class="fx-fog"></div>
        <div class="fx-storm-tint"></div>
        <div class="fx-lightning"></div>
    </div>
    <div class="site-footer-inner">
        <span class="site-footer-mark">© <?php echo esc_html(date('Y')); ?> Funnie Tech.</span>
        <span class="site-footer-tagline">All rights reserved.</span>
    </div>
</footer>

<!-- Custom lightsaber cursor (positioned by JS) -->
<div class="cursor-saber" aria-hidden="true">
    <img class="saber-idle"   src="<?php echo esc_url(FUNNIE_THEME_URL . '/assets/cursor.gif'); ?>" alt="">
    <img class="saber-active" src="<?php echo esc_url(FUNNIE_THEME_URL . '/assets/cursor_pointer.gif'); ?>" alt="">
</div>
<?php wp_footer(); ?>
</body>
</html>
