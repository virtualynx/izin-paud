<?php

namespace App\Models;

use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class MxEmpPos extends Model
{
    use HasUuidPrimaryKey;
    
    protected $table = 'mx_employee_position';

    protected $primaryKey = 'mx_emp_pos_id';

    protected $guarded = [];
}
