<?php

namespace App\Models;

use App\Models\Masters\DoctypeRequirement;
use App\Models\Masters\Position;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class MxEmpPos extends Model
{
    use HasUuidPrimaryKey;
    
    protected $table = 'mx_employee_position';

    protected $primaryKey = 'mx_emp_pos_id';

    protected $guarded = [];
    
    public function employee()
    {
        return $this->belongsTo(UserProfile::class, 'nip', 'nip');
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }
}
