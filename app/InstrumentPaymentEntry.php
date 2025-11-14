<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentPaymentEntry extends Model
{
    use HasFactory;
    protected $table = 'tbl_instrument_payment_entries';
    protected $guarded = [];
}
