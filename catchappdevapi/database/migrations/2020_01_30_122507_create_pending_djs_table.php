<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingDjsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */



    public function up()
    {
        Schema::create('pending_djs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id')->nullable(true);
            $table->string('email')->nullable(true);
            $table->string('name')->nullable(true);
            $table->string('device_token')->nullable(true);
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
        Schema::dropIfExists('pending_djs');
    }


}
