<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileUpdateColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->string('account_name')->after('school_attended')->nullable();
            $table->string('account_number')->after('school_attended')->nullable();
            $table->string('bank_name')->after('school_attended')->nullable();
            // $table->string('account_type')->after('school_attended')->nullable();
            $table->string('working_days')->after('school_attended')->nullable();
            $table->string('start_time')->after('school_attended')->nullable();
            $table->string('closing_time')->after('school_attended')->nullable();
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->string('account_name')->after('description')->nullable();
            $table->string('account_number')->after('description')->nullable();
            $table->string('bank_name')->after('description')->nullable();
            // $table->string('account_type')->after('description')->nullable();
            $table->string('working_days')->after('description')->nullable();
            $table->string('start_time')->after('description')->nullable();
            $table->string('closing_time')->after('description')->nullable();
            $table->string('representative_one_name')->after('description')->nullable();
            $table->string('representative_one_email')->after('description')->nullable();
            $table->string('representative_one_phone_number')->after('description')->nullable();
            $table->string('representative_two_name')->after('description')->nullable();
            $table->string('representative_two_email')->after('description')->nullable();
            $table->string('representative_two_phone_number')->after('description')->nullable();
        });

        Schema::create('partner_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partners_id');
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->foreign('partners_id')->references('id')->on('partners')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('requested_partners', function (Blueprint $table) {
            $table->string('years_of_experience')->after('school_attended')->nullable();
            $table->dropColumn('account_type');
            $table->dropColumn('license_number');
            $table->dropColumn('address');
        });

        Schema::create('requested_partners_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requested_partner_id');
            $table->string('description')->nullable();
            $table->string('file_name');
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
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn('account_name');
            $table->dropColumn('account_number');
            $table->dropColumn('bank_name');
            // $table->dropColumn('account_type');
            $table->dropColumn('working_days');
            $table->dropColumn('start_time');
            $table->dropColumn('closing_time');
        });

        Schema::dropIfExists('speciaty_branches');

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('account_name');
            $table->dropColumn('account_number');
            $table->dropColumn('bank_name');
            // $table->dropColumn('account_type');
            $table->dropColumn('working_days');
            $table->dropColumn('start_time');
            $table->dropColumn('closing_time');
            $table->dropColumn('representative_one_name');
            $table->dropColumn('representative_one_email');
            $table->dropColumn('representative_one_phone_number');
            $table->dropColumn('representative_two_name');
            $table->dropColumn('representative_two_email');
            $table->dropColumn('representative_two_phone_number');
        });

        Schema::dropIfExists('partner_branches');

        Schema::table('requested_partners', function (Blueprint $table) {
            $table->dropColumn('years_of_experience');
        });

        chema::dropIfExists('partner_certificates');
    }
}
