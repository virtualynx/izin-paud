<?php

namespace App\Models;

use App\Models\Masters\Position;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profile';

    protected $primaryKey = 'user_id';
    public $incrementing = false; // This tells Laravel the ID isn't auto-incrementing
    // protected $keyType = 'string'; // The primary key is a string
    
    protected $guarded = [];

    protected $casts = [];
    
    public function positions()
    {
        return $this->belongsToMany(
            Position::class, // Related model
            'mx_employee_position',  // Pivot table name
            'nip',   // Foreign key on pivot table
            'position_id',   // Related key on pivot table
            'nip',   // Local key
            'position_id'    // Related key
        );
    }
}
