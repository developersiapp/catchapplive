<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubWebStreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_web_stream', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('club_id');
            $table->string('stream_id');
            $table->string('stream_url');
            $table->integer('female_listeners')->default(0);
            $table->integer('male_listeners')->default(0);
            $table->string('traffic')->nullable(true);
            $table->integer('updated_by_dj')->nullable(true);
            $table->timestamp('stream_time')->nullable(true);
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
        Schema::dropIfExists('club_web_stream');
    }
}
