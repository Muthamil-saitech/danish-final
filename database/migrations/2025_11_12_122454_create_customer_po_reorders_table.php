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
        Schema::create('tbl_customer_po_reorders', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_order_id');
            $table->integer('customer_order_detail_id');
            $table->integer('product_id');
            $table->integer('mat_id');
            $table->float('mat_qty');
            $table->integer('prod_qty');
            $table->float('unit_price', 10, 2);
            $table->float('price', 10, 2);
            $table->integer('tax_type');
            $table->string('inter_state',1);
            $table->string('cgst',3);
            $table->string('sgst',3);
            $table->string('igst',3);
            $table->float('tax_amount', 10, 2);
            $table->float('subtotal', 10, 2);
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
        Schema::dropIfExists('tbl_customer_po_reorders');
    }
};
