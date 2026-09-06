<?php

return [
    'thresholds' => [
        'medium' => 35,
        'high' => 70,
    ],

    /*
    |--------------------------------------------------------------------------
    | Local fallback rules
    |--------------------------------------------------------------------------
    |
    | Gemini is the context-aware classifier. These rules are a fast fallback
    | and a second signal when the API is unavailable or a very strong phrase
    | is present. Human admin review remains the final enforcement decision.
    |
    */
    'rules' => [
        'controlled_substances_strong' => [
            'label' => 'Controlled / prohibited substances',
            'score' => 95,
            'hard_flag' => true,
            'terms' => [
                'illegal drugs', 'drug for sale', 'drugs for sale',
                'cocaine for sale', 'heroin for sale', 'methamphetamine for sale',
                'shabu for sale', 'fentanyl for sale', 'mdma for sale',
            ],
        ],
        'controlled_substances_context' => [
            'label' => 'Possible controlled substance context',
            'score' => 50,
            'hard_flag' => false,
            'terms' => [
                'drugs', 'cocaine', 'heroin', 'methamphetamine', 'crystal meth',
                'shabu', 'fentanyl', 'mdma', 'ecstasy', 'lsd',
            ],
        ],
        'weapons_strong' => [
            'label' => 'Possible prohibited weapon sale',
            'score' => 92,
            'hard_flag' => true,
            'terms' => [
                'gun for sale', 'firearm for sale', 'ammunition for sale',
                'silencer for sale', 'suppressor for sale', 'grenade for sale',
                'explosive device for sale',
            ],
        ],
        'weapons_context' => [
            'label' => 'Weapon-related context',
            'score' => 50,
            'hard_flag' => false,
            'terms' => [
                'firearm', 'handgun', 'pistol', 'revolver', 'rifle', 'shotgun',
                'ammunition', 'silencer', 'suppressor', 'switchblade',
                'brass knuckles', 'grenade',
            ],
        ],
        'fraudulent_documents' => [
            'label' => 'Fraudulent documents / currency',
            'score' => 92,
            'hard_flag' => true,
            'terms' => [
                'fake id for sale', 'forged id for sale', 'fake passport for sale',
                'forged passport for sale', 'counterfeit money', 'fake money for sale',
                'forged certificate for sale',
            ],
        ],
        'counterfeit_goods' => [
            'label' => 'Possible counterfeit merchandise',
            'score' => 55,
            'hard_flag' => false,
            'terms' => [
                'counterfeit', 'fake branded', 'replica branded', 'class a replica',
                'mirror copy', 'unauthorized replica', '1:1 copy original brand',
            ],
        ],
        'hazardous_materials' => [
            'label' => 'Hazardous / toxic materials',
            'score' => 88,
            'hard_flag' => true,
            'terms' => [
                'cyanide for sale', 'mercury for sale', 'radioactive material for sale',
                'toxic poison for sale', 'industrial explosive for sale',
            ],
        ],
        'stolen_goods' => [
            'label' => 'Possible stolen goods / accounts',
            'score' => 70,
            'hard_flag' => false,
            'terms' => [
                'stolen phone', 'stolen iphone', 'stolen laptop', 'nakaw na phone',
                'hacked account for sale', 'stolen account for sale',
            ],
        ],
        'wildlife_trafficking' => [
            'label' => 'Possible illegal wildlife trade',
            'score' => 90,
            'hard_flag' => true,
            'terms' => [
                'ivory tusk for sale', 'rhino horn for sale', 'pangolin scales for sale',
                'endangered animal parts for sale', 'illegal wildlife for sale',
            ],
        ],
    ],

    'ai' => [
        'provider' => 'gemini',
        'enabled' => env('SARI_AI_PRODUCT_REVIEW_ENABLED', true),
        'api_key' => env('GEMINI_API_KEY'),

        /*
         * Free-tier-first default as of August 2026.
         * If your Google AI project does not expose this model, set the env
         * value to gemini-3.6-flash (which you can test independently).
         */
        'model' => env('SARI_AI_PRODUCT_REVIEW_MODEL', 'gemini-3.5-flash-lite'),

        'endpoint_base' => env(
            'SARI_GEMINI_ENDPOINT_BASE',
            'https://generativelanguage.googleapis.com/v1beta'
        ),
        'connect_timeout' => (int) env('SARI_AI_CONNECT_TIMEOUT', 8),
        'timeout' => (int) env('SARI_AI_TIMEOUT', 35),
        'max_output_tokens' => (int) env('SARI_AI_MAX_OUTPUT_TOKENS', 1500),

        'policy_categories' => [
            'weapons' => 'Weapons, weapon parts, ammunition, explosives, or dangerous weapon-related merchandise prohibited or restricted by SARI policy.',
            'controlled_substances' => 'Illegal or controlled drugs/substances and listings that appear to sell or distribute them.',
            'hazardous_materials' => 'Poisons, toxic chemicals, radioactive materials, dangerous explosives, or other hazardous goods requiring prohibition/restriction.',
            'counterfeit_goods' => 'Counterfeit, fake-branded, deceptive replica, or authenticity-misrepresented merchandise.',
            'fraudulent_documents_services' => 'Forged IDs/passports/certificates, counterfeit currency, fraudulent documents, or services that facilitate fraud.',
            'stolen_goods_accounts' => 'Listings that appear to knowingly sell stolen property, hacked accounts, stolen accounts, or unlawfully obtained access.',
            'illegal_wildlife' => 'Protected/endangered wildlife, prohibited animal parts, or listings indicating illegal wildlife trade.',
            'regulated_restricted' => 'Goods that may require age limits, prescriptions, permits, licenses, professional handling, or jurisdiction-specific restrictions and therefore require human review.',
            'malicious_or_surveillance_tools' => 'Malware, spyware, credential-theft tools, covert surveillance products/services, or tools primarily intended for malicious access.',
            'self_harm_enabling_goods' => 'Products primarily marketed to facilitate self-harm or suicide rather than ordinary legitimate use.',
            'other_prohibited' => 'Other merchandise that strongly appears illegal, dangerous, prohibited, or unsuitable for marketplace sale under SARI policy.',
        ],
    ],
];
