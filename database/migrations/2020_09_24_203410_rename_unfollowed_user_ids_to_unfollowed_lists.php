<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUnfollowedUserIdsToUnfollowedLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unfollowed_lists', function (Blueprint $table) {
            Schema::rename('unfollowed_user_ids', 'unfollowed_lists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unfollowed_lists', function (Blueprint $table) {
            Schema::rename('unfollowed_lists', 'unfollowed_user_ids');
        });
    }
}
