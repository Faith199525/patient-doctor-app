<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutritionistServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutritionist_services', function (Blueprint $table) {
            $table->id();
            $table->longText('initial_complain');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('nutritionist_id');
            $table->text('comment')->nullable();
            $table->string('status')->default('PENDING'); //'PENDING','ACTIVE','COMPLETED'
            $table->timestamps();

            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('nutritionist_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nutritionist_services');
    }
}
