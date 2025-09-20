<?php

namespace App\Models;

use App\Models\Masters\DoctypeRequirement;
use App\Traits\Auditable;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequestDocument extends Model
{
    use HasUuidPrimaryKey, Auditable;
    
    protected $table = 'trx_request_document';

    protected $primaryKey = 'req_doc_id';

    protected $guarded = [];
    
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REVISION = 'revision';

    public function doctype()
    {
        return $this->belongsTo(DoctypeRequirement::class, 'doctypereq_id', 'doctypereq_id');
    }

    public function revision_note()
    {
        return $this->hasOne(TrxRevisionNotes::class, 'req_doc_id', 'req_doc_id')
            ->where('is_disabled', 0)
            ->where('is_resolved', 0);
    }

    public function latest_revision_note()
    {
        return $this->hasOne(TrxRevisionNotes::class, 'req_doc_id', 'req_doc_id')
            ->where('is_disabled', 0)
            ->latest('created_at');
    }
}
