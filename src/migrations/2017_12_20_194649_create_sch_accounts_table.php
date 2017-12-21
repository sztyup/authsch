<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('sch_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('provider_user_id');
            $table->string('name');
            $table->string('email');
            $table->string('schacc');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->integer('bme_id');
            $table->integer('bme_status');
            $table->string('dormitory')->nullable();
            $table->integer('room_number')->nullable();
            $table->integer('phone')->nullable();
            $table->string('neptun')->nullable();
            $table->timestamps();
        });
    }
}
