<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReturnEntry extends Model
{
    use HasFactory;
    protected $table = 'tbl_stock_return_entries';
    protected $guarded = [];
}
