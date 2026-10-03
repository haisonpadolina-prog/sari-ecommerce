{{-- SARI ABOUT PAGE — Premium Editorial V2 --}}
<section class="sari-about-v2" id="about">

    {{-- HERO --}}
    <section class="sari-about-v2__hero">
        <div class="sari-about-v2__hero-copy">
            <div class="sari-about-v2__eyebrow">
                <span></span>
                About SARI
            </div>

            <h1 class="sari-about-v2__hero-title">
                Redefining Shopping.<br>
                Connecting <em>Everything.</em>
            </h1>

            <p class="sari-about-v2__lead">
                SARI is a next-generation e-commerce marketplace built to make
                discovery effortless, transactions secure, and delivery seamless—
                every step of the way.
            </p>

            <div class="sari-about-v2__pillars">
                <div class="sari-about-v2__pillar">
                    <span class="sari-about-v2__icon" aria-hidden="true">
                        <i class="fi fi-ss-search-location"></i>
                    </span>
                    <div>
                        <strong>Discover</strong>
                        <small>Explore millions of products effortlessly.</small>
                    </div>
                </div>

                <div class="sari-about-v2__pillar">
                    <span class="sari-about-v2__icon" aria-hidden="true">
                        <i class="fi fi-ss-shield-check"></i>
                    </span>
                    <div>
                        <strong>Trust</strong>
                        <small>Shop with confidence and secure every step.</small>
                    </div>
                </div>

                <div class="sari-about-v2__pillar">
                    <span class="sari-about-v2__icon" aria-hidden="true">
                        <i class="fi fi-sr-truck-check"></i>
                    </span>
                    <div>
                        <strong>Delivered</strong>
                        <small>Fast, reliable delivery to your doorstep.</small>
                    </div>
                </div>
            </div>

            <a href="#about-story" class="sari-about-v2__button">
                Explore Our Platform
                <span>↗</span>
            </a>
        </div>

        <div class="sari-about-v2__hero-media">
            <img
                src="{{ asset('images/sari-about.png') }}"
                alt="SARI logistics and fulfillment center"
                class="sari-about-v2__hero-image"
                loading="eager"
            >

            {{-- Premium S-curve / editorial mask --}}
            <svg
                class="sari-about-v2__wave"
                viewBox="0 0 100 100"
                preserveAspectRatio="none"
                aria-hidden="true"
            >
                <path d="M0 0H58C70 14 76 28 70 42C64 55 60 65 62 76C64 86 57 94 47 100H0Z"></path>
            </svg>

            {{-- Appears after the commercial has played once. --}}
            <button
                type="button"
                class="sari-about-v2__replay"
                aria-label="Replay SARI commercial"
                title="Replay SARI commercial"
            >
                <span class="sari-about-v2__replay-ring" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M9 7.5 17 12l-8 4.5v-9Z" fill="currentColor"></path>
                    </svg>
                </span>
                <span class="sari-about-v2__replay-label">Play film</span>
            </button>
        </div>

        {{-- Full-hero commercial layer. It is activated by JS when the hero enters view. --}}
        <div class="sari-about-v2__commercial" aria-hidden="true">
            <video
                class="sari-about-v2__commercial-video"
                preload="auto"
                playsinline
                webkit-playsinline
            >
                <source src="{{ asset('videos/sari-about-commercial.mp4') }}" type="video/mp4">
            </video>

            <button
                type="button"
                class="sari-about-v2__commercial-sound"
                aria-label="Unmute commercial"
                title="Unmute commercial"
                aria-pressed="false"
            >
                <svg class="sari-about-v2__sound-icon sari-about-v2__sound-icon--on" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 9v6h4l5 4V5L9 9H5Z"></path>
                    <path d="M17 9.5c.8.7 1.2 1.5 1.2 2.5s-.4 1.8-1.2 2.5"></path>
                    <path d="M19.5 7c1.4 1.3 2.1 3 2.1 5s-.7 3.7-2.1 5"></path>
                </svg>
                <svg class="sari-about-v2__sound-icon sari-about-v2__sound-icon--off" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 9v6h4l5 4V5L9 9H5Z"></path>
                    <path d="m17 10 4 4M21 10l-4 4"></path>
                </svg>
                <span class="sari-about-v2__sound-label">Unmute</span>
            </button>

            <button
                type="button"
                class="sari-about-v2__commercial-close"
                aria-label="Close commercial"
                title="Close commercial"
            >
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </section>

    {{-- STATS --}}
    <section class="sari-about-v2__stats" aria-label="SARI marketplace statistics">
        <div class="sari-about-v2__stat">
            <b>150<span>+</span></b>
            <small>Countries<br>Connected</small>
        </div>
        <div class="sari-about-v2__stat">
            <b>10M<span>+</span></b>
            <small>Products<br>Available</small>
        </div>
        <div class="sari-about-v2__stat">
            <b>250K<span>+</span></b>
            <small>Active<br>Sellers</small>
        </div>
        <div class="sari-about-v2__stat">
            <b>5M<span>+</span></b>
            <small>Happy<br>Customers</small>
        </div>
        <div class="sari-about-v2__stat-note">
            <span>CONNECTED BY SARI</span>
            <strong>One marketplace.<br>Countless possibilities.</strong>
        </div>
    </section>

    {{-- STORY — transparent PNG/WEBP sits directly on the page, no card --}}
    <section class="sari-about-v2__story" id="about-story">
        <div class="sari-about-v2__story-art">
            <div class="sari-about-v2__story-glow"></div>
            <img
                src="{{ asset('images/sari-story.png') }}"
                alt="SARI products, packaging and mobile marketplace experience"
                loading="lazy"
            >
            <span class="sari-about-v2__story-caption">01&nbsp;&nbsp; FROM LOCAL ROOTS TO GLOBAL IMPACT</span>
        </div>

        <div class="sari-about-v2__story-copy">
            <div class="sari-about-v2__eyebrow">
                <span></span>
                Our Story
            </div>

            <h2>
                From Local Roots<br>
                to <em>Global Impact.</em>
            </h2>

            <p>
                SARI began with a simple belief: shopping should be limitless.
                Today, we empower thousands of businesses and millions of
                shoppers across the globe.
            </p>

            <a href="#about-values" class="sari-about-v2__outline-button">
                Our Journey <span>↗</span>
            </a>

            <div class="sari-about-v2__features">
                <div>
                    <span class="sari-about-v2__feature-icon" aria-hidden="true"><i class="fi fi-ss-sparkles"></i></span>
                    <p><strong>Curated for You</strong><small>Smart discovery and personalized recommendations just for you.</small></p>
                </div>
                <div>
                    <span class="sari-about-v2__feature-icon" aria-hidden="true"><i class="fi fi-sr-trust"></i></span>
                    <p><strong>Built for Trust</strong><small>Transparent policies, secure payments, and 24/7 support.</small></p>
                </div>
                <div>
                    <span class="sari-about-v2__feature-icon" aria-hidden="true"><i class="fi fi-sr-audit"></i></span>
                    <p><strong>Seamless Experience</strong><small>Powerful technology that makes shopping smart and simple.</small></p>
                </div>
                <div>
                    <span class="sari-about-v2__feature-icon" aria-hidden="true"><i class="fi fi-sr-hr-group"></i></span>
                    <p><strong>Everyone, Everywhere</strong><small>Opening doors for businesses and shoppers in every corner of the world.</small></p>
                </div>
            </div>
        </div>
    </section>

    {{-- VALUES --}}
    <section class="sari-about-v2__values" id="about-values">
        <div class="sari-about-v2__section-heading">
            <div class="sari-about-v2__eyebrow">
                <span></span>
                Our Values
            </div>
            <h2>Built on <em>Values.</em> Driven by Impact.</h2>
        </div>

        <div class="sari-about-v2__value-grid">
            <article>
                <span class="value-icon value-icon--gold"><i class="fi fi-sr-master-plan-integrate" aria-hidden="true"></i></span>
                <div>
                    <h3>Integrity</h3>
                    <p>We do the right thing, always.</p>
                </div>
            </article>
            <article>
                <span class="value-icon value-icon--blue"><i class="fi fi-sr-practice" aria-hidden="true"></i></span>
                <div>
                    <h3>Innovation</h3>
                    <p>We embrace new ideas to create better experiences.</p>
                </div>
            </article>
            <article>
                <span class="value-icon value-icon--green"><i class="fi fi-ss-hand-back-fist" aria-hidden="true"></i></span>
                <div>
                    <h3>Empowerment</h3>
                    <p>We empower businesses and communities to grow together.</p>
                </div>
            </article>
            <article>
                <span class="value-icon value-icon--coral"><i class="fi fi-ss-hand-holding-water" aria-hidden="true"></i></span>
                <div>
                    <h3>Sustainability</h3>
                    <p>We build for the future, with care for people and planet.</p>
                </div>
            </article>
        </div>
    </section>

