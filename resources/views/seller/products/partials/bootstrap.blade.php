<script>
(function () {
    const libraryUrl = @json(route('seller.products.library'));
    const sellerId = @json((int) ($sellerAccount->id ?? 0));
    const cacheKey = 'sari:seller-products-library:v6:' + sellerId + ':' + libraryUrl;

    try {
        window.sessionStorage.removeItem('sari:seller-products-library:v5:' + libraryUrl);
    } catch (_) {}
    const maxCacheAgeMs = 10 * 60 * 1000;
    const serverInitial = @json($initialProductLibrary ?? null);

    window.__SARI_PRODUCTS_LIBRARY_URL__ = libraryUrl;
    window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__ = cacheKey;
    window.__SARI_PRODUCTS_SERVER_INITIAL__ = serverInitial;

    function isCompleteLibrary(data) {
        if (!data || !Array.isArray(data.products)) return false;

        const declaredCount = Number(data.count);

        if (Number.isFinite(declaredCount)) {
            return data.products.length >= Math.max(0, declaredCount);
        }

        return !Object.prototype.hasOwnProperty.call(data, 'initial_count');
    }

    /*
     * Reuse a complete Product Library cache before falling back to the
     * server-rendered first 12 products. Most importantly, never overwrite a
     * complete cache with the partial first-screen payload on every visit.
     */
    let cachedEntry = null;

    try {
        const raw = window.sessionStorage.getItem(cacheKey);

        if (raw) {
            const stored = JSON.parse(raw);
            const age = Date.now() - Number(stored?.saved_at || 0);

            if (
                stored?.data
                && Array.isArray(stored.data.products)
                && age >= 0
                && age <= maxCacheAgeMs
                && isCompleteLibrary(stored.data)
            ) {
                cachedEntry = stored;
            }
        }
    } catch (_) {}

    const selectedLibrary = cachedEntry?.data || serverInitial || null;
    const selectedLibraryIsComplete = isCompleteLibrary(selectedLibrary);

    if (selectedLibrary && Array.isArray(selectedLibrary.products)) {
        window.__SARI_PRODUCTS_LIBRARY_CACHED__ = selectedLibrary;
        window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = selectedLibraryIsComplete;
        window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = cachedEntry?.saved_at || Date.now();

        /*
         * Only persist a complete payload. Serializing the partial first-screen
         * data during every Livewire navigation adds synchronous work and used
         * to destroy the useful full-library cache.
         */
        if (!cachedEntry && selectedLibraryIsComplete) {
            try {
                window.sessionStorage.setItem(
                    cacheKey,
                    JSON.stringify({
                        saved_at: Date.now(),
                        data: selectedLibrary
                    })
                );
            } catch (_) {}
        }
    }

    window.__SARI_PRODUCTS_EARLY_ABORT__?.abort();
    const earlyAbort = new AbortController();
    window.__SARI_PRODUCTS_EARLY_ABORT__ = earlyAbort;

    function fetchFullLibrary() {
        return fetch(libraryUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            /*
             * Respect the endpoint/browser cache. Dashboard may already have
             * warmed this exact JSON response before Product Management opens.
             * Mutation refreshes still use no-store from seller-products.js.
             */
            cache: 'default',
            signal: earlyAbort.signal
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load Product Library.');
                }

                return response.json();
            })
            .then(function (data) {
                window.__SARI_PRODUCTS_LIBRARY_CACHED__ = data;
                window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = isCompleteLibrary(data);
                window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = Date.now();

                if (window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__) {
                    try {
                        window.sessionStorage.setItem(
                            cacheKey,
                            JSON.stringify({
                                saved_at: Date.now(),
                                data: data
                            })
                        );
                    } catch (_) {}
                }

                return data;
            })
            .catch(function (error) {
                if (error?.name !== 'AbortError') {
                    window.__SARI_PRODUCTS_LIBRARY_PREFETCH_ERROR__ = error;
                }
                return null;
            });
    }

    /*
     * The first 12 cards are already server-rendered. When a complete cache is
     * available, let the browser paint first and revalidate later so the JSON
     * request cannot compete with the navigation-critical CSS/JS/images.
     */
    let idleHandle = null;
    let timeoutHandle = null;

    window.__SARI_PRODUCTS_LIBRARY_PROMISE__ =
        new Promise(function (resolve) {
            const run = function () {
                if (earlyAbort.signal.aborted) {
                    resolve(null);
                    return;
                }

                fetchFullLibrary().then(resolve);
            };

            const scheduleIdle = function () {
                if (earlyAbort.signal.aborted) {
                    resolve(null);
                    return;
                }

                if ('requestIdleCallback' in window) {
                    idleHandle = window.requestIdleCallback(run, {
                        timeout: selectedLibraryIsComplete ? 1400 : 700
                    });
                } else {
                    timeoutHandle = window.setTimeout(
                        run,
                        selectedLibraryIsComplete ? 260 : 180
                    );
                }
            };

            if (selectedLibraryIsComplete) {
                timeoutHandle = window.setTimeout(function () {
                    timeoutHandle = null;
                    scheduleIdle();
                }, 650);
            } else {
                scheduleIdle();
            }

            document.addEventListener('livewire:navigating', function () {
                if (idleHandle !== null && 'cancelIdleCallback' in window) {
                    window.cancelIdleCallback(idleHandle);
                    idleHandle = null;
                }

                if (timeoutHandle !== null) {
                    window.clearTimeout(timeoutHandle);
                    timeoutHandle = null;
                }

                earlyAbort.abort();
                resolve(null);
            }, { once: true });
        });
})();
</script>
