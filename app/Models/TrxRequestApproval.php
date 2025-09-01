<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequestApproval extends Model
{
    use HasUuidPrimaryKey, Auditable;
    
    protected $table = 'trx_request_approval';

    protected $primaryKey = 'req_app_id';

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }
}
