<?php

return [
    'user' => [
        'class' => \App\Models\User::class,
        'fields' => [
            'name' => 'name',
            'email' => 'email'
        ]
    ],

    'driver' => [
        'client_id' => env("AUTHSCH_CLIENT_ID"),
        'client_secret' => env("AUTHSCH_CLIENT_SECRET"),
        'redirect' => 'authsch.callback' // Route where a callback route is found
    ],

    'scopes' => [

    ],

    'redirect_route' => 'home'
];