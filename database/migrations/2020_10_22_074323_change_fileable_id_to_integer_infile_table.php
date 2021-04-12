<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeFileableIdToIntegerInfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('fileable_id');
        });
        Schema::table('files', function (Blueprint $table) {
            $table->bigInteger('fileable_id')->after('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('fileable_id');
        });
        Schema::table('files', function (Blueprint $table) {
            $table->uuid('fileable_id')->after('path');
        });
    }
}
