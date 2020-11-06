<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUnfollowListFollowersToUnfollowUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unfollow_users', function (Blueprint $table) {
            Schema::rename('unfollow_list_followers', 'unfollow_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unfollow_users', function (Blueprint $table) {
            Schema::rename('unfollow_users', 'unfollow_list_followers');
        });
    }
}