<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'position';

    protected $primaryKey = 'position_id';
    public $incrementing = false; // This tells Laravel the ID isn't auto-incrementing
    protected $keyType = 'string'; // The primary key is a string
    
    protected $guarded = [];
}