<script>
(function () {
    const root = document.querySelector('.sari-about-v2');
    if (!root) return;

    // Enable the motion layer only when JS is available, so content never gets stuck hidden.
    root.classList.add('sari-motion-ready');

    /* -------------------------------------------------------
       ABOUT HERO COMMERCIAL
       - Autoplays once when the hero meaningfully enters view.
       - Restores the original editorial hero after playback.
       - Shows a manual replay control over the right-side media.
       ------------------------------------------------------- */
    const hero = root.querySelector('.sari-about-v2__hero');
    const commercial = hero?.querySelector('.sari-about-v2__commercial');
    const commercialVideo = hero?.querySelector('.sari-about-v2__commercial-video');
    const replayButton = hero?.querySelector('.sari-about-v2__replay');
    const closeButton = hero?.querySelector('.sari-about-v2__commercial-close');
    const soundButton = hero?.querySelector('.sari-about-v2__commercial-sound');
    const soundLabel = soundButton?.querySelector('.sari-about-v2__sound-label');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let hasAutoPlayed = false;
    let isCommercialOpen = false;
    let restoreTimer = null;
    let pendingSoundUnlock = false;

    const restoreHero = ({ instant = false } = {}) => {
        if (!hero || !commercial || !commercialVideo) return;

        window.clearTimeout(restoreTimer);
        isCommercialOpen = false;
        commercialVideo.pause();

        hero.classList.remove('is-commercial-playing');
        hero.classList.add('is-commercial-restoring');
        commercial.setAttribute('aria-hidden', 'true');

        const finishRestore = () => {
            hero.classList.remove('is-commercial-restoring');
            hero.classList.add('has-commercial-played');
            commercialVideo.currentTime = 0;
        };

        if (instant || prefersReducedMotion) {
            finishRestore();
            return;
        }

        // Copy and media return together as one complete composition.
        restoreTimer = window.setTimeout(finishRestore, 720);
    };

    const syncSoundControl = () => {
        if (!commercialVideo || !soundButton || !soundLabel) return;
        const muted = commercialVideo.muted;
        soundButton.classList.toggle('is-muted', muted);
        soundButton.setAttribute('aria-pressed', muted ? 'false' : 'true');
        soundButton.setAttribute('aria-label', muted ? 'Unmute commercial' : 'Mute commercial');
        soundButton.setAttribute('title', muted ? 'Unmute commercial' : 'Mute commercial');
        soundLabel.textContent = muted ? 'Unmute' : 'Mute';
    };

    const playCommercial = async ({ manual = false } = {}) => {
        if (!hero || !commercial || !commercialVideo || isCommercialOpen) return;

        window.clearTimeout(restoreTimer);
        isCommercialOpen = true;
        hero.classList.remove('is-commercial-restoring', 'has-commercial-played');
        hero.classList.add('is-commercial-playing');
        commercial.setAttribute('aria-hidden', 'false');

        try {
            commercialVideo.currentTime = 0;

            // Manual replay can always request sound. Scroll autoplay also tries
            // sound first; browsers may reject unmuted autoplay until the user
            // has interacted with the page.
            commercialVideo.muted = false;
            commercialVideo.volume = 1;
            syncSoundControl();

            try {
                await commercialVideo.play();
                pendingSoundUnlock = false;
            } catch (soundAutoplayError) {
                if (manual) throw soundAutoplayError;

                // Fallback keeps the transition instant instead of failing the film.
                // The first click/tap anywhere on the page upgrades playback to audio.
                commercialVideo.muted = true;
                syncSoundControl();
                await commercialVideo.play();
                pendingSoundUnlock = true;
            }
        } catch (error) {
            // Browser autoplay policies can still block playback in edge cases.
            // Restore the hero and leave the replay control available.
            isCommercialOpen = false;
            hero.classList.remove('is-commercial-playing');
            hero.classList.add('has-commercial-played');
            commercial.setAttribute('aria-hidden', 'true');
            if (!manual) hasAutoPlayed = true;
        }
    };

    if (commercialVideo) {
        commercialVideo.addEventListener('ended', () => restoreHero());
        commercialVideo.addEventListener('error', () => restoreHero({ instant: true }));
    }

    replayButton?.addEventListener('click', () => playCommercial({ manual: true }));
    closeButton?.addEventListener('click', () => restoreHero());
    soundButton?.addEventListener('click', () => {
        if (!commercialVideo) return;
        commercialVideo.muted = !commercialVideo.muted;
        commercialVideo.volume = 1;
        pendingSoundUnlock = false;
        syncSoundControl();
    });

    // If the browser blocked sound during scroll autoplay, the first real user
    // interaction enables audio immediately without restarting the commercial.
    const unlockCommercialAudio = () => {
        if (!pendingSoundUnlock || !isCommercialOpen || !commercialVideo) return;
        commercialVideo.muted = false;
        commercialVideo.volume = 1;
        pendingSoundUnlock = false;
        syncSoundControl();
    };
    window.addEventListener('pointerdown', unlockCommercialAudio, { passive: true });
    window.addEventListener('keydown', unlockCommercialAudio);

    if (hero && commercialVideo && 'IntersectionObserver' in window) {
        const commercialObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting || hasAutoPlayed) return;

                hasAutoPlayed = true;
                observer.unobserve(entry.target);

                // Start immediately as soon as the hero reaches the trigger point.
                playCommercial();
            });
        }, {
            threshold: 0.58,
            rootMargin: '0px 0px -5% 0px'
        });

        commercialObserver.observe(hero);
    } else if (hero) {
        // No observer support: keep the normal hero visible and expose replay.
        hero.classList.add('has-commercial-played');
    }

    const groups = [
        ['.sari-about-v2__stats', 'sari-motion-item'],
        ['.sari-about-v2__story-art', 'sari-motion-item sari-motion-left'],
        ['.sari-about-v2__story-copy', 'sari-motion-item sari-motion-right'],
        ['.sari-about-v2__section-heading', 'sari-motion-item'],
        ['.sari-about-v2__vision', 'sari-motion-item']
    ];

    groups.forEach(([selector, classes]) => {
        const el = root.querySelector(selector);
        if (el) el.classList.add(...classes.split(' '));
    });


    root.querySelectorAll('.sari-about-v2__stat').forEach((el, index) => {
        el.classList.add('sari-motion-item');
        if (index < 4) el.classList.add('sari-motion-delay-' + (index + 1));
    });

    root.querySelectorAll('.sari-about-v2__value-grid article').forEach((el, index) => {
        el.classList.add('sari-motion-item');
        if (index < 4) el.classList.add('sari-motion-delay-' + (index + 1));
    });

    root.querySelectorAll('.sari-about-v2__features > div').forEach((el, index) => {
        el.classList.add('sari-motion-item');
        if (index < 4) el.classList.add('sari-motion-delay-' + (index + 1));
    });

    const revealItems = root.querySelectorAll('.sari-motion-item');

    if (!('IntersectionObserver' in window)) {
        revealItems.forEach(el => el.classList.add('is-visible'));
        const vision = root.querySelector('.sari-about-v2__vision');
        if (vision) vision.classList.add('sari-is-visible');
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            obs.unobserve(entry.target);
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -8% 0px'
    });

    revealItems.forEach(el => observer.observe(el));

    const vision = root.querySelector('.sari-about-v2__vision');
    if (vision) {
        const visionObserver = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('sari-is-visible');
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.18 });
        visionObserver.observe(vision);
    }
})();
</script>

