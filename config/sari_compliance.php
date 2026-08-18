<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SARI Marketplace Product Pre-Screening Rules
    |--------------------------------------------------------------------------
    |
    | These are intentionally conservative keyword rules for a school/demo
    | marketplace. They flag listings for HUMAN ADMIN REVIEW. They do not make
    | a legal determination and they should not automatically punish a seller.
    |
    */

    'exceptions' => [
        'water gun',
        'toy gun',
        'glue gun',
        'heat gun',
        'massage gun',
        'gunpla',
    ],

    'rules' => [
        [
            'label' => 'Possible firearm or ammunition listing',
            'risk' => 'high',
            'terms' => [
                'firearm', 'handgun', 'pistol', 'revolver', 'rifle', 'shotgun',
                'ammunition', 'ammo', '9mm bullet', '9mm ammunition', 'silencer',
            ],
        ],
        [
            'label' => 'Possible explosive or weapon listing',
            'risk' => 'high',
            'terms' => [
                'grenade', 'dynamite', 'explosive', 'bomb', 'detonator',
            ],
        ],
        [
            'label' => 'Possible illegal drug listing',
            'risk' => 'high',
            'terms' => [
                'shabu', 'methamphetamine', 'meth', 'cocaine', 'heroin',
                'ecstasy', 'mdma', 'magic mushroom', 'psychedelic mushroom',
            ],
        ],
        [
            'label' => 'Possible counterfeit or stolen goods listing',
            'risk' => 'high',
            'terms' => [
                'counterfeit', 'fake designer', 'replica luxury', 'stolen goods',
                'fake passport', 'fake id',
            ],
        ],
        [
            'label' => 'Restricted nicotine or vape product',
            'risk' => 'medium',
            'terms' => [
                'vape', 'vaping', 'e-cigarette', 'cigarette', 'nicotine pouch',
            ],
        ],
        [
            'label' => 'Restricted alcoholic beverage',
            'risk' => 'medium',
            'terms' => [
                'whisky', 'whiskey', 'vodka', 'tequila', 'rum bottle',
                'beer case', 'alcoholic beverage',
            ],
        ],
        [
            'label' => 'Possible gambling product or service',
            'risk' => 'medium',
            'terms' => [
                'online casino', 'sports betting', 'betting account', 'slot machine',
            ],
        ],
    ],
];
