<footer class="sari-footer" id="footer">

    <div class="sari-footer-shell">

        {{-- =====================================================
            MAIN FOOTER
        ====================================================== --}}
        <div class="sari-footer-main">

            {{-- BRAND --}}
            <div class="sari-footer-brand">
                <a
                    href="#home"
                    class="sari-footer-brand-link"
                    aria-label="SARI Home"
                >
                    <img
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                        class="sari-footer-logo"
                    >
                </a>

                <p class="sari-footer-brand-copy">
                    A modern marketplace connecting everyday shopping,
                    trusted sellers, and reliable delivery in one seamless experience.
                </p>

                <div class="sari-footer-brand-meta">
                    <span class="sari-footer-status-dot"></span>
                    <span>Marketplace services available</span>
                </div>
            </div>


            {{-- EXPLORE --}}
            <div class="sari-footer-column">
                <h3 class="sari-footer-column-title">
                    Explore
                </h3>

                <nav class="sari-footer-links" aria-label="Explore SARI">
                    <a href="#home" class="sari-footer-link">
                        <span>Home</span>
                    </a>

                    <a href="#discover" class="sari-footer-link">
                        <span>Discover</span>
                    </a>

                    <a href="#discover" class="sari-footer-link">
                        <span>Categories</span>
                    </a>

                    <a href="#about" class="sari-footer-link">
                        <span>About SARI</span>
                    </a>
                </nav>
            </div>


            {{-- MARKETPLACE --}}
            <div class="sari-footer-column">
                <h3 class="sari-footer-column-title">
                    Marketplace
                </h3>

                <nav class="sari-footer-links" aria-label="Join SARI">
                    <a href="#discover" class="sari-footer-link">
                        <span>Shop on SARI</span>
                    </a>

                    <a href="{{ url('/register') }}" class="sari-footer-link">
                        <span>Become a Seller</span>
                    </a>

                    <a href="{{ url('/register') }}" class="sari-footer-link">
                        <span>Join as Courier</span>
                    </a>

                    <a href="{{ url('/login') }}" class="sari-footer-link">
                        <span>Sign In</span>
                    </a>
                </nav>
            </div>


            {{-- SUPPORT --}}
            <div class="sari-footer-column">
                <h3 class="sari-footer-column-title">
                    Support
                </h3>

                <nav class="sari-footer-links" aria-label="SARI Support">
                    <a href="#help" class="sari-footer-link">
                        <span>Help Center</span>
                    </a>

                    <a href="#contact" class="sari-footer-link">
                        <span>Contact Us</span>
                    </a>

                    <a href="#terms" class="sari-footer-link">
                        <span>Terms of Service</span>
                    </a>

                    <a href="#privacy" class="sari-footer-link">
                        <span>Privacy Policy</span>
                    </a>
                </nav>
            </div>

        </div>


        {{-- =====================================================
            FOOTER DIVIDER / SIGNATURE
        ====================================================== --}}
        <div class="sari-footer-signature">
            <span class="sari-footer-signature-line"></span>

            <span class="sari-footer-signature-text">
                Elevated Everyday
            </span>

            <span class="sari-footer-signature-line"></span>
        </div>


        {{-- =====================================================
            BOTTOM FOOTER
        ====================================================== --}}
        <div class="sari-footer-bottom">
            <p class="sari-footer-copy">
                © <span id="sariYear"></span> SARI. All rights reserved.
            </p>

            <div class="sari-footer-bottom-links">
                <a href="#privacy">Privacy</a>
                <span></span>
                <a href="#terms">Terms</a>
                <span></span>
                <a href="#contact">Contact</a>
            </div>
        </div>

    </div>

</footer>


<style>
/* =========================================================
   SARI FOOTER — CLEAN / FORMAL / PREMIUM
   ========================================================= */

.sari-footer {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(
            circle at 12% 0%,
            rgba(190, 135, 33, .08),
            transparent 28%
        ),
        #0c0b09;
    color: #f6f0e6;
    border-top: 1px solid rgba(255,255,255,.07);
}

.sari-footer::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,.018) 50%,
            transparent
        );
}

.sari-footer-shell {
    position: relative;
    z-index: 1;
    width: min(calc(100% - 48px), 1320px);
    margin-inline: auto;
    padding: 72px 0 26px;
}


/* =========================================================
   MAIN GRID
   ========================================================= */

.sari-footer-main {
    display: grid;
    grid-template-columns:
        minmax(300px, 1.65fr)
        repeat(3, minmax(145px, .7fr));
    gap: clamp(42px, 6vw, 88px);
    align-items: start;
}


/* =========================================================
   BRAND
   ========================================================= */

.sari-footer-brand {
    max-width: 420px;
}

.sari-footer-brand-link {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
}

.sari-footer-logo {
    display: block;
    width: clamp(118px, 10vw, 154px);
    height: auto;
    object-fit: contain;
    filter: brightness(0) invert(1);
    opacity: .93;
    transition:
        opacity .25s ease,
        transform .35s cubic-bezier(.22,1,.36,1);
}

