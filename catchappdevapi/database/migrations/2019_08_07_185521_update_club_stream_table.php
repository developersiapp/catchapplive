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
            $table->string('stream_url')->after('stream_id');
            $table->longText('connection_code')->after('stream_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('club_stream', function (Blueprint $table) {
            $table->dropColumn('stream_url');
            $table->dropColumn('connection_code');
        });
    }
}
