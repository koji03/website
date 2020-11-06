<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUnfollowListsToUnfollowedUserIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unfollowed_user_ids', function (Blueprint $table) {
            Schema::rename('unfollow_lists', 'unfollowed_user_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unfollowed_user_ids', function (Blueprint $table) {
            Schema::rename('unfollowed_user_ids', 'unfollow_lists');
        });
    }
}
