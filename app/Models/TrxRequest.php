<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRequest extends Model
{
    use HasUuidPrimaryKey, Auditable;
    
    protected $table = 'trx_request';

    protected $primaryKey = 'req_id';

    protected $guarded = [];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VERIFIED = 'verified';

    public function approvals()
    {
        return $this->hasMany(TrxRequestApproval::class, 'req_id', 'req_id');
    }

    public function documents()
    {
        return $this->hasMany(TrxRequestDocument::class, 'req_id', 'req_id')->orderBy('doctypereq_id', 'asc');;
    }
    
    public function decree()
    {
        // return $this->belongsTo(TrxPermitDecree::class, 'req_id', 'req_id');
        return $this->hasOne(TrxPermitDecree::class, 'req_id', 'req_id')
            ->where('is_disabled', 0)
            ->latest('created_at');
    }
    
    public function ext_of_decree()
    {
        return $this->belongsTo(TrxPermitDecree::class, 'req_id', 'req_id');
    }

    public function revision_notes()
    {
        return $this->hasMany(TrxRevisionNotes::class, 'req_id', 'req_id')
            ->where('is_disabled', 0)
            ->orderBy('created_at', 'desc');
    }

    public function revision_note()
    {
        return $this->hasOne(TrxRevisionNotes::class, 'req_id', 'req_id')
            ->where('is_disabled', 0)
            ->whereNull('req_doc_id')
            ->where('is_resolved', 0);
    }

    public function latest_revision_note()
    {
        return $this->hasOne(TrxRevisionNotes::class, 'req_id', 'req_id')
            ->where('is_disabled', 0)
            ->whereNull('req_doc_id')
            ->latest('created_at');
    }
}
