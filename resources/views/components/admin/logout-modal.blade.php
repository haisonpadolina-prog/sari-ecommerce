        <div id="adminLogoutModal" class="hidden" data-admin-logout-root>
            <div class="admin-logout-modal-backdrop" data-admin-logout-backdrop aria-hidden="true"></div>

            <section
                class="admin-logout-modal-dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="adminLogoutModalTitle"
                aria-describedby="adminLogoutModalDescription"
            >
                <div class="admin-logout-modal-body">
                    <div class="admin-logout-modal-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 17l5-5-5-5"></path>
                            <path d="M15 12H3"></path>
                            <path d="M14 3h7v18h-7"></path>
                        </svg>
                    </div>

                    <h2 id="adminLogoutModalTitle" class="admin-logout-modal-title">Log out of SARI?</h2>
                    <p id="adminLogoutModalDescription" class="admin-logout-modal-copy">
                        You will need to sign in again to access your Admin account.
                    </p>
                </div>

                <div class="admin-logout-modal-footer">
                    <button id="adminCancelLogout" type="button"
                        class="admin-logout-modal-btn admin-logout-modal-btn-cancel">Cancel</button>

                    <button id="adminConfirmLogout" type="button"
                        class="admin-logout-modal-btn admin-logout-modal-btn-danger">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M10 17l5-5-5-5"></path>
                            <path d="M15 12H3"></path>
                            <path d="M14 3h7v18h-7"></path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </div>
            </section>
        </div>
    <style id="sariAdminSellerStyleLogoutModal">
