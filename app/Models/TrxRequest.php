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
}
