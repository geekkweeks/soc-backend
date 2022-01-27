<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('client_id', 36);
            $table->char('media_id', 36);
            $table->dateTime('taken_date');
            $table->dateTime('posted_date');
            $table->string('origin_id', 250);
            $table->string('keyword', 250);
            $table->string('title', 250);
            $table->longtext('caption')->nullable();
            $table->longtext('content', 250)->nullable();
            $table->string('permalink', 250)->nullable();
            $table->string('thumblink', 250)->nullable();
            $table->integer('replies')->nullable();
            $table->integer('views')->nullable();
            $table->integer('favs')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('comment')->nullable();
            $table->integer('age')->nullable();
            $table->string('edu', 250)->nullable();
            $table->boolean('spam');
            $table->string('db_name', 250);
            $table->boolean('is_active');
            $table->nullableTimestamps(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feeds');
    }
}
