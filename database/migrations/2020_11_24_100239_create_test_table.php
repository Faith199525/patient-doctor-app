<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnosis_id');
            $table->string('name');
            $table->unsignedBigInteger('price_in_minor_unit')->nullable();
            $table->string('result')->nullable();
            $table->foreign('diagnosis_id')->references('id')->on('diagnosis');
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('prescription_id');
        });

        Schema::dropIfExists('diagnostic_reports');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test');

        Schema::table('payments', function (Blueprint $table) {
            $table->string('prescription_id');
        });
    }
}
