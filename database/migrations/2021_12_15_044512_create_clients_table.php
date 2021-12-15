<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name', 250);
            $table->string('short_name', 250);
            $table->string('website', 250);
            $table->string('pagetitle', 250);
            $table->string('description', 250);
            $table->string('logo_url', 250);
            $table->string('created_by', 100);
            $table->string('updated_by', 100)->nullable();
            $table->boolean('is_active');
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
        Schema::dropIfExists('clients');
    }
}
