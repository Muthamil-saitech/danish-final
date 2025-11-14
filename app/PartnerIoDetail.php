<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerIoDetail extends Model
{
    protected $table = "tbl_partner_io_details";
    protected $guarded = [];
    use HasFactory;
    public function paymentInvoice()
    {
        return $this->hasOne(PartnerInstrumentPayment::class, 'partner_io_detail_id', 'id');
    }
}
