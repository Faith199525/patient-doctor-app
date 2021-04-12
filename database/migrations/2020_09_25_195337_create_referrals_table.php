<?php

use App\Utils\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_file_id');
            $table->unsignedBigInteger('partners_id')->nullable();
            $table->enum('status', ['ACTIVE', 'ACCEPTED', 'COMPLETED'])->default('ACTIVE');
            $table->enum('type', ['HOSPITAL', 'DIAGNOSTIC', 'PAEDIATRICIAN', 'GYNAECOLOGIST', 'OPTICIAN', 'OPHTHALMOLOGIST', 'ENT', 'CARDIOLOGIST', 'INTERNAL MEDICINE PHYSICIAN', 'ORTHOPAEDIC SURGEON', 'PUBLIC HEALTH PHYSICIAN', 'GENERAL SURGEON', 'UROLOGIST', 'GASTROENTEROLOGIST', 'DERMATOLOGIST', 'DENTIST', 'ORAL SURGEON' ]);
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('doctor_note')->nullable();
            $table->text('partners_note')->nullable();
            $table->text('price_in_minor_unit')->nullable();
            $table->text('proposed_date')->nullable();
            $table->timestamps();

            $table->foreign('case_file_id')->references('id')->on('case_files')->onDelete('cascade');
            $table->foreign('partners_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}
