<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class DoctypeRequirement extends Model
{
    protected $table = 'doctype_requirement';

    protected $primaryKey = 'doctypereq_id';
    public $incrementing = false; // This tells Laravel the ID isn't auto-incrementing
    protected $keyType = 'string'; // The primary key is a string
    
    protected $guarded = [];
}
