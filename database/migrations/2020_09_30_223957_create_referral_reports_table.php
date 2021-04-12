<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id');
            $table->string('title');
            $table->longText('body');
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('referral_id')->references('id')->on('referrals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_reports');
    }
}
