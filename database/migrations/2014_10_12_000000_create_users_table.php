<?php

use App\Utils\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile_phone_number')->nullable();
            
            $this->status($table);
            $table->boolean('verified')->default(false);
            $table->enum('gender', [
                'MALE',
                'FEMALE'
            ])->nullable();
            //$table->string('profile_picture')->nullable();

            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('kin_first_name')->nullable();;
            $table->string('kin_last_name')->nullable();;
            $table->string('kin_country')->nullable();
            $table->string('kin_state')->nullable();
            $table->string('kin_city')->nullable();
            $table->string('kin_address')->nullable();
            $table->string('kin_phone_number')->nullable();   
            $table->timestamp('email_verified_at')->nullable();         
            $table->string('dob')->nullable();
            $table->string('mothers_maiden_name')->nullable();
            $table->string('work_phone_number')->nullable();
            
            $table->text('email_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
