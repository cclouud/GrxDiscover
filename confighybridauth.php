
<?

return [
    'callback' => env('APP_URL') . '/auth/callback', // URL de redirección después del login

    'providers' => [
        'Google' => [
            'enabled' => true,
            'keys'    => [
                'id' => env('GOOGLE_CLIENT_ID'),
                'secret' => env('GOOGLE_CLIENT_SECRET'),
            ],
        ],
        'Facebook' => [
            'enabled' => true,
            'keys'    => [
                'id' => env('FACEBOOK_APP_ID'),
                'secret' => env('FACEBOOK_APP_SECRET'),
            ],
        ],
    ],
];
