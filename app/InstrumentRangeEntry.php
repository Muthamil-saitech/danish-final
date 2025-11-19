<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentRangeEntry extends Model
{
    use HasFactory;
    protected $table = 'tbl_instrument_range_entries';
    protected $guarded = [];
}
