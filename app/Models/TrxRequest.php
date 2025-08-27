<?php

namespace App\Models;

use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequest extends Model
{
    use HasUuidPrimaryKey;
    
    protected $table = 'trx_request';

    protected $primaryKey = 'req_id';

    protected $guarded = [];

    public function approvals()
    {
        return $this->hasMany(TrxRequestApproval::class, 'req_id', 'req_id');
    }

    public function documents()
    {
        return $this->hasMany(TrxRequestDocument::class, 'req_id', 'req_id');
    }
}