<style>
/* ==========================================================
   SARI ABOUT V2 REFINEMENT
   Cleaner premium editorial layout
   ========================================================== */

.sari-about-v2 {
    background:#faf8f3;
    overflow:hidden;
}

/* ==========================================================
   ABOUT HERO COMMERCIAL EXPERIENCE
   ========================================================== */
.sari-about-v2__hero {
    position: relative;
    isolation: isolate;
}

.sari-about-v2__hero-copy,
.sari-about-v2__hero-media {
    transition:
        opacity .28s cubic-bezier(.22,.61,.36,1),
        transform .48s cubic-bezier(.16,1,.3,1);
    will-change: opacity, transform;
    backface-visibility: hidden;
    transform: translateZ(0);
}

.sari-about-v2__commercial {
    position: absolute;
    inset: 0;
    z-index: 20;
    display: grid;
    place-items: center;
    overflow: hidden;
    background: #090806;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translateZ(0) scale(1.008);
    transition:
        opacity .28s ease-out,
        transform .45s cubic-bezier(.16,1,.3,1),
        visibility 0s linear .28s;
    will-change: opacity, transform;
    backface-visibility: hidden;
}

.sari-about-v2__commercial::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    box-shadow: inset 0 0 90px rgba(0,0,0,.22);
}

.sari-about-v2__commercial-video {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    background: #090806;
}

.sari-about-v2__commercial-sound {
    position: absolute;
    top: 22px;
    left: 22px;
    z-index: 4;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 0 13px;
    border: 1px solid rgba(255,255,255,.42);
    border-radius: 999px;
    background: rgba(13,11,8,.38);
    color: #fff;
    font: inherit;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
    transition: transform .2s ease, background .2s ease, border-color .2s ease;
}

