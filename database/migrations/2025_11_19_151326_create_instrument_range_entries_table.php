<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_instrument_range_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('instrument_id');
            $table->integer('ins_unit_id');
            $table->string('ins_range',50);
            $table->string('ins_accuracy',25);
            $table->string('ins_make',25);
            $table->enum('del_status', ['Live', 'Deleted'])->default('Live');
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
        Schema::dropIfExists('tbl_instrument_range_entries');
    }
};
