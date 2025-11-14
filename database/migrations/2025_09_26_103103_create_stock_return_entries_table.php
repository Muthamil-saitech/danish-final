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
        Schema::create('tbl_stock_return_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_id');
            $table->integer('mat_type');
            $table->integer('mat_cat_id');
            $table->integer('mat_id');
            $table->string('reference_no',50);
            $table->integer('line_item_no');
            $table->integer('float_stock');
            $table->integer('qty');
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
        Schema::dropIfExists('tbl_stock_return_entries');
    }
};