.sari-about-v2__commercial-sound:hover,
.sari-about-v2__commercial-sound:focus-visible {
    transform: translateY(-1px);
    background: rgba(13,11,8,.62);
    border-color: rgba(255,255,255,.72);
    outline: none;
}

.sari-about-v2__sound-icon {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sari-about-v2__sound-icon--off { display: none; }
.sari-about-v2__commercial-sound.is-muted .sari-about-v2__sound-icon--on { display: none; }
.sari-about-v2__commercial-sound.is-muted .sari-about-v2__sound-icon--off { display: block; }

.sari-about-v2__commercial-close {
    position: absolute;
    top: 24px;
    right: 24px;
    z-index: 3;
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,.42);
    border-radius: 999px;
    background: rgba(13,11,8,.32);
    color: #fff;
    font: inherit;
    font-size: 27px;
    line-height: 1;
    cursor: pointer;
    /* Avoid full-screen blur work during video playback on lower-end GPUs. */
    transition: transform .25s ease, background .25s ease, border-color .25s ease;
}

.sari-about-v2__commercial-close:hover,
.sari-about-v2__commercial-close:focus-visible {
    transform: scale(1.06);
    background: rgba(13,11,8,.55);
    border-color: rgba(255,255,255,.75);
    outline: none;
}

.sari-about-v2__hero.is-commercial-playing .sari-about-v2__commercial {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: scale(1);
    transition-delay: 0s;
}

.sari-about-v2__hero.is-commercial-playing .sari-about-v2__hero-copy {
    opacity: 0;
    transform: translate3d(-56px,0,0);
    /* transform/opacity only for GPU-friendly motion */
}

.sari-about-v2__hero.is-commercial-playing .sari-about-v2__hero-media {
    opacity: 0;
    transform: translate3d(56px,0,0) scale(1.025);
    /* transform/opacity only for GPU-friendly motion */
}

/* Restore sequence: image returns from the right, white editorial copy from the left. */
.sari-about-v2__hero.is-commercial-restoring .sari-about-v2__commercial {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translateZ(0) scale(1.008);
}

.sari-about-v2__hero.is-commercial-restoring .sari-about-v2__hero-copy {
    animation: sariAboutCopyReturn .70s cubic-bezier(.16,1,.3,1) both;
}

.sari-about-v2__hero.is-commercial-restoring .sari-about-v2__hero-media {
    animation: sariAboutMediaReturn .70s cubic-bezier(.16,1,.3,1) both;
}

@keyframes sariAboutCopyReturn {
    0% { opacity: 0; transform: translate3d(-36px,0,0); }
    100% { opacity: 1; transform: translate3d(0,0,0); }
}

@keyframes sariAboutMediaReturn {
    0% { opacity: 0; transform: translate3d(36px,0,0) scale(1.012); }
    100% { opacity: 1; transform: translate3d(0,0,0) scale(1); }
}

.sari-about-v2__replay {
    position: absolute;
    z-index: 8;
    top: 50%;
    left: 72%;
    transform: translate(-50%,-50%) scale(.92);
    display: grid;
    justify-items: center;
    gap: 7px;
    padding: 0;
    border: 0;
    background: transparent;
    color: #fff;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .4s ease, transform .45s cubic-bezier(.16,1,.3,1), visibility .4s;
}

.sari-about-v2__replay-ring {
    width: 50px;
    height: 50px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,.75);
    border-radius: 999px;
    background: rgba(12,10,7,.24);
    box-shadow: 0 16px 45px rgba(0,0,0,.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: transform .3s ease, background .3s ease, box-shadow .3s ease;
}

.sari-about-v2__replay-ring svg {
    width: 20px;
    height: 20px;
    margin-left: 3px;
}

.sari-about-v2__replay-label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    text-shadow: 0 2px 14px rgba(0,0,0,.5);
}

.sari-about-v2__hero.has-commercial-played .sari-about-v2__replay {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translate(-50%,-50%) scale(1);
}

.sari-about-v2__replay:hover .sari-about-v2__replay-ring,
.sari-about-v2__replay:focus-visible .sari-about-v2__replay-ring {
    transform: scale(1.08);
    background: rgba(201,145,40,.9);
    box-shadow: 0 18px 55px rgba(0,0,0,.3);
}

.sari-about-v2__replay:focus-visible {
    outline: 2px solid #fff;
    outline-offset: 8px;
    border-radius: 999px;
}

