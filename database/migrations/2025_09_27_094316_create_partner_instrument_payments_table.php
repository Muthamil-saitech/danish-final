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
        Schema::create('tbl_partner_instrument_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('partner_io_detail_id');
            $table->float('amount',10,2);
            $table->float('paid_amount',10,2);
            $table->float('due_amount',10,2);
            $table->date('payment_date');
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
        Schema::dropIfExists('tbl_partner_instrument_payments');
    }
};