.sari-footer-brand-link:hover .sari-footer-logo {
    opacity: 1;
    transform: translateY(-2px);
}

.sari-footer-brand-copy {
    max-width: 390px;
    margin: 24px 0 0;
    color: #aaa197;
    font-size: 11px;
    line-height: 1.85;
}

.sari-footer-brand-meta {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    margin-top: 20px;
    color: #81786f;
    font-size: 8px;
    font-weight: 600;
    letter-spacing: .025em;
}

.sari-footer-status-dot {
    width: 7px;
    height: 7px;
    flex: 0 0 auto;
    border-radius: 999px;
    background: #71a77f;
    box-shadow: 0 0 0 4px rgba(113,167,127,.08);
}


/* =========================================================
   COLUMNS
   ========================================================= */

.sari-footer-column-title {
    position: relative;
    margin: 2px 0 22px;
    color: #eee6da;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.sari-footer-column-title::after {
    content: "";
    display: block;
    width: 22px;
    height: 1px;
    margin-top: 10px;
    background: #bd861f;
}

.sari-footer-links {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 13px;
}

.sari-footer-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    color: #918981;
    font-size: 9.5px;
    font-weight: 500;
    line-height: 1.5;
    text-decoration: none;

    transition:
        color .22s ease,
        transform .28s cubic-bezier(.22,1,.36,1);
}

.sari-footer-link::before {
    content: "";
    width: 0;
    height: 1px;
    margin-right: 0;
    background: #c68b1d;

    transition:
        width .28s cubic-bezier(.22,1,.36,1),
        margin-right .28s cubic-bezier(.22,1,.36,1);
}

.sari-footer-link:hover {
    color: #f1e6d3;
    transform: translateX(2px);
}

.sari-footer-link:hover::before {
    width: 12px;
    margin-right: 7px;
}


/* =========================================================
   SIGNATURE DIVIDER
   ========================================================= */

.sari-footer-signature {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
    margin-top: 62px;
}

.sari-footer-signature-line {
    width: min(120px, 10vw);
    height: 1px;
    background: rgba(255,255,255,.09);
}

.sari-footer-signature-text {
    color: #796f63;
    font-size: 7.5px;
    font-weight: 700;
    letter-spacing: .24em;
    text-transform: uppercase;
    white-space: nowrap;
}


/* =========================================================
   BOTTOM
   ========================================================= */

.sari-footer-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    margin-top: 24px;
    padding-top: 22px;
    border-top: 1px solid rgba(255,255,255,.07);
}

.sari-footer-copy {
    margin: 0;
    color: #6f6861;
    font-size: 8px;
    line-height: 1.5;
}

.sari-footer-bottom-links {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sari-footer-bottom-links a {
    color: #777069;
    font-size: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: color .2s ease;
}

.sari-footer-bottom-links a:hover {
    color: #d39a2c;
}

.sari-footer-bottom-links > span {
    width: 3px;
    height: 3px;
    border-radius: 999px;
    background: #4a4641;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 1050px) {
    .sari-footer-main {
        grid-template-columns:
            minmax(280px, 1.4fr)
            repeat(3, 1fr);
        gap: 42px;
    }
}

@media (max-width: 820px) {
    .sari-footer-shell {
        width: min(calc(100% - 40px), 1320px);
        padding-top: 58px;
    }

    .sari-footer-main {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 42px 36px;
    }

    .sari-footer-brand {
        grid-column: 1 / -1;
        max-width: 560px;
    }

    .sari-footer-brand-copy {
        max-width: 520px;
    }
}

@media (max-width: 560px) {
    .sari-footer-shell {
        width: min(calc(100% - 32px), 1320px);
        padding: 48px 0 22px;
    }

    .sari-footer-main {
        grid-template-columns: 1fr 1fr;
        gap: 36px 20px;
    }

    .sari-footer-brand {
        grid-column: 1 / -1;
    }

    .sari-footer-column-title {
        margin-bottom: 17px;
    }

    .sari-footer-links {
        gap: 11px;
    }

    .sari-footer-link {
        font-size: 9px;
    }

    .sari-footer-signature {
        margin-top: 46px;
        gap: 12px;
    }

    .sari-footer-signature-line {
        width: 30px;
    }

    .sari-footer-signature-text {
        font-size: 6.5px;
        letter-spacing: .18em;
    }

    .sari-footer-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 13px;
    }
}

@media (max-width: 380px) {
    .sari-footer-main {
        grid-template-columns: 1fr;
    }

    .sari-footer-brand {
        grid-column: auto;
    }
}


/* =========================================================
   ACCESSIBILITY
   ========================================================= */

@media (prefers-reduced-motion: reduce) {
    .sari-footer-logo,
    .sari-footer-link,
    .sari-footer-link::before {
        transition: none !important;
        transform: none !important;
    }
}
</style>


<script>
(function () {
    const year =
        document.getElementById('sariYear');

    if (year) {
        year.textContent =
            new Date().getFullYear();
    }
})();
</script>