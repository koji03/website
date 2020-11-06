<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //twitterテーブル作成
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('oauth_token');
            $table->string('oauth_token_secret');
            $table->string('twitter_user_id');
            $table->string('twitter_screen_name');
            $table->dateTime('follow_limit')->nullable();
            $table->dateTime('unfollow_limit')->nullable();
            $table->dateTime('like_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
