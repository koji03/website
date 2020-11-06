<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowerTargetListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_list_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_lists_id')
            ->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->string('description');
            $table->string('id_str');
            $table->string('following');
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
        Schema::dropIfExists('follower_target_lists');
    }
}
