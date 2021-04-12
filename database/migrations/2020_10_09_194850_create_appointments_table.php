<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');//the partner creating the appointment
            $table->unsignedBigInteger('referral_id');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['ACTIVE', 'ACCEPTED', 'COMPLETED', 'DECLINED'])->default('ACTIVE');
            $table->enum('type', ['HOSPITAL', 'DIAGNOSTIC']);
            //$table->boolean('accepted')->default(false);
            $table->timestamps();

            $table->foreign('referral_id')->references('id')->on('referrals')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
