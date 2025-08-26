<?php

namespace App\Models;

use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequestApproval extends Model
{
    use HasUuidPrimaryKey;
    
    protected $table = 'trx_request_approval';

    protected $primaryKey = 'req_app_id';

    protected $guarded = [];
}
