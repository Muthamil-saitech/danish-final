<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerIoDetail extends Model
{
    use HasFactory;
    protected $table = "tbl_customer_io_details";
    protected $guarded = [];
    public function instrument()
    {
        return $this->belongsTo(Instrument::class, 'ins_name');
    }
}
