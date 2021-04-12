<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagnosisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnosis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_file_id');
            $table->unsignedBigInteger('partners_id')->nullable();
            $table->unsignedBigInteger('shipping_rate_in_minor_unit')->nullable();
            $table->enum('status', ['ACTIVE','PENDING','COMPLETED']);
            $table->timestamps();

            $table->foreign('case_file_id')->references('id')->on('case_files');
            $table->foreign('partners_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diagnosis');
    }
}
