<?php

Route::get('auth/sch', '\Sztyup\Authsch\LoginController@redirect');
Route::get('auth/sch/callback', '\Sztyup\Authsch\LoginController@callback');