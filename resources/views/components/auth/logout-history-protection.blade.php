{{-- Revalidate the session when browser Back/Forward restores a private page. --}}
<script data-sari-secure-history>
    (() => {
        'use strict';

        const refreshFromServer = () => {
            // A full request must pass through the Laravel role/session guard.
            window.location.reload();
        };

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                refreshFromServer();
            }
        });

        // Also covers browser history restored by client-side navigation.
        window.addEventListener('popstate', refreshFromServer);
    })();
</script>
