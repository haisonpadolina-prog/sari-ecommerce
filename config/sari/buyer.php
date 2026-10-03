<?php

return [
    'delivery_fee_per_seller' => (float) env('SARI_BUYER_DELIVERY_FEE', 80),
    'reward_points_per_peso' => (float) env('SARI_BUYER_REWARD_POINTS_PER_PESO', 0.10),
];
