<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleNoteEntry extends Model
{
    use HasFactory;
    protected $table = 'tbl_sale_note_entries';
    protected $guarded = [];
}
