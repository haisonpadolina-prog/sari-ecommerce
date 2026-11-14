@php
    $platformAnnouncementService = app(\App\Services\Platform\PlatformAnnouncementService::class);
    $platformAnnouncementContext = $platformAnnouncementService->currentIdentity(request());
    $platformAnnouncements = $platformAnnouncementService->activeForCurrentSession(request(), 5);
@endphp

@if($platformAnnouncementContext && $platformAnnouncements->isNotEmpty())
    @once
        <style>
            .sari-announcement-overlay {
                position: fixed !important;
                inset: 0 !important;
                z-index: 9999 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 20px !important;
                background: rgba(17, 24, 39, .34) !important;
                backdrop-filter: blur(2px);
                -webkit-backdrop-filter: blur(2px);
                box-sizing: border-box !important;
            }

            .sari-announcement-overlay[hidden],
            .sari-announcement-dialog[hidden] {
                display: none !important;
            }

            .sari-announcement-stack {
                width: min(440px, calc(100vw - 40px)) !important;
                max-width: 440px !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .sari-announcement-dialog {
                --sari-announcement-accent: #b97805;
                position: relative !important;
                width: 100% !important;
                max-width: 440px !important;
                overflow: hidden !important;
                border: 1px solid #e3e7eb !important;
                border-radius: 13px !important;
                background: #ffffff !important;
                color: #202124 !important;
                box-shadow:
                    0 22px 55px rgba(15, 23, 42, .18),
                    0 8px 22px rgba(15, 23, 42, .08) !important;
                font-family: inherit !important;
                box-sizing: border-box !important;
                outline: none !important;
            }

            .sari-announcement-close {
                position: absolute !important;
                top: 14px !important;
                right: 14px !important;
                z-index: 3 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
                min-height: 32px !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 8px !important;
                background: transparent !important;
                color: #77818d !important;
                cursor: pointer !important;
                transition: background .16s ease, color .16s ease !important;
                box-sizing: border-box !important;
            }

            .sari-announcement-close:hover {
                background: #f3f5f7 !important;
                color: #202124 !important;
            }

            .sari-announcement-close:focus-visible,
            .sari-announcement-primary:focus-visible,
            .sari-announcement-action:focus-visible {
                outline: 2px solid rgba(213, 150, 23, .42) !important;
                outline-offset: 2px !important;
            }

            .sari-announcement-close svg {
                display: block !important;
                width: 15px !important;
                height: 15px !important;
                min-width: 15px !important;
                min-height: 15px !important;
                max-width: 15px !important;
                max-height: 15px !important;
            }

            .sari-announcement-body {
                padding: 24px 25px 19px !important;
                box-sizing: border-box !important;
            }

            .sari-announcement-kicker {
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
                min-height: 38px !important;
                padding-right: 44px !important;
                box-sizing: border-box !important;
            }

            .sari-announcement-logo {
                display: block !important;
                width: 58px !important;
                height: auto !important;
                max-width: 58px !important;
                max-height: 34px !important;
                object-fit: contain !important;
                object-position: left center !important;
                flex: 0 0 auto !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .sari-announcement-meta {
                min-width: 0 !important;
            }

            .sari-announcement-label {
                margin: 0 !important;
                color: var(--sari-announcement-accent) !important;
                font-size: 8px !important;
                line-height: 1.25 !important;
                font-weight: 800 !important;
                letter-spacing: .14em !important;
                text-transform: uppercase !important;
            }

            .sari-announcement-source {
                margin: 3px 0 0 !important;
                color: #97a0aa !important;
                font-size: 9px !important;
                line-height: 1.35 !important;
                font-weight: 500 !important;
            }

            .sari-announcement-title {
                margin: 18px 0 0 !important;
                padding: 0 !important;
                color: #202124 !important;
                font-size: 18px !important;
                line-height: 1.28 !important;
                font-weight: 750 !important;
                letter-spacing: -.02em !important;
                overflow-wrap: anywhere !important;
            }

            .sari-announcement-image {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                max-height: 220px !important;
                margin: 13px 0 0 !important;
                border: 1px solid #e7eaee !important;
                border-radius: 9px !important;
                object-fit: cover !important;
                background: #f7f8f9 !important;
            }

            .sari-announcement-message {
                margin: 8px 0 0 !important;
                color: #626c78 !important;
                font-size: 11px !important;
                line-height: 1.7 !important;
                font-weight: 400 !important;
                white-space: pre-line !important;
                overflow-wrap: anywhere !important;
            }

            .sari-announcement-expiry {
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
                margin-top: 14px !important;
                color: #8d97a2 !important;
                font-size: 8px !important;
                line-height: 1.4 !important;
                font-weight: 600 !important;
            }

            .sari-announcement-expiry svg {
                display: block !important;
                flex: 0 0 auto !important;
                width: 12px !important;
                height: 12px !important;
                min-width: 12px !important;
                min-height: 12px !important;
                max-width: 12px !important;
                max-height: 12px !important;
            }

            .sari-announcement-footer {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 8px !important;
                padding: 12px 24px 14px !important;
                border-top: 1px solid #eef0f2 !important;
                background: #fafbfc !important;
                box-sizing: border-box !important;
            }

            .sari-announcement-primary,
            .sari-announcement-action {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 36px !important;
                padding: 0 18px !important;
                border-radius: 7px !important;
                font-family: inherit !important;
                font-size: 9px !important;
                line-height: 1 !important;
                font-weight: 800 !important;
                text-decoration: none !important;
                cursor: pointer !important;
                box-sizing: border-box !important;
                transition: background .16s ease, border-color .16s ease, color .16s ease !important;
            }

            .sari-announcement-primary {
                min-width: 90px !important;
                border: 1px solid #d59617 !important;
                background: #d59617 !important;
                color: #ffffff !important;
                box-shadow: 0 5px 14px rgba(213, 150, 23, .16) !important;
            }

            .sari-announcement-primary:hover {
                border-color: #b97805 !important;
                background: #b97805 !important;
            }

            .sari-announcement-action {
                border: 1px solid #d8dde3 !important;
                background: #ffffff !important;
                color: #4c5661 !important;
            }

            .sari-announcement-action:hover {
                border-color: #c7cdd4 !important;
                background: #f7f8f9 !important;
            }

            .sari-announcement-primary:disabled,
            .sari-announcement-close:disabled {
                cursor: wait !important;
                opacity: .6 !important;
            }

            @media (max-width: 520px) {
                .sari-announcement-overlay {
                    align-items: flex-end !important;
                    padding: 14px !important;
                }

                .sari-announcement-stack,
                .sari-announcement-dialog {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .sari-announcement-body {
                    padding: 21px 19px 16px !important;
                }

                .sari-announcement-logo {
                    width: 52px !important;
                    max-width: 52px !important;
                }

                .sari-announcement-footer {
                    padding: 11px 19px 14px !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .sari-announcement-close,
                .sari-announcement-primary,
                .sari-announcement-action {
                    transition: none !important;
                }
            }
        </style>
    @endonce

    <div
        class="sari-announcement-overlay"
        data-platform-announcements
        data-announcement-audience="{{ $platformAnnouncementContext['audience_role'] }}"
        aria-live="polite"
    >
        <div class="sari-announcement-stack" data-announcement-stack>
            @foreach($platformAnnouncements as $announcement)
                @php
                    $severity = in_array($announcement->severity, ['info', 'warning', 'critical'], true)
                        ? $announcement->severity
                        : 'info';

                    $accent = match($severity) {
                        'critical' => '#B44747',
                        'warning' => '#B97805',
                        default => '#B97805',
                    };

                    $label = match($severity) {
                        'critical' => 'Important update',
                        'warning' => 'Notice',
                        default => 'SARI update',
                    };
                @endphp

                <article
                    data-platform-announcement="{{ $announcement->id }}"
                    data-announcement-dialog
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="announcement-title-{{ $announcement->id }}"
                    aria-describedby="announcement-message-{{ $announcement->id }}"
                    tabindex="-1"
                    class="sari-announcement-dialog"
                    style="--sari-announcement-accent: {{ $accent }};"
                    @if(!$loop->first) hidden @endif
                >
                    <button
                        type="button"
                        data-announcement-dismiss
                        data-read-url="{{ route('platform-announcements.read', $announcement) }}"
                        aria-label="Close announcement"
                        class="sari-announcement-close"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                    <div class="sari-announcement-body">
                        <div class="sari-announcement-kicker">
                            <img
                                src="{{ asset('images/sari-main-logo.png') }}"
                                alt="SARI"
                                class="sari-announcement-logo"
                            >

                            <div class="sari-announcement-meta">
                                <p class="sari-announcement-label">{{ $label }}</p>
                                <p class="sari-announcement-source">Platform announcement</p>
                            </div>
                        </div>

                        <h2 id="announcement-title-{{ $announcement->id }}" class="sari-announcement-title">
                            {{ $announcement->title }}
                        </h2>

                        @if($announcement->image_path)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($announcement->image_path) }}"
                                alt="{{ $announcement->title }}"
                                class="sari-announcement-image"
                                loading="eager"
                            >
                        @endif

                        <p id="announcement-message-{{ $announcement->id }}" class="sari-announcement-message">{{ $announcement->message }}</p>

                        @if($announcement->ends_at)
                            <div class="sari-announcement-expiry">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 7v5l3 2" />
                                </svg>
                                <span>
                                    Available until {{ $announcement->ends_at->copy()->timezone('Asia/Manila')->format('M j, Y · h:i A') }} PHT
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="sari-announcement-footer">
                        @if($announcement->action_url && $announcement->action_label)
                            <a href="{{ $announcement->action_url }}" class="sari-announcement-action">
                                {{ $announcement->action_label }}
                            </a>
                        @endif

                        <button
                            type="button"
                            data-announcement-dismiss
                            data-read-url="{{ route('platform-announcements.read', $announcement) }}"
                            class="sari-announcement-primary"
                        >
                            Okay
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    @once
        <script>
            (() => {
                const wrapperSelector = '[data-platform-announcements]';
                const dialogSelector = '[data-announcement-dialog]';
                const dismissSelector = '[data-announcement-dismiss]';

                const getWrapper = () => document.querySelector(wrapperSelector);

                const getCurrentDialog = () => {
                    const wrapper = getWrapper();

                    return wrapper
                        ? [...wrapper.querySelectorAll(dialogSelector)]
                            .find((dialog) => !dialog.hidden) || null
                        : null;
                };

                const lockPage = () => {
                    if (!getWrapper()) return;

                    document.documentElement.dataset.sariAnnouncementScroll =
                        document.documentElement.style.overflow || '';

                    document.documentElement.style.overflow = 'hidden';

                    requestAnimationFrame(() => {
                        getCurrentDialog()?.focus({ preventScroll: true });
                    });
                };

                const unlockPage = () => {
                    const previous =
                        document.documentElement.dataset.sariAnnouncementScroll || '';

                    document.documentElement.style.overflow = previous;
                    delete document.documentElement.dataset.sariAnnouncementScroll;
                };

                const showNext = () => {
                    const wrapper = getWrapper();

                    if (!wrapper) {
                        unlockPage();
                        return;
                    }

                    const next = [...wrapper.querySelectorAll(dialogSelector)]
                        .find((dialog) => dialog.hidden);

                    if (next) {
                        next.hidden = false;

                        requestAnimationFrame(() => {
                            next.focus({ preventScroll: true });
                        });

                        return;
                    }

                    wrapper.remove();
                    unlockPage();
                };

                const dismiss = async (button) => {
                    const dialog = button.closest(dialogSelector);
                    const url = button.dataset.readUrl;

                    if (
                        !dialog ||
                        !url ||
                        dialog.dataset.dismissing === 'true'
                    ) {
                        return;
                    }

                    dialog.dataset.dismissing = 'true';

                    dialog.querySelectorAll(dismissSelector).forEach((control) => {
                        control.disabled = true;
                    });

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN':
                                    document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error('Unable to dismiss announcement');
                        }

                        dialog.remove();
                        showNext();
                    } catch (error) {
                        delete dialog.dataset.dismissing;

                        dialog.querySelectorAll(dismissSelector).forEach((control) => {
                            control.disabled = false;
                        });
                    }
                };

                document.addEventListener('click', (event) => {
                    const button = event.target.closest(dismissSelector);

                    if (button) {
                        dismiss(button);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;

                    const dialog = getCurrentDialog();
                    const button = dialog?.querySelector(dismissSelector);

                    if (button) {
                        dismiss(button);
                    }
                });

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', lockPage, {
                        once: true,
                    });
                } else {
                    lockPage();
                }
            })();
        </script>
    @endonce
@endif
