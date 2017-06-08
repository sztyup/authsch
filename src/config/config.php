<?php

return [
    'user' => [
        'class' => \App\User::class,
        'fields' => [
            'name' => 'name',
            'email' => 'email'
        ]
    ],

    'driver' => [
        'client_id' => env("AUTHSCH_CLIENT_ID"),
        'client_secret' => env("AUTHSCH_CLIENT_SECRET"),
        'redirect' => 'authsch.callback'
    ],

    'update_when_login' => false,

    'redirect_route' => 'home'
];