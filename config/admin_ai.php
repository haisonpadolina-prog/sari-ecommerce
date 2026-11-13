<?php

return [
    'enabled' => env('ADMIN_AI_ENABLED', true),
    // Reuse the same Gemini credential/model as the Buyer assistant.
    // ADMIN_AI_GEMINI_MODEL is an optional override; old ADMIN_AI_MODEL is intentionally ignored.
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('ADMIN_AI_GEMINI_MODEL'),
    'allow_actions' => env('ADMIN_AI_ALLOW_ACTIONS', false),
    'alerts_enabled' => env('ADMIN_AI_ALERTS_ENABLED', false),
    'stale_order_hours' => (int) env('ADMIN_AI_STALE_ORDER_HOURS', 48),
    'retention_days' => (int) env('ADMIN_AI_RETENTION_DAYS', 30),
];
