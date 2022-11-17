<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDjsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('djs', function ($table) {
            $table->date('birth_date')->after('password')->nullable();
            $table->string('gender', 100)->after('birth_date')->nullable();
            $table->integer('registeration_type')->after('gender')->default(0);
            $table->longText('client_id')->after('registeration_type')->nullable();
            $table->longText('oauth_key')->after('client_id')->nullable();
            $table->string('locatione', 255)->after('profile_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('djs', function ($table) {
            $table->dropColumn('birth_date');
            $table->dropColumn('gender');
            $table->dropColumn('registeration_type');
            $table->dropColumn('client_id');
            $table->dropColumn('oauth_key');
            $table->dropColumn('locatione');
        });
    }
}
