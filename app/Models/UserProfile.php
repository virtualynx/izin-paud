<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profile';

    protected $primaryKey = 'user_id';
    public $incrementing = false; // This tells Laravel the ID isn't auto-incrementing
    // protected $keyType = 'string'; // The primary key is a string
    
    protected $guarded = [];

    protected $casts = [];
}
