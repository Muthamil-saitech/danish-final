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
        Schema::create('tbl_sale_note_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type',10);
            $table->integer('sale_id');
            $table->integer('product_id');
            $table->string('invoice_no',50);
            $table->integer('qty');
            $table->float('price',10,2);
            $table->date('invoice_date');
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
        Schema::dropIfExists('tbl_sale_note_entries');
    }
};
