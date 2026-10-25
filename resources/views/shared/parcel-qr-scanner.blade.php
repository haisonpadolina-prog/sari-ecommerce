@php
    $scanEndpoint = $scanEndpoint ?? '#';
    $backUrl = $backUrl ?? '#';
    $backLabel = $backLabel ?? 'Back';
    $roleLabel = $roleLabel ?? 'Operations';
    $description = $description ?? 'Scan a SARI parcel QR code.';
@endphp

<div
    class="mx-auto max-w-[980px] space-y-4"
    data-sari-qr-scanner
    data-scan-endpoint="{{ $scanEndpoint }}"
>
    <section class="rounded-[22px] border border-[#eadfc9] bg-white p-5 shadow-[0_8px_24px_rgba(41,31,18,.05)] sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="inline-flex rounded-full border border-[#eadfc9] bg-[#fffaf0] px-3 py-1.5 text-[9px] font-semibold uppercase tracking-[.12em] text-[#a66f11]">
                    {{ $roleLabel }} · QR Handoff
                </span>

                <h2 class="mt-3 text-[22px] font-bold tracking-[-.03em] text-[#211d17]">
                    Scan Parcel QR
                </h2>

                <p class="mt-2 max-w-[720px] text-[11px] leading-6 text-[#7f7568]">
                    {{ $description }} The QR only identifies the parcel. Your signed-in account, assignment and current parcel status are still validated by the server before any handoff is recorded.
                </p>
            </div>

            <a
                href="{{ $backUrl }}"
                class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#e2d7c6] bg-white px-4 text-[10px] font-semibold text-[#675d51] transition hover:border-[#d5b87f] hover:text-[#a96f08]"
            >
                ← {{ $backLabel }}
            </a>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-[1.35fr_.65fr]">
        <div class="overflow-hidden rounded-[22px] border border-[#e6ded2] bg-[#171512] shadow-[0_12px_30px_rgba(26,20,13,.08)]">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-[.12em] text-[#d7ab4a]">Live Camera</p>
                    <p class="mt-1 text-[10px] text-white/60">Rear camera is preferred on phones.</p>
                </div>

                <span
                    id="sariQrCameraBadge"
                    class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-[9px] font-semibold text-white/65"
                >
                    Camera Off
                </span>
            </div>

            <div class="relative min-h-[340px] bg-black sm:min-h-[430px]">
                <div id="sariQrReader" class="min-h-[340px] w-full sm:min-h-[430px]"></div>

                <div
                    id="sariQrCameraPlaceholder"
                    class="absolute inset-0 grid place-items-center px-8 text-center"
                >
                    <div>
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl border border-white/10 bg-white/5 text-2xl text-[#d9a12f]">
                            ▣
                        </div>
                        <p class="mt-4 text-[13px] font-semibold text-white">Camera ready when you are</p>
                        <p class="mt-2 text-[10px] leading-5 text-white/50">
                            Tap Open Camera, allow camera permission, then place the printed parcel QR inside the frame.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-white/10 p-4">
                <button
                    type="button"
                    id="sariQrStartButton"
                    class="min-h-10 rounded-xl bg-[#d9950b] px-4 text-[10px] font-bold text-white transition hover:bg-[#c98708]"
                >
                    Open Camera
                </button>

                <button
                    type="button"
                    id="sariQrStopButton"
                    class="min-h-10 rounded-xl border border-white/15 bg-white/5 px-4 text-[10px] font-semibold text-white/75 transition hover:bg-white/10"
                    disabled
                >
                    Stop Camera
                </button>

                <button
                    type="button"
                    id="sariQrScanAnotherButton"
                    class="hidden min-h-10 rounded-xl border border-[#d9950b]/40 bg-[#d9950b]/10 px-4 text-[10px] font-semibold text-[#ffd47a] transition hover:bg-[#d9950b]/15"
                >
                    Scan Another Parcel
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <section class="rounded-[22px] border border-[#e6ded2] bg-white p-5 shadow-[0_8px_24px_rgba(41,31,18,.04)]">
                <p class="text-[9px] font-semibold uppercase tracking-[.12em] text-[#9f9588]">Scan Status</p>

                <div id="sariQrResult" class="mt-4 rounded-2xl border border-[#ebe5dc] bg-[#faf9f7] p-4">
                    <p id="sariQrResultTitle" class="text-[13px] font-bold text-[#2b261f]">Waiting for scan</p>
                    <p id="sariQrResultMessage" class="mt-2 text-[10px] leading-5 text-[#81786d]">
                        No parcel has been scanned yet.
                    </p>

                    <div id="sariQrOrderBlock" class="mt-4 hidden border-t border-[#e7e0d6] pt-4">
                        <p class="text-[8px] font-semibold uppercase tracking-[.12em] text-[#9b9184]">Order</p>
                        <p id="sariQrOrderNumber" class="mt-1 text-[12px] font-bold text-[#211d17]"></p>
                        <p id="sariQrStage" class="mt-1 text-[10px] font-semibold text-[#a8700c]"></p>
                    </div>

                    <a
                        id="sariQrNextLink"
                        href="#"
                        class="mt-4 hidden min-h-10 items-center justify-center rounded-xl bg-[#211d17] px-4 text-[10px] font-semibold text-white"
                    ></a>
                </div>
            </section>

            <section class="rounded-[22px] border border-[#e6ded2] bg-white p-5 shadow-[0_8px_24px_rgba(41,31,18,.04)]">
                <p class="text-[9px] font-semibold uppercase tracking-[.12em] text-[#9f9588]">Camera Fallback</p>
                <p class="mt-2 text-[10px] leading-5 text-[#81786d]">
                    If camera permission is unavailable, you can paste the complete QR payload here. This fallback uses the same server validation as the camera scanner.
                </p>

                <form id="sariQrManualForm" class="mt-4 space-y-3">
                    <textarea
                        id="sariQrManualPayload"
                        rows="4"
                        class="w-full rounded-xl border border-[#dfd8cd] bg-white px-3 py-3 text-[10px] text-[#332d26] outline-none transition focus:border-[#d9950b] focus:ring-4 focus:ring-[#d9950b]/10"
                        placeholder="Paste SARI-PARCEL QR payload"
                    ></textarea>

                    <button
                        type="submit"
                        class="min-h-10 w-full rounded-xl border border-[#dcc897] bg-[#fff8e9] px-4 text-[10px] font-bold text-[#9c6810] transition hover:bg-[#fff3d4]"
                    >
                        Validate Parcel Code
                    </button>
                </form>
            </section>

            <section class="rounded-[18px] border border-[#f0dfb9] bg-[#fff9ec] p-4">
                <p class="text-[9px] font-semibold text-[#9e6a0d]">Camera requirement</p>
                <p class="mt-1 text-[9px] leading-5 text-[#8d7650]">
                    Browser camera access requires HTTPS or localhost. Your preview site can use the camera because it is served over HTTPS. Camera permission must still be allowed in the browser.
                </p>
            </section>
        </div>
    </section>
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

    let scanner = null;
    let cameraRunning = false;
    let processing = false;

    const setResult = (type, title, message, data = null) => {
        resultTitle.textContent = title;
        resultMessage.textContent = message;
        result.className = 'mt-4 rounded-2xl border p-4';

        if (type === 'success') {
            result.classList.add('border-emerald-200', 'bg-emerald-50');
            resultTitle.className = 'text-[13px] font-bold text-emerald-800';
            resultMessage.className = 'mt-2 text-[10px] leading-5 text-emerald-700';
        } else if (type === 'error') {
            result.classList.add('border-rose-200', 'bg-rose-50');
            resultTitle.className = 'text-[13px] font-bold text-rose-800';
            resultMessage.className = 'mt-2 text-[10px] leading-5 text-rose-700';
        } else {
            result.classList.add('border-[#ebe5dc]', 'bg-[#faf9f7]');
            resultTitle.className = 'text-[13px] font-bold text-[#2b261f]';
            resultMessage.className = 'mt-2 text-[10px] leading-5 text-[#81786d]';
        }

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
        if (processing || !payload) {
            return;
        }

        processing = true;
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
                'Parcel scan accepted',
                data.message ?? 'Parcel QR verified successfully.',
                data
            );

            scanAnotherButton.classList.remove('hidden');
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
        cameraBadge.textContent = 'Camera Off';
        cameraBadge.className = 'rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-[9px] font-semibold text-white/65';
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
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                disableFlip: false,
            },
            onDecoded,
            () => {}
        );
    };

    const startCamera = async () => {
        if (cameraRunning || processing) {
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
            cameraBadge.textContent = 'Camera Live';
            cameraBadge.className = 'rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-[9px] font-semibold text-emerald-300';
            setResult('neutral', 'Camera is live', 'Hold the SARI parcel QR steady inside the scan frame.');
        } catch (firstError) {
            try {
                await startWithConfig({ facingMode: 'environment' });
                cameraRunning = true;
                cameraPlaceholder.classList.add('hidden');
                stopButton.disabled = false;
                cameraBadge.textContent = 'Camera Live';
                cameraBadge.className = 'rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-[9px] font-semibold text-emerald-300';
                setResult('neutral', 'Camera is live', 'Hold the SARI parcel QR steady inside the scan frame.');
            } catch (secondError) {
                startButton.disabled = false;
                cameraBadge.textContent = 'Camera Blocked';
                cameraBadge.className = 'rounded-full border border-rose-400/20 bg-rose-400/10 px-3 py-1.5 text-[9px] font-semibold text-rose-300';
                setResult(
                    'error',
                    'Unable to open camera',
                    'Allow camera permission for this site, make sure no other app is using the camera, then try again.'
                );
            }
        }
    };

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
