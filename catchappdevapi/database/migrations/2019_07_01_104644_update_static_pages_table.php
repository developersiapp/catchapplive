<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStaticPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('static_pages', function($table) {
            $table->string('title')->after('type');
            $table->integer('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('static_pages', 'title'))
        {
            Schema::table('static_pages', function (Blueprint $table)
            {
                $table->dropColumn('title');
            });
        }
    }
}
