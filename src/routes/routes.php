<?php

Route::group([
    'middleware' => 'web',
    'namespace' => '\\Sztyup\\Authsch\\'
], function() {
    Route::get('auth/sch', 'LoginController@redirect')->name('authsch.redirect');
    Route::get('auth/sch/callback', 'LoginController@callback')->name('authsch.callback');
});
