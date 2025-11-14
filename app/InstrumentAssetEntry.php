<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentAssetEntry extends Model
{
    use HasFactory;
    protected $table = 'tbl_instrument_asset_entries';
    protected $guarded = [];
}