/* ============================================================
           BUYER LOGOUT CONFIRMATION MODAL — SAME SELLER DESIGN
           ============================================================ */
        #adminLogoutModal.hidden,
        #adminLogoutModal[hidden] {
            display: none !important;
        }

        #adminLogoutModal {
            position: fixed;
            inset: 0;
            z-index: 11000;
            display: grid;
            place-items: center;
            padding: 22px;
        }

        .admin-logout-modal-backdrop {
            position: absolute;
            inset: 0;
            background: var(--sari-dark-27033fc7d6, rgba(24, 28, 34, .54));
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            transition: opacity 150ms ease;
        }

        .admin-logout-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(100%, 390px);
            overflow: hidden;
            border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB);
            border-radius: 8px;
            background: var(--sari-dark-950baec030, #FFFFFF);
            box-shadow:
                0 30px 80px var(--sari-dark-7edf1b5a37, rgba(15, 23, 42, .24)),
                0 8px 24px var(--sari-dark-c33e3d1bb4, rgba(15, 23, 42, .08));
            opacity: 0;
            transform: translateY(10px) scale(.985);
            transition:
                opacity 160ms ease,
                transform 160ms cubic-bezier(.22, 1, .36, 1);
        }

        #adminLogoutModal:not(.hidden) .admin-logout-modal-backdrop {
            opacity: 1;
        }

        #adminLogoutModal:not(.hidden) .admin-logout-modal-dialog {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .admin-logout-modal-body {
            padding: 21px 21px 18px;
        }

        .admin-logout-modal-icon {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 1px solid var(--sari-dark-d8c2716225, #FFB7BE);
            border-radius: 8px;
            background: var(--sari-dark-950baec030, #FFF0F2);
            color: var(--sari-dark-a9d850885f, #F04438);
            box-shadow: inset 0 0 0 1px var(--sari-dark-f30a5f9061, rgba(255,255,255,.8));
        }

        .admin-logout-modal-icon svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-logout-modal-title {
            margin: 14px 0 0;
            color: var(--sari-dark-e23e64dd1e, #1F2937);
            font-size: 18px;
            font-weight: 700;
            line-height: 1.28;
            letter-spacing: -.035em;
        }

        .admin-logout-modal-copy {
            margin: 6px 0 0;
            max-width: 410px;
            color: var(--sari-dark-a63aa16d7c, #667085);
            font-size: 12px;
            font-weight: 500;
            line-height: 1.6;
        }

        .admin-logout-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid var(--sari-dark-d8c2716225, #EEF0F3);
            background: var(--sari-dark-950baec030, #FAFBFC);
            padding: 10px 12px;
        }

        .admin-logout-modal-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            padding: 0 13px;
            font: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition:
                background-color 120ms ease,
                border-color 120ms ease,
                color 120ms ease,
                box-shadow 120ms ease,
                transform 120ms ease;
        }

        .admin-logout-modal-btn svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-logout-modal-btn-cancel {
            min-width: 84px;
            border: 1px solid var(--sari-dark-d8c2716225, #D8DEE6);
            background: var(--sari-dark-950baec030, #FFFFFF);
            color: var(--sari-dark-e23e64dd1e, #475467);
            box-shadow: 0 1px 2px var(--sari-dark-b13200ac05, rgba(16, 24, 40, .03));
        }

        .admin-logout-modal-btn-cancel:hover {
            border-color: var(--sari-dark-d8c2716225, #C7CED8);
            background: var(--sari-dark-950baec030, #F7F8FA);
            color: var(--sari-dark-e23e64dd1e, #253142);
        }

        .admin-logout-modal-btn-danger {
            min-width: 96px;
            border: 1px solid #ff0b0b;
            background: #ff0b0b;
            color: #FFFFFF;
            box-shadow:
                0 6px 14px var(--sari-dark-ef22e61893, rgba(127, 29, 45, .20)),
                inset 0 1px 0 var(--sari-dark-d6c0b25c03, rgba(255,255,255,.12));
        }

        .admin-logout-modal-btn-danger:hover {
            border-color: #ff0b0b;
            background: #ff0b0b;
            box-shadow:
                0 8px 18px var(--sari-dark-f846f23b59, rgba(107, 23, 38, .25)),
                inset 0 1px 0 var(--sari-dark-2f91cbc37f, rgba(255,255,255,.10));
            transform: translateY(-1px);
        }

        .admin-logout-modal-btn-danger:active {
            border-color: #ff0b0b;
            background: #ff0b0b;
            box-shadow:
                0 4px 10px var(--sari-dark-e267f3cebc, rgba(86, 17, 31, .22)),
                inset 0 1px 0 var(--sari-dark-5b5634790c, rgba(255,255,255,.08));
            transform: translateY(0);
        }

        .admin-logout-modal-btn-danger:disabled {
            border-color: #9B5360;
            background: #9B5360;
            box-shadow: none;
            opacity: .78;
            cursor: wait;
            transform: none;
        }

        .admin-logout-modal-btn:focus-visible {
            outline: 3px solid rgba(127, 29, 45, .18);
            outline-offset: 2px;
        }

        @media (max-width: 520px) {
            #adminLogoutModal {
                align-items: end;
                padding: 10px;
            }

            .admin-logout-modal-dialog {
                width: 100%;
                border-radius: 8px;
            }

            .admin-logout-modal-body {
                padding: 20px 18px 17px;
            }

            .admin-logout-modal-title {
                font-size: 17px;
            }

            .admin-logout-modal-copy {
                font-size: 12px;
            }

            .admin-logout-modal-footer {
                display: grid;
                grid-template-columns: 1fr 1fr;
                padding: 10px;
            }

            .admin-logout-modal-btn {
                width: 100%;
                min-width: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .admin-logout-modal-backdrop,
            .admin-logout-modal-dialog,
            .admin-logout-modal-btn {
                transition: none !important;
            }
        }
    </style>

<script>
(function () {
    if (window.__SARI_ADMIN_LOGOUT_MODAL__) return;
    window.__SARI_ADMIN_LOGOUT_MODAL__ = true;
    let pendingForm = null;
    let previousFocus = null;
    let previousOverflow = '';
    const root = () => document.getElementById('adminLogoutModal');
    const isOpen = () => root() && !root().classList.contains('hidden');
    function closeModal() {
        if (!isOpen()) return;
        root().classList.add('hidden');
        document.body.style.overflow = previousOverflow;
        pendingForm = null;
        previousFocus?.isConnected && previousFocus.focus();
    }
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('[data-admin-logout-form]') || !root()) return;
        event.preventDefault();
        if (isOpen()) return;
        pendingForm = form;
        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        root().classList.remove('hidden');
        document.getElementById('adminCancelLogout')?.focus();
    }, true);
    document.addEventListener('click', function (event) {
        if (event.target.closest('#adminCancelLogout, [data-admin-logout-backdrop]')) closeModal();
        if (!event.target.closest('#adminConfirmLogout') || !pendingForm) return;
        const form = pendingForm;
        pendingForm = null;
        const button = document.getElementById('adminConfirmLogout');
        button.disabled = true;
        button.querySelector('span').textContent = 'Logging out…';
        HTMLFormElement.prototype.submit.call(form);
    });
    document.addEventListener('keydown', function (event) {
        if (!isOpen()) return;
        if (event.key === 'Escape') { event.preventDefault(); closeModal(); }
        if (event.key !== 'Tab') return;
        const buttons = Array.from(root().querySelectorAll('button:not(:disabled)'));
        if (!buttons.length) { event.preventDefault(); return; }
        const first = buttons[0], last = buttons[buttons.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
    document.addEventListener('livewire:navigating', closeModal);
    window.addEventListener('pageshow', () => {
        closeModal();
        const button = document.getElementById('adminConfirmLogout');
        if (button) { button.disabled = false; button.querySelector('span').textContent = 'Logout'; }
    });
})();
</script>
