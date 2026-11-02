{{-- SARI Rider QR Scanner — immersive gold interface. Logistics scanner remains unchanged. --}}
<div data-sari-qr-scanner data-scan-endpoint="{{ $scanEndpoint }}">
    <div class="sqr-shell">
        <div class="sqr-scanner" aria-label="Parcel QR verification workspace">
            {{-- Camera layer spans the ENTIRE dark scanner, including behind the floating status panel. --}}
            <div class="sqr-camera-feed">
                <div id="sariQrReader" aria-label="Live parcel QR camera preview"></div>
                <div id="sariQrCameraPlaceholder" class="sqr-placeholder" aria-hidden="true">
                    <div class="sqr-placeholder-content">
                        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 18V8h10M30 8h10v10M40 30v10H30M18 40H8V30"/><rect x="17" y="17" width="14" height="14" rx="2"/><path d="M24 20v8M20 24h8"/></svg>
                        <strong>Ready to scan</strong>
                        <span>Open your camera to begin</span>
                    </div>
                </div>
            </div>
            <section class="sqr-camera-zone" aria-labelledby="sqrCameraTitle">
                <div class="sqr-scan-guide" aria-hidden="true"><span></span><span></span><span></span><span></span><i></i></div>

                <header class="sqr-camera-head">
                    <div class="sqr-camera-head-left">
                        <span class="sqr-head-gold-dot" aria-hidden="true"></span>
                        <div>
                            <h2 id="sqrCameraTitle" class="sqr-card-title">Live Scanner</h2>
                            <p class="sqr-camera-sub">Align the QR code inside the frame</p>
                        </div>
                    </div>
                    <span class="sqr-camera-security">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="15" rx="3"/><circle cx="12" cy="12.5" r="3.25"/></svg>
                        Rear camera preferred
                    </span>
                </header>
                <span id="sariQrCameraBadge" class="sqr-camera-status" data-state="off">Camera off</span>

                <div class="sqr-camera-actions">
                    <button type="button" id="sariQrStartButton" class="sqr-button sqr-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="14" rx="3"/><path d="M9 6l1.5-2h3L15 6"/><circle cx="12" cy="13" r="3.25"/></svg>
                        Open Camera
                    </button>
                    <button type="button" id="sariQrStopButton" class="sqr-button sqr-outline" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                        Stop Camera
                    </button>
                    <button type="button" id="sariQrScanAnotherButton" class="sqr-button sqr-primary hidden">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2 6"/><path d="M20 4v7h-7"/></svg>
                        Scan Another
                    </button>
                </div>
                <p class="sqr-camera-foot">Signed SARI parcel QR codes only</p>
            </section>

            {{-- The status and fallback live INSIDE the same full-width camera container. --}}
            <aside class="sqr-right" aria-label="Scan verification and camera fallback">
                <section class="sqr-status-card" aria-labelledby="sqrScanStatus">
                    <div class="sqr-panel-heading">
                        <span class="sqr-panel-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3H4v3m13-3h3v3M4 17v3h3m13-3v3h-3"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                        </span>
                        <h3 id="sqrScanStatus" class="sqr-section-title">Scan Status</h3>
                    </div>
                    <div id="sariQrResult" class="sqr-result" data-state="neutral" aria-live="polite">
                        <p id="sariQrResultTitle" class="sqr-result-title">Waiting for scan</p>
                        <p id="sariQrResultMessage" class="sqr-result-message">The order will appear here after verification.</p>
                        <div id="sariQrOrderBlock" class="sqr-order hidden">
                            <span class="sqr-order-label">Order</span>
                            <strong id="sariQrOrderNumber" class="sqr-order-number"></strong>
                            <span id="sariQrStage" class="sqr-order-stage"></span>
                        </div>
                        <a id="sariQrNextLink" href="#" class="sqr-next-link hidden"></a>
                    </div>
                </section>
                <div class="sqr-panel-divider" aria-hidden="true"></div>
                <section class="sqr-assist-card">
                    <details class="sqr-fallback" open>
                        <summary>
                            <span class="sqr-panel-heading">
                                <span class="sqr-panel-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 14a5 5 0 0 0 7.1 0l3-3a5 5 0 0 0-7.1-7.1l-1.2 1.2M14 10a5 5 0 0 0-7.1 0l-3 3A5 5 0 0 0 11 20l1.2-1.2"/></svg>
                                </span>
                                <span class="sqr-section-title">Can't use the camera?</span>
                            </span>
                            <svg class="sqr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <p class="sqr-fallback-copy">Paste the signed QR payload as a backup.</p>
                        <form id="sariQrManualForm" class="sqr-manual">
                            <label for="sariQrManualPayload">Parcel QR payload</label>
                            <textarea id="sariQrManualPayload" rows="3" placeholder="Paste the complete QR payload here..." autocomplete="off" spellcheck="false"></textarea>
                            <button type="submit" class="sqr-button sqr-primary sqr-verify">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                                Verify Code
                            </button>
                        </form>
                        <p class="sqr-help-note">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-9v1"/></svg>
                            Camera requires HTTPS or localhost and browser permission.
                        </p>
                    </details>
                </section>
            </aside>
        </div>
    </div>

    {{-- Only the backend's verified Buyer-stage COD QR scan can open this dialog. --}}
    <div id="sariQrCodPayment" class="sqr-modal hidden" role="dialog" aria-modal="true" aria-labelledby="sqrPaymentTitle" aria-live="polite">
        <div class="sqr-dialog" tabindex="-1" id="sariQrPaymentDialog">
            <div class="sqr-dialog-top">
                <div class="sqr-dialog-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="9"/></svg></div>
                <div>
                    <p class="sqr-dialog-kicker">Parcel QR verified</p>
                    <h3 class="sqr-dialog-title" id="sqrPaymentTitle">Receive COD payment</h3>
                </div>
            </div>
            <div class="sqr-payment-summary">
                <p class="sqr-payment-label">Amount to collect</p>
                <strong id="sariQrCodAmount" class="sqr-payment-amount" aria-label="COD amount due"></strong>
                <div class="sqr-payment-meta"><span>Cash on Delivery</span><strong id="sariQrPaymentOrder"></strong></div>
            </div>
            <label class="sqr-cash-affirm">
                <input id="sariQrCashReceivedCheck" type="checkbox">
                <span>I have received the full cash amount from the buyer.</span>
            </label>
            <button id="sariQrConfirmCashButton" type="button" class="sqr-pay-confirm" disabled>Confirm Payment &amp; Complete Delivery</button>
            <p id="sariQrPaymentError" class="sqr-payment-error hidden" role="alert"></p>
            <button id="sariQrRescanInsteadButton" type="button" class="sqr-pay-cancel">Cancel and scan again</button>
            <p class="sqr-pay-disclaimer">Confirm only after receiving cash. Remittance to Logistics is separate.</p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(() => {
    const root = document.querySelector('[data-sari-qr-scanner]');

    if (!root) {
        return;
    }

    const endpoint = root.dataset.scanEndpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const startButton = document.getElementById('sariQrStartButton');
    const stopButton = document.getElementById('sariQrStopButton');
    const scanAnotherButton = document.getElementById('sariQrScanAnotherButton');
    const cameraBadge = document.getElementById('sariQrCameraBadge');
    const cameraPlaceholder = document.getElementById('sariQrCameraPlaceholder');
    const result = document.getElementById('sariQrResult');
    const resultTitle = document.getElementById('sariQrResultTitle');
    const resultMessage = document.getElementById('sariQrResultMessage');
    const orderBlock = document.getElementById('sariQrOrderBlock');
    const orderNumber = document.getElementById('sariQrOrderNumber');
    const stage = document.getElementById('sariQrStage');
    const nextLink = document.getElementById('sariQrNextLink');
    const manualForm = document.getElementById('sariQrManualForm');
    const manualPayload = document.getElementById('sariQrManualPayload');
    const paymentPanel = document.getElementById('sariQrCodPayment');
    const codAmount = document.getElementById('sariQrCodAmount');
    const cashReceivedCheck = document.getElementById('sariQrCashReceivedCheck');
    const confirmCashButton = document.getElementById('sariQrConfirmCashButton');
    const paymentError = document.getElementById('sariQrPaymentError');
    let pendingPayment = null;

    let scanner = null;
    let cameraRunning = false;
    let processing = false;

    const setResult = (type, title, message, data = null) => {
        resultTitle.textContent = title;
        resultMessage.textContent = message;
        result.dataset.state = type;


        if (data?.order_number) {
            orderNumber.textContent = data.order_number;
            stage.textContent = data.stage ?? '';
            orderBlock.classList.remove('hidden');
        } else {
            orderBlock.classList.add('hidden');
        }

        if (data?.next_url && data?.next_label) {
            nextLink.href = data.next_url;
            nextLink.textContent = data.next_label;
            nextLink.classList.remove('hidden');
            nextLink.classList.add('inline-flex');
        } else {
            nextLink.classList.add('hidden');
            nextLink.classList.remove('inline-flex');
        }
    };

    const extractError = async (response) => {
        let payload = null;

        try {
            payload = await response.json();
        } catch (error) {
            return 'The server could not process this parcel scan.';
        }

        if (payload?.errors) {
            const first = Object.values(payload.errors).flat()[0];

            if (first) {
                return first;
            }
        }

        return payload?.message ?? 'The parcel scan could not be completed.';
    };

    const submitPayload = async (payload) => {
        if (processing || !payload || pendingPayment) {
            return;
        }

        processing = true;
        pendingPayment = null;
        paymentPanel?.classList.add('hidden');
        if (cashReceivedCheck) cashReceivedCheck.checked = false;
        if (confirmCashButton) confirmCashButton.disabled = true;
        setResult('neutral', 'Validating parcel…', 'Checking QR signature, assignment, role and current parcel status.');

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ payload }),
            });

            if (!response.ok) {
                throw new Error(await extractError(response));
            }

            const data = await response.json();

            setResult(
                'success',
                data.requires_payment === true ? 'QR verified — payment pending' : 'Parcel scan accepted',
                data.message ?? 'Parcel QR verified successfully.',
                data
            );

            if (data.requires_payment === true && paymentPanel && data.payment_token && data.order_id) {
                pendingPayment = { order_id: data.order_id, payment_token: data.payment_token };
                codAmount.textContent = '₱' + Number(data.amount_due).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('sariQrPaymentOrder').textContent = String(data.order_number ?? '');
                paymentPanel.classList.remove('hidden');
                document.body.classList.add('sqr-payment-open');
                document.getElementById('sariQrPaymentDialog')?.focus();
                scanAnotherButton.classList.add('hidden');
            } else {
                scanAnotherButton.classList.remove('hidden');
            }
        } catch (error) {
            setResult(
                'error',
                'Scan rejected',
                error?.message ?? 'Unable to validate this parcel QR.'
            );
            scanAnotherButton.classList.remove('hidden');
        } finally {
            processing = false;
        }
    };

    const stopCamera = async () => {
        if (!scanner || !cameraRunning) {
            return;
        }

        try {
            await scanner.stop();
            await scanner.clear();
        } catch (error) {
            // Camera may already be stopped by the browser.
        }

        cameraRunning = false;
        cameraBadge.textContent = 'Camera off';
        cameraBadge.dataset.state = 'off';
        startButton.disabled = false;
        stopButton.disabled = true;
        cameraPlaceholder.classList.remove('hidden');
    };

    const onDecoded = async (decodedText) => {
        if (processing) {
            return;
        }

        await stopCamera();
        await submitPayload(decodedText);
    };

    const startWithConfig = async (cameraConfig) => {
        await scanner.start(
            cameraConfig,
            {
                fps: 10,
                // Scan the full video frame: the reader now spans the whole container.
                // The visible gold guide stays centered in the unobstructed left area.
                disableFlip: false,
            },
            onDecoded,
            () => {}
        );
    };

    const startCamera = async () => {
        if (cameraRunning || processing || pendingPayment) {
            return;
        }

        if (!window.isSecureContext) {
            setResult('error', 'Camera blocked', 'Camera scanning requires HTTPS or localhost.');
            return;
        }

        if (typeof Html5Qrcode === 'undefined') {
            setResult('error', 'Scanner library unavailable', 'The QR camera library did not load. Check the internet connection, then reload this page.');
            return;
        }

        scanner = new Html5Qrcode('sariQrReader');
        startButton.disabled = true;
        cameraBadge.textContent = 'Opening…';
        cameraBadge.dataset.state = 'off';

        try {
            const cameras = await Html5Qrcode.getCameras();
            const preferred = cameras.find((camera) => /back|rear|environment/i.test(camera.label))
                ?? cameras[cameras.length - 1];

            if (preferred?.id) {
                await startWithConfig(preferred.id);
            } else {
                await startWithConfig({ facingMode: 'environment' });
            }

            cameraRunning = true;
            cameraPlaceholder.classList.add('hidden');
            stopButton.disabled = false;
            cameraBadge.textContent = 'Camera live';
            cameraBadge.dataset.state = 'live';
            setResult('neutral', 'Camera is live', 'Hold the SARI parcel QR steady inside the scan frame.');
        } catch (firstError) {
            try {
                await startWithConfig({ facingMode: 'environment' });
                cameraRunning = true;
                cameraPlaceholder.classList.add('hidden');
                stopButton.disabled = false;
                cameraBadge.textContent = 'Camera live';
                cameraBadge.dataset.state = 'live';
                setResult('neutral', 'Camera is live', 'Hold the SARI parcel QR steady inside the scan frame.');
            } catch (secondError) {
                startButton.disabled = false;
                cameraBadge.textContent = 'Camera blocked';
                cameraBadge.dataset.state = 'error';
                setResult(
                    'error',
                    'Unable to open camera',
                    'Allow camera permission for this site, make sure no other app is using the camera, then try again.'
                );
            }
        }
    };

    cashReceivedCheck?.addEventListener('change', () => {
        if (confirmCashButton) confirmCashButton.disabled = !cashReceivedCheck.checked || !pendingPayment || processing;
    });

    confirmCashButton?.addEventListener('click', async () => {
        if (!pendingPayment || !cashReceivedCheck?.checked || processing) return;
        processing = true;
        confirmCashButton.disabled = true;
        paymentError.classList.add('hidden');
        const proof = pendingPayment;
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    action: 'confirm_cod_payment', order_id: proof.order_id,
                    payment_token: proof.payment_token, cash_received: true,
                }),
            });
            if (!response.ok) throw new Error(await extractError(response));
            const data = await response.json();
            pendingPayment = null;
            paymentPanel.classList.add('hidden');
            document.body.classList.remove('sqr-payment-open');
            cashReceivedCheck.checked = false;
            setResult('success', 'Payment received — delivery complete', data.message, data);
            scanAnotherButton.classList.remove('hidden');
        } catch (error) {
            paymentError.textContent = error?.message ?? 'Unable to record COD payment.';
            paymentError.classList.remove('hidden');
            confirmCashButton.disabled = false;
        } finally {
            processing = false;
        }
    });

    document.getElementById('sariQrRescanInsteadButton')?.addEventListener('click', () => {
        if (processing) return;
        pendingPayment = null;
        paymentPanel?.classList.add('hidden');
        document.body.classList.remove('sqr-payment-open');
        if (cashReceivedCheck) cashReceivedCheck.checked = false;
        if (confirmCashButton) confirmCashButton.disabled = true;
        if (paymentError) paymentError.classList.add('hidden');
        setResult('neutral', 'Payment not recorded', 'Scan the parcel again when you are ready to collect payment.');
        scanAnotherButton.classList.remove('hidden');
        scanAnotherButton.focus();
    });

    startButton.addEventListener('click', startCamera);
    stopButton.addEventListener('click', stopCamera);

    scanAnotherButton.addEventListener('click', async () => {
        scanAnotherButton.classList.add('hidden');
        manualPayload.value = '';
        setResult('neutral', 'Ready for another parcel', 'Open the camera and scan the next SARI parcel QR.');
        await startCamera();
    });

    manualForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        await stopCamera();
        await submitPayload(manualPayload.value.trim());
    });

    window.addEventListener('pagehide', () => {
        if (scanner && cameraRunning) {
            scanner.stop().catch(() => {});
        }
    });
})();

</script>
