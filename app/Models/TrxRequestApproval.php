<?php

namespace App\Models;

use App\Models\Masters\Position;
use App\Traits\Auditable;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequestApproval extends Model
{
    use HasUuidPrimaryKey, Auditable;
    
    protected $table = 'trx_request_approval';

    protected $primaryKey = 'req_app_id';

    protected $guarded = [];
    
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REVISION = 'revision';
    public const STATUS_REJECTED = 'rejected';

    public function employee()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'approver_position_id', 'position_id');
    }
}
