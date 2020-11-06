<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnfollowNextIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unfollow_next_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
            ->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->string('timeline_next_id')->nullable();
            $table->boolean('timeline_flag')->nullable();
            $table->string('lookup_next_id')->nullable();
            $table->boolean('lookup_flag')->nullable();
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
        Schema::dropIfExists('unfollow_next_ids');
    }
}
