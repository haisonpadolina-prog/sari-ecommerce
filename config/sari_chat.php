<?php

return [
    /*
    | Temporary development channel used by the session-based SARI admin.
    | When the project moves to Laravel Auth, replace this with private
    | authenticated channels.
    */
    'admin_channel' => env(
        'SARI_ADMIN_REALTIME_CHANNEL',
        'sari.admin.' . substr(hash('sha256', (string) env('APP_KEY', 'sari-local')), 0, 32)
    ),
];
