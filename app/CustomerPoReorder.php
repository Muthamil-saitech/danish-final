<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPoReorder extends Model
{
    use HasFactory;
    protected $table = 'tbl_customer_po_reorders';
    protected $guarded = [];
}
