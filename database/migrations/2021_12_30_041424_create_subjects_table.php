<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('client_id', 36);
            $table->string('title', 250);
            $table->string('color', 250)->nullable();
            $table->integer('order_no')->nullable();
            $table->string('created_by', 100)->nullable();
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
        Schema::dropIfExists('subjects');
    }
}
