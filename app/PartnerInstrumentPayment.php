<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerInstrumentPayment extends Model
{
    use HasFactory;
    protected $table = 'tbl_partner_instrument_payments';
    protected $guarded = [];
}
