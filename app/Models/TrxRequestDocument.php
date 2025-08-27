<?php

namespace App\Models;

use App\Models\Masters\DoctypeRequirement;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequestDocument extends Model
{
    use HasUuidPrimaryKey;
    
    protected $table = 'trx_request_document';

    protected $primaryKey = 'req_doc_id';

    protected $guarded = [];

    public function doctype()
    {
        return $this->belongsTo(DoctypeRequirement::class, 'doctypereq_id', 'doctypereq_id');
    }
}
