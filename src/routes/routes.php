<?php

Route::group(['middleware' => 'web'], function() {
    Route::get('auth/sch', '\Sztyup\Authsch\LoginController@redirect')->name('authsch.redirect');
    Route::get('auth/sch/callback', '\Sztyup\Authsch\LoginController@callback')->name('authsch.callback');
});
