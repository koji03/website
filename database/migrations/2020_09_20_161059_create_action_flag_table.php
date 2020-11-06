<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionFlagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
            ->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->boolean('follow_flag')->nullable();
            $table->boolean('follow_target_flag')->nullable();
            $table->boolean('unfollow_flag')->nullable();
            $table->boolean('unfollow_target_flag')->nullable();
            $table->boolean('like_flag')->nullable();
            $table->boolean('follow_end_flag')->nullable();
            $table->boolean('unfollow_end_flag')->nullable();
            $table->boolean('like_end_flag')->nullable();
            $table->boolean('mail_flag')->nullable();
            $table->boolean('auto_restart_flag')->nullable();
            $table->boolean('error_flag')->nullable();
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
        Schema::dropIfExists('action_flags');
    }
}
