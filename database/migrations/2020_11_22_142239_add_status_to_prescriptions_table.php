<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table = 'prescriptions';

    public function up()
    {
            DB::statement("ALTER TABLE ".$this->set_schema_table." MODIFY COLUMN status ENUM('ACTIVE', 'ACCEPTED', 'APPROVED', 'DECLINED', 'PENDING', 'REJECTED') NOT NULL DEFAULT 'ACTIVE'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
             DB::statement("ALTER TABLE ".$this->set_schema_table." MODIFY COLUMN status ENUM('ACTIVE', 'ACCEPTED', 'APPROVED', 'DECLINED') ");
    }
}
