<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookNurseNutritionistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_nurse_nutritionists', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id');
            $table->string('partner_id')->nullable();
            $table->longText('description');
            $table->string('address');
            $table->longText('comment')->nullable();
            $table->date('date');
            $table->string('hours')->nullable();
            $table->string('days')->nullable();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'COMPLETED'])->default('PENDING');
            $table->enum('type', ['NURSE', 'NUTRITIONIST']);
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
        Schema::dropIfExists('book_nurse_nutritionists');
    }
}
