<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedAnalysisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_analysis', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('feed_id', 36);
            $table->char('subject_id', 36)->nullable();
            $table->string('talk_about')->nullable();
            $table->string('conversation_type')->nullable();
            $table->text('tags')->nullable();
            $table->string('corporate')->nullable();
            $table->string('education')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('location')->nullable();
            $table->timestamps('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_analysis');
    }
}
