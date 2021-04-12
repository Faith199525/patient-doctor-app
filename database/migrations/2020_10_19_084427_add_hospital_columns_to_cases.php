<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHospitalColumnsToCases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->date('hospital_date')->after('doctor_observation')->nullable();
            $table->text('hospital_comments')->after('doctor_observation')->nullable();
            $table->string('hospital_medications')->after('doctor_observation')->nullable();
            $table->text('hospital_diagnosis')->after('doctor_observation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn('hospital_date');
            $table->dropColumn('hospital_diagnosis');
            $table->dropColumn('hospital_medications');
            $table->dropColumn('hospital_comments');
        });
    }
}
