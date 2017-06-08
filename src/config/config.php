<?php

return [
    'user' => [
        'class' => \App\User::class,
        'fields' => [
            'name' => 'name',
            'email' => 'email'
        ]
    ],

    'update_when_login' => false,

    'redirect_route' => 'home'
];