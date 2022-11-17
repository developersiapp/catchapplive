<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClubStreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('club_stream', function ($table) {
            $table->integer('female_listeners')->after('connection_code')->default(0);
            $table->integer('male_listeners')->after('female_listeners')->default(0);

            $table->timestamp('stream_time')->after('male_listeners')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('club_stream', function ($table){
         $table->dropColumn('female_listeners','male_listeners','stream_time');
      });
    }
}
