<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxServiceRate extends Model
{
    use HasUuidPrimaryKey, Auditable;
    
    protected $table = 'trx_service_rate';

    protected $primaryKey = 'svc_rate_id';

    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(TrxRequest::class, 'req_id', 'req_id');
    }
}
