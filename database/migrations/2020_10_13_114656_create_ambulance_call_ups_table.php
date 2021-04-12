<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbulanceCallUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ambulance_call_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');//the patient requesting for an ambulance service
            $table->unsignedBigInteger('ambulance_id')->nullable();//the ambulance that accepted the request
            $table->string('pick_up_location');
            $table->string('pick_up_address');
            $table->string('hospital');
            $table->string('hospital_location');
            $table->enum('status', ['PENDING', 'ACCEPTED', 'COMPLETED', 'DECLINED'])->default('PENDING');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ambulance_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('ambulance_call_ups');
    }
}
