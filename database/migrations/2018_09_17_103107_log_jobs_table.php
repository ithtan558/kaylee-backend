<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LogJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id')->nullable();
            $table->string('method', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->text('params')->nullable();
            $table->text('response')->nullable();
            $table->string('error', 255)->nullable();
            $table->string('http_code', 3)->nullable();
            $table->string('channel', 255)->nullable();
            $table->dateTime('created_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_jobs');
    }
}
