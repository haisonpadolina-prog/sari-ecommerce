<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SARI Admin Assistant
    |--------------------------------------------------------------------------
    |
    | This assistant is a temporary first-response helper only. It waits for
    | the configured delay and responds only when a human Admin has not yet
    | replied. It is intentionally restricted to SARI marketplace support.
    |
    */
    'enabled' => env('SARI_ADMIN_ASSISTANT_ENABLED', true),

    // Reuse the project's existing Gemini credential by default.
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env(
        'SARI_ADMIN_ASSISTANT_MODEL',
        env('SARI_AI_PRODUCT_REVIEW_MODEL', 'gemini-3.5-flash-lite')
    ),
    'endpoint_base' => env(
        'SARI_ADMIN_ASSISTANT_ENDPOINT',
        'https://generativelanguage.googleapis.com/v1beta'
    ),

    // Give the human Admin a short window to answer first.
    'delay_seconds' => (int) env('SARI_ADMIN_ASSISTANT_DELAY_SECONDS', 0),

    'connect_timeout' => (int) env('SARI_ADMIN_ASSISTANT_CONNECT_TIMEOUT', 5),
    'timeout' => (int) env('SARI_ADMIN_ASSISTANT_TIMEOUT', 15),
    'max_output_tokens' => (int) env('SARI_ADMIN_ASSISTANT_MAX_OUTPUT_TOKENS', 160),

    'fallback_reply' => env(
        'SARI_ADMIN_ASSISTANT_FALLBACK',
        'I can only help with SARI marketplace concerns. A SARI administrator will follow up if your question needs account-specific or order-specific verification.'
    ),
];
