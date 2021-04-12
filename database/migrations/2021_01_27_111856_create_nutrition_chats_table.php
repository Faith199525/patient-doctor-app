<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutritionChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrition_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender');
            $table->string('body');
            $table->unsignedBigInteger('nutritionist_service_id');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('sender')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('nutritionist_service_id')->references('id')->on('nutritionist_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nutrition_chats');
    }
}
