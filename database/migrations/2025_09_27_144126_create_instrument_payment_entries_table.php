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
        Schema::create('tbl_instrument_payment_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('io_detail_id');
            $table->string('reference_no',50);
            $table->date('io_date');
            $table->integer('partner_id');
            $table->float('total_amount',10,2);
            $table->float('pay_amount',10,2);
            $table->float('balance_amount',10,2);
            $table->string('payment_type',10);
            $table->string('note',100);
            $table->string('payment_proof',150);
            $table->integer('user_id');
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
        Schema::dropIfExists('tbl_instrument_payment_entries');
    }
};
