<?php

namespace App\Models;

use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxPermitDecree extends Model
{
    use HasUuidPrimaryKey;
    
    protected $table = 'trx_permit_decree';

    protected $primaryKey = 'permit_decree_id';

    protected $guarded = [];

    public const TYPE_NEW = 'NEW';
    public const TYPE_EXTENSION = 'EXTENSION';
    public const TYPE_REVISION = 'REVISION';

    public function request()
    {
        return $this->belongsTo(TrxRequest::class, 'req_id', 'req_id');
    }
}