@media (max-width: 768px) {
    .sari-about-v2__commercial {
        border-radius: inherit;
    }

    .sari-about-v2__commercial-video {
        object-fit: cover;
    }

    .sari-about-v2__commercial-close {
        top: 14px;
        right: 14px;
        width: 40px;
        height: 40px;
    }

    .sari-about-v2__replay {
        left: 50%;
        top: 50%;
    }

    .sari-about-v2__replay-ring {
        width: 48px;
        height: 48px;
    }

    .sari-about-v2__commercial-sound {
        top: 14px;
        left: 14px;
        min-height: 36px;
        padding: 0 11px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sari-about-v2__hero-copy,
    .sari-about-v2__hero-media,
    .sari-about-v2__commercial,
    .sari-about-v2__replay,
    .sari-about-v2__replay-ring {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }
}

/* VALUES SECTION */
.sari-about-v2__values {
    background:#fff !important;
    padding:120px 0 !important;
}

.sari-about-v2__values .sari-about-v2__section-heading {
    max-width:800px;
    margin:auto;
    text-align:center;
}

.sari-about-v2__values h2 {
    color:#17140e;
    font-size:clamp(36px,5vw,56px);
    letter-spacing:-.05em;
}

.sari-about-v2__values h2 em {
    color:#c99128;
    font-style:normal;
}

.sari-about-v2__value-grid {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:24px;
    margin-top:60px;
}

.sari-about-v2__value-grid article {
    background:white;
    border:1px solid #ece3d4;
    border-radius:30px;
    padding:36px 30px;
    min-height:230px;
    box-shadow:0 15px 40px rgba(0,0,0,.04);
    transition:.35s ease;
}

.sari-about-v2__value-grid article:hover {
    transform:translateY(-8px);
    border-color:#d6b36c;
    box-shadow:0 25px 60px rgba(0,0,0,.10);
}

.value-icon {
    width:68px;
    height:68px;
    border-radius:22px;
    display:grid;
    place-items:center;
    font-size:27px;
    font-weight:700;
    margin-bottom:25px;
    transition:.35s ease;
}

.sari-about-v2__value-grid article:hover .value-icon {
    transform:scale(1.1);
}

.value-icon--gold {
    color:#bd8300;
    background:#fff7df;
}

.value-icon--blue {
    color:#3d78ad;
    background:#edf6ff;
}

.value-icon--green {
    color:#438766;
    background:#edf8f1;
}

.value-icon--coral {
    color:#c86f55;
    background:#fff0eb;
}


/* STORY FEATURE BOX */
.sari-about-v2__features > div {
    background:white;
    border:1px solid #eee4d5;
    border-radius:20px;
    padding:18px;
    transition:.3s ease;
}

.sari-about-v2__features > div:hover {
    transform:translateY(-5px);
    box-shadow:0 15px 35px rgba(0,0,0,.06);
}


/* RESPONSIVE */
@media(max-width:1100px){
    .sari-about-v2__value-grid{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:650px){
    .sari-about-v2__value-grid{
        grid-template-columns:1fr;
    }
}


/* ==========================================================
   MOBILE-ONLY LAYOUT FIX
   Desktop/tablet design above 768px remains unchanged.
   Only the CTA text color and mobile composition are adjusted.
   ========================================================== */

/* Requested CTA text/arrow color only */
.sari-about-v2 .sari-about-v2__button,
.sari-about-v2 .sari-about-v2__button > span {
    color: #ffffff !important;
}

/* Keep visited state white as well */
.sari-about-v2 .sari-about-v2__button:visited,
.sari-about-v2 .sari-about-v2__button:visited > span {
    color: #ffffff !important;
}

@media (max-width: 768px) {

    /* HERO: clean stacked composition */
    .sari-about-v2 .sari-about-v2__hero {
        display: flex !important;
        flex-direction: column !important;
        min-height: auto !important;
    }

    .sari-about-v2 .sari-about-v2__hero-copy {
        order: 1;
        width: 100% !important;
        max-width: none !important;
        box-sizing: border-box;
        padding: 64px 20px 34px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-media {
        order: 2;
        position: relative;
        width: calc(100% - 24px) !important;
        min-height: 0 !important;
        margin: 0 12px 18px !important;
        overflow: hidden;
        border-radius: 18px;
    }

    .sari-about-v2 .sari-about-v2__hero-image {
        display: block;
        width: 100% !important;
        height: clamp(280px, 72vw, 430px) !important;
        min-height: 0 !important;
        object-fit: cover;
        object-position: center;
    }

    /* The desktop editorial curve is not useful on a narrow portrait layout. */
    .sari-about-v2 .sari-about-v2__wave {
        display: none !important;
    }

    /* Copy hierarchy */
    .sari-about-v2 .sari-about-v2__hero-title {
        margin-top: 14px !important;
        font-size: clamp(38px, 10.5vw, 50px) !important;
        line-height: 1.02 !important;
        letter-spacing: -.045em !important;
    }

    .sari-about-v2 .sari-about-v2__lead {
        max-width: 560px !important;
        margin-top: 18px !important;
        font-size: 13px !important;
        line-height: 1.68 !important;
    }

    /* Pillars: same components, cleaner vertical mobile flow */
    .sari-about-v2 .sari-about-v2__pillars {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0 !important;
        width: 100% !important;
        margin-top: 26px !important;
    }

    .sari-about-v2 .sari-about-v2__pillar {
        width: 100% !important;
        box-sizing: border-box;
        padding: 14px 0 !important;
    }

    .sari-about-v2 .sari-about-v2__pillar:first-child {
        padding-top: 0 !important;
    }

    .sari-about-v2 .sari-about-v2__pillar + .sari-about-v2__pillar {
        border-left: 0 !important;
        border-top: 1px solid rgba(40, 32, 20, .10);
    }

    /* CTA: thumb-friendly while keeping the same button design */
    .sari-about-v2 .sari-about-v2__button {
        width: 100% !important;
        min-height: 50px !important;
        box-sizing: border-box;
        justify-content: center !important;
        margin-top: 24px !important;
    }

    /* STATS: compact 2x2 grid, note below */
    .sari-about-v2 .sari-about-v2__stats {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        width: calc(100% - 24px) !important;
        margin-inline: auto !important;
        box-sizing: border-box;
    }

    .sari-about-v2 .sari-about-v2__stat {
        min-width: 0;
        box-sizing: border-box;
    }

    .sari-about-v2 .sari-about-v2__stat-note {
        grid-column: 1 / -1 !important;
        box-sizing: border-box;
    }

    /* STORY: image then copy */
    .sari-about-v2 .sari-about-v2__story {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 38px !important;
        width: calc(100% - 36px) !important;
        margin: 72px auto !important;
        box-sizing: border-box;
    }

    .sari-about-v2 .sari-about-v2__story-art {
        order: 1;
        width: 100% !important;
        min-width: 0;
    }

    .sari-about-v2 .sari-about-v2__story-copy {
        order: 2;
        width: 100% !important;
        min-width: 0;
    }

    .sari-about-v2 .sari-about-v2__story-art img {
        display: block;
        max-width: 100% !important;
        height: auto !important;
    }

    .sari-about-v2 .sari-about-v2__story-copy h2 {
        font-size: clamp(36px, 9.5vw, 48px) !important;
        line-height: 1.04 !important;
    }

    .sari-about-v2 .sari-about-v2__story-copy > p {
        font-size: 13px !important;
        line-height: 1.68 !important;
    }

    /* Feature boxes: retain design, improve available width */
    .sari-about-v2 .sari-about-v2__features {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .sari-about-v2 .sari-about-v2__features > div {
        min-width: 0;
        box-sizing: border-box;
    }

    /* VALUES: keep existing card design, simply reflow */
    .sari-about-v2 .sari-about-v2__values {
        padding-left: 18px !important;
        padding-right: 18px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 14px !important;
        margin-top: 38px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article {
        min-width: 0;
        box-sizing: border-box;
    }
}

/* Small phones: one-column cards for better readability */
@media (max-width: 520px) {
    .sari-about-v2 .sari-about-v2__hero-copy {
        padding: 58px 17px 30px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-media {
        width: calc(100% - 20px) !important;
        margin-left: 10px !important;
        margin-right: 10px !important;
        border-radius: 16px;
    }

    .sari-about-v2 .sari-about-v2__hero-title {
        font-size: clamp(36px, 10.8vw, 46px) !important;
    }

    .sari-about-v2 .sari-about-v2__stats {
        width: calc(100% - 20px) !important;
    }

    .sari-about-v2 .sari-about-v2__features {
        grid-template-columns: 1fr !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 360px) {
    .sari-about-v2 .sari-about-v2__hero-copy {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-title {
        font-size: 35px !important;
    }

    .sari-about-v2 .sari-about-v2__lead {
        font-size: 12.5px !important;
    }
}



/* ==========================================================
   MOBILE HERO — CONTENT STAYS INSIDE THE IMAGE
   Desktop design remains unchanged.
   ========================================================== */
@media (max-width: 768px) {
    .sari-about-v2 .sari-about-v2__hero {
        position: relative !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        grid-template-rows: 1fr !important;

        width: calc(100% - 20px) !important;
        min-height: 760px !important;
        margin: 12px auto 24px !important;

        overflow: hidden !important;
        border-radius: 22px !important;
        background: #17140e !important;
    }

    /* Image becomes the full hero canvas */
    .sari-about-v2 .sari-about-v2__hero-media {
        position: relative !important;
        z-index: 1 !important;
        grid-column: 1 !important;
        grid-row: 1 !important;

        width: 100% !important;
        height: 100% !important;
        min-height: 760px !important;
        margin: 0 !important;
        overflow: hidden !important;
        border-radius: inherit !important;
    }

    .sari-about-v2 .sari-about-v2__hero-image {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        min-height: 760px !important;

        object-fit: cover !important;
        object-position: 64% center !important;

        /* Slight zoom so the image still fills the tall mobile canvas */
        transform: scale(1.08);
        transform-origin: center;
    }

    .sari-about-v2 .sari-about-v2__wave {
        display: none !important;
    }

    /* Content is layered INSIDE the image */
    .sari-about-v2 .sari-about-v2__hero-copy {
        position: relative !important;
        z-index: 3 !important;
        grid-column: 1 !important;
        grid-row: 1 !important;

        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-end !important;

        width: 100% !important;
        max-width: none !important;
        min-width: 0 !important;
        box-sizing: border-box !important;

        padding: 92px 20px 24px !important;
        margin: 0 !important;
        background: transparent !important;
    }

    .sari-about-v2 .sari-about-v2__eyebrow {
        margin: 0 0 12px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-title {
        max-width: 620px !important;
        margin: 0 !important;

        font-size: clamp(38px, 10.5vw, 50px) !important;
        line-height: 1.01 !important;
        letter-spacing: -.047em !important;
    }

    .sari-about-v2 .sari-about-v2__lead {
        max-width: 560px !important;
        margin-top: 15px !important;

        font-size: 12.8px !important;
        line-height: 1.62 !important;
    }

    /* Pillars remain within the same image area */
    .sari-about-v2 .sari-about-v2__pillars {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0 !important;

        width: 100% !important;
        max-width: none !important;
        margin-top: 20px !important;
        padding: 0 !important;
    }

    .sari-about-v2 .sari-about-v2__pillar {
        display: grid !important;
        grid-template-columns: 42px minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: center !important;

        width: 100% !important;
        box-sizing: border-box !important;
        padding: 11px 0 !important;
    }

    .sari-about-v2 .sari-about-v2__pillar:first-child {
        padding-top: 0 !important;
    }

    .sari-about-v2 .sari-about-v2__pillar + .sari-about-v2__pillar {
        border-left: 0 !important;
        border-top: 1px solid rgba(57, 45, 28, .10) !important;
    }

    .sari-about-v2 .sari-about-v2__icon {
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px !important;
    }

    .sari-about-v2 .sari-about-v2__pillar strong {
        font-size: 12px !important;
        line-height: 1.25 !important;
    }

    .sari-about-v2 .sari-about-v2__pillar small {
        margin-top: 3px !important;
        font-size: 10px !important;
        line-height: 1.42 !important;
    }

    /* CTA stays inside the image too */
    .sari-about-v2 .sari-about-v2__button {
        width: 100% !important;
        min-height: 50px !important;
        box-sizing: border-box !important;

        justify-content: center !important;
        margin-top: 18px !important;

        color: #ffffff !important;
    }

    .sari-about-v2 .sari-about-v2__button > span {
        color: #ffffff !important;
    }
}

/* Slightly tighter layout for normal small phones */
@media (max-width: 540px) {
    .sari-about-v2 .sari-about-v2__hero,
    .sari-about-v2 .sari-about-v2__hero-media,
    .sari-about-v2 .sari-about-v2__hero-image {
        min-height: 720px !important;
    }

    .sari-about-v2 .sari-about-v2__hero {
        width: calc(100% - 16px) !important;
        border-radius: 19px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-copy {
        padding: 78px 17px 20px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-image {
        object-position: 66% center !important;
        transform: scale(1.12);
    }

    .sari-about-v2 .sari-about-v2__hero-title {
        font-size: clamp(36px, 10.8vw, 46px) !important;
    }

    .sari-about-v2 .sari-about-v2__lead {
        font-size: 12.3px !important;
    }

    .sari-about-v2 .sari-about-v2__pillar {
        padding: 9px 0 !important;
    }

    .sari-about-v2 .sari-about-v2__button {
        min-height: 48px !important;
    }
}

/* Very narrow phones */
@media (max-width: 380px) {
    .sari-about-v2 .sari-about-v2__hero,
    .sari-about-v2 .sari-about-v2__hero-media,
    .sari-about-v2 .sari-about-v2__hero-image {
        min-height: 690px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-copy {
        padding: 68px 15px 18px !important;
    }

    .sari-about-v2 .sari-about-v2__hero-title {
        font-size: 34px !important;
    }

    .sari-about-v2 .sari-about-v2__lead {
        font-size: 12px !important;
        line-height: 1.55 !important;
    }

    .sari-about-v2 .sari-about-v2__icon {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
    }
}



/* ==========================================================
   MOBILE READABILITY — NO WHITE FADE / NO IMAGE OVERLAY
   ========================================================== */
@media (max-width: 768px) {
    .sari-about-v2 .sari-about-v2__hero-copy {
        background: transparent !important;
    }

    .sari-about-v2 .sari-about-v2__eyebrow,
    .sari-about-v2 .sari-about-v2__hero-title,
    .sari-about-v2 .sari-about-v2__lead,
    .sari-about-v2 .sari-about-v2__pillar strong,
    .sari-about-v2 .sari-about-v2__pillar small {
        text-shadow: 0 1px 10px rgba(255, 255, 255, .18);
    }

    /* Keep the existing image completely visible and untouched. */
    .sari-about-v2 .sari-about-v2__hero::before,
    .sari-about-v2 .sari-about-v2__hero::after,
    .sari-about-v2 .sari-about-v2__hero-media::before,
    .sari-about-v2 .sari-about-v2__hero-media::after,
    .sari-about-v2 .sari-about-v2__hero-copy::before,
    .sari-about-v2 .sari-about-v2__hero-copy::after {
        background: transparent !important;
        background-image: none !important;
    }
}



/* ==========================================================
   MOBILE TEXT CONTRAST — NO PHOTO OVERLAY
   Keeps the background image fully untouched.
   ========================================================== */
@media (max-width: 768px) {
    /* No overlay/fade of any kind */
    .sari-about-v2 .sari-about-v2__hero-copy {
        background: transparent !important;
    }

    .sari-about-v2 .sari-about-v2__hero::before,
    .sari-about-v2 .sari-about-v2__hero::after,
    .sari-about-v2 .sari-about-v2__hero-media::before,
    .sari-about-v2 .sari-about-v2__hero-media::after,
    .sari-about-v2 .sari-about-v2__hero-copy::before,
    .sari-about-v2 .sari-about-v2__hero-copy::after {
        background: transparent !important;
        background-image: none !important;
    }

    /* Main headline */
    .sari-about-v2 .sari-about-v2__hero-title {
        color: #ffffff !important;
        text-shadow:
            0 2px 4px rgba(0, 0, 0, .58),
            0 8px 24px rgba(0, 0, 0, .34) !important;
    }

    .sari-about-v2 .sari-about-v2__hero-title em {
        color: #e3a10d !important;
        text-shadow:
            0 2px 4px rgba(0, 0, 0, .55),
            0 7px 20px rgba(0, 0, 0, .30) !important;
    }

    /* Eyebrow */
    .sari-about-v2 .sari-about-v2__eyebrow {
        color: #e2a21c !important;
        text-shadow:
            0 1px 3px rgba(0, 0, 0, .70),
            0 4px 12px rgba(0, 0, 0, .32) !important;
    }

    .sari-about-v2 .sari-about-v2__eyebrow > span {
        background: #e2a21c !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .35);
    }

    /* Description */
    .sari-about-v2 .sari-about-v2__lead {
        color: rgba(255, 255, 255, .92) !important;
        text-shadow:
            0 1px 3px rgba(0, 0, 0, .78),
            0 4px 14px rgba(0, 0, 0, .34) !important;
    }

    /* Pillars */
    .sari-about-v2 .sari-about-v2__pillar strong {
        color: #ffffff !important;
        text-shadow:
            0 1px 3px rgba(0, 0, 0, .76),
            0 4px 12px rgba(0, 0, 0, .28) !important;
    }

    .sari-about-v2 .sari-about-v2__pillar small {
        color: rgba(255, 255, 255, .84) !important;
        text-shadow:
            0 1px 3px rgba(0, 0, 0, .82),
            0 4px 12px rgba(0, 0, 0, .30) !important;
    }

    /* Make separators visible without using a panel */
    .sari-about-v2 .sari-about-v2__pillar + .sari-about-v2__pillar {
        border-top-color: rgba(255, 255, 255, .22) !important;
    }

    /* Icons stay gold but gain a clearer outline on the photo */
    .sari-about-v2 .sari-about-v2__icon {
        color: #e2a21c !important;
        border-color: rgba(226, 162, 28, .72) !important;
        background: rgba(15, 13, 10, .18) !important;
        box-shadow:
            0 4px 14px rgba(0, 0, 0, .16),
            inset 0 0 0 1px rgba(255, 255, 255, .04);
        -webkit-backdrop-filter: blur(2px);
        backdrop-filter: blur(2px);
    }

    /* CTA remains unchanged visually, only ensure text stays white */
    .sari-about-v2 .sari-about-v2__button,
    .sari-about-v2 .sari-about-v2__button > span,
    .sari-about-v2 .sari-about-v2__button:visited,
    .sari-about-v2 .sari-about-v2__button:visited > span {
        color: #ffffff !important;
        text-shadow: none !important;
    }
}



/* ==========================================================
   MOBILE STORY EYEBROW — CLEAN / NO SHADOW
   Keeps "About SARI" hero contrast untouched.
   ========================================================== */
@media (max-width: 768px) {
    .sari-about-v2 .sari-about-v2__story-copy .sari-about-v2__eyebrow {
        text-shadow: none !important;
    }

    .sari-about-v2 .sari-about-v2__story-copy .sari-about-v2__eyebrow > span {
        box-shadow: none !important;
    }
}



/* ==========================================================
   MOBILE OUR VALUES — 2 COLUMNS + CLEAN EYEBROW
   Desktop remains unchanged.
   ========================================================== */
@media (max-width: 768px) {

    /* Remove shadow from "Our Values" only */
    .sari-about-v2 .sari-about-v2__values .sari-about-v2__eyebrow {
        text-shadow: none !important;
    }

    .sari-about-v2 .sari-about-v2__values .sari-about-v2__eyebrow > span {
        box-shadow: none !important;
    }

    /* Keep cards compact: two side-by-side on mobile */
    .sari-about-v2 .sari-about-v2__value-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
        margin-top: 34px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article {
        display: block !important;
        min-width: 0 !important;
        min-height: 170px !important;
        padding: 20px 16px !important;
        border-radius: 18px !important;
        box-sizing: border-box !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article .value-icon {
        width: 44px !important;
        height: 44px !important;
        margin: 0 0 15px !important;
        border-radius: 13px !important;
        font-size: 18px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article h3 {
        margin: 0 !important;
        font-size: 14px !important;
        line-height: 1.25 !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article p {
        margin-top: 7px !important;
        font-size: 10.5px !important;
        line-height: 1.5 !important;
    }
}

/* Very small phones: still keep 2 columns, just tighten spacing */
@media (max-width: 390px) {
    .sari-about-v2 .sari-about-v2__value-grid {
        gap: 9px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article {
        min-height: 158px !important;
        padding: 17px 13px !important;
        border-radius: 16px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article .value-icon {
        width: 40px !important;
        height: 40px !important;
        margin-bottom: 13px !important;
        font-size: 17px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article h3 {
        font-size: 13px !important;
    }

    .sari-about-v2 .sari-about-v2__value-grid article p {
        font-size: 9.5px !important;
        line-height: 1.45 !important;
    }
}



/* ==========================================================
   MOBILE STORY FEATURES — 2 COLUMNS
   Curated / Trust / Seamless / Everyone
   Desktop remains unchanged.
   ========================================================== */
@media (max-width: 768px) {
    .sari-about-v2 .sari-about-v2__features {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
        margin-top: 28px !important;
    }

    .sari-about-v2 .sari-about-v2__features > div {
        display: grid !important;
        grid-template-columns: 36px minmax(0, 1fr) !important;
        gap: 10px !important;
        align-items: start !important;

        min-width: 0 !important;
        min-height: 126px !important;
        box-sizing: border-box !important;

        padding: 16px 14px !important;
        border-radius: 16px !important;
    }

    .sari-about-v2 .sari-about-v2__feature-icon {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
        font-size: 15px !important;
    }

    .sari-about-v2 .sari-about-v2__features p {
        min-width: 0 !important;
        margin: 0 !important;
    }

    .sari-about-v2 .sari-about-v2__features strong {
        display: block !important;
        font-size: 11px !important;
        line-height: 1.28 !important;
    }

    .sari-about-v2 .sari-about-v2__features small {
        display: block !important;
        margin-top: 5px !important;
        font-size: 9.5px !important;
        line-height: 1.45 !important;
    }
}

/* Keep 2 columns even on small phones, just tighten them */
@media (max-width: 390px) {
    .sari-about-v2 .sari-about-v2__features {
        gap: 9px !important;
    }

    .sari-about-v2 .sari-about-v2__features > div {
        grid-template-columns: 32px minmax(0, 1fr) !important;
        gap: 8px !important;
        min-height: 118px !important;
        padding: 14px 11px !important;
        border-radius: 14px !important;
    }

    .sari-about-v2 .sari-about-v2__feature-icon {
        width: 31px !important;
        height: 31px !important;
        font-size: 14px !important;
    }

    .sari-about-v2 .sari-about-v2__features strong {
        font-size: 10px !important;
    }

    .sari-about-v2 .sari-about-v2__features small {
        font-size: 8.8px !important;
        line-height: 1.4 !important;
    }
}


/* Story CTA + Flaticon refinements */
.sari-about-v2 .sari-about-v2__outline-button {
    background: #b88917 !important;
    border-color: #b88917 !important;
    color: #ffffff !important;
    box-shadow: 0 10px 24px rgba(184, 137, 23, 0.18);
    transition: transform 180ms ease, background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease !important;
}
.sari-about-v2 .sari-about-v2__outline-button span { color: #ffffff !important; }
.sari-about-v2 .sari-about-v2__outline-button:hover {
    background: #9f7410 !important;
    border-color: #9f7410 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(159, 116, 16, 0.24);
}
.sari-about-v2 .sari-about-v2__outline-button:focus-visible {
    outline: 3px solid rgba(184, 137, 23, 0.30);
    outline-offset: 3px;
}
.sari-about-v2 .sari-about-v2__feature-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.sari-about-v2 .sari-about-v2__feature-icon i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    font-size: 0.95em;
}
</style>

<style>
/* FINAL VALUE ICON OVERRIDE: colored glyph only, no colored background container */
.sari-about-v2 .sari-about-v2__value-grid article .value-icon,
.sari-about-v2 .sari-about-v2__value-grid article:hover .value-icon {
    width: auto !important;
    height: auto !important;
    min-width: 0 !important;
    min-height: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    padding: 0 !important;
    margin: 0 0 25px !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    transform: none !important;
    font-size: 29px !important;
    line-height: 1 !important;
}
.sari-about-v2 .sari-about-v2__value-grid article .value-icon::before,
.sari-about-v2 .sari-about-v2__value-grid article .value-icon::after { content: none !important; display: none !important; }
.sari-about-v2 .sari-about-v2__value-grid article .value-icon i {
    display: inline-flex !important;
    line-height: 1 !important;
}
.sari-about-v2 .sari-about-v2__icon i { display:inline-flex; line-height:1; font-size:22px; }
@media (max-width: 768px) {
    .sari-about-v2 .sari-about-v2__value-grid article .value-icon {
        width:auto !important; height:auto !important; border-radius:0 !important;
        margin:0 0 15px !important; font-size:21px !important; background:transparent !important;
    }
}
</style>
