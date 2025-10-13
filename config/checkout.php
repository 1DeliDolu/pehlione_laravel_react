<?php

return [
    'currency' => 'EUR',

    'shipping' => [
        'domestic_country' => 'DE',
        'domestic_flat_rate' => 12.5,
        'international_flat_rate' => 29.9,
        'domestic_free_threshold' => 500.0,
        'default_method' => 'Standard',
    ],

    'payment_methods' => [
        [
            'id' => 'paypal',
            'label' => 'PayPal',
            'description' => 'Pay securely using your PayPal account.',
        ],
        [
            'id' => 'credit_card',
            'label' => 'Credit Card',
            'description' => 'Visa, MasterCard, American Express.',
        ],
        [
            'id' => 'klarna',
            'label' => 'Klarna Pay Later',
            'description' => 'Buy now, pay later with Klarna.',
        ],
        [
            'id' => 'debit_card',
            'label' => 'Debit Card',
            'description' => 'Use your Maestro or EC card.',
        ],
        [
            'id' => 'sofort',
            'label' => 'Sofortüberweisung',
            'description' => 'Instant bank transfer popular in Germany.',
        ],
    ],

    'notifications' => [
        'warehouse_email' => env('WAREHOUSE_ALERT_EMAIL', 'lager@pehlione.com'),
    ],
];
