<?php

use App\Utils\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_file_id');
            $table->text('ailment');
            $table->unsignedBigInteger('partners_id')->nullable();
            $table->longText('pharmacy_note')->nullable();
            $table->unsignedBigInteger('shipping_rate_in_minor_unit')->nullable();
            $table->enum('status', ['ACTIVE', 'ACCEPTED', 'APPROVED', 'DECLINED']);
            $table->enum('delivery_type', ['HOME', 'PICK-UP']);
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
        Schema::dropIfExists('prescriptions');
    }
}
