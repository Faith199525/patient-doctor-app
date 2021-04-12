<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookNursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_nurses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->longText('comment')->nullable();
            $table->date('date');
            $table->string('days')->nullable();
            $table->enum('status', ['ACTIVE', 'CONFIRMED', 'COMPLETED'])->default('ACTIVE');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users');
            $table->foreign('partner_id')->references('id')->on('users');
        });

        Schema::table('ambulance_call_ups', function (Blueprint $table) {
            $table->dropColumn('pick_up_location');
            $table->dropColumn('hospital');
            $table->dropColumn('hospital_location');
            $table->string('phone_number')->after('pick_up_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_nurses');

        Schema::table('ambulance_call_ups', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
}
