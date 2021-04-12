<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestedPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requested_partners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('address')->nullable();
            $table->string('registered_name')->nullable();
            $table->string('license_number');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank');
            $table->enum('account_type', ['SAVINGS', 'CURRENT']);
            $table->mediumText('description')->nullable();
            $table->string('year_of_graduation')->nullable();
            $table->string('school_attended')->nullable();
            $table->enum('type', ['AMBULANCE','NURSE','NUTRITIONIST']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('bank_id')->references('id')->on('bank_lists')->onDelete('cascade');
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
        Schema::dropIfExists('requested_partners');
    }
}
