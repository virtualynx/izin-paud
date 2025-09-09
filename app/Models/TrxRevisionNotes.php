<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class TrxRevisionNotes extends Model
{
    use HasUuidPrimaryKey, Auditable;
    
    protected $table = 'trx_revision_notes';

    protected $primaryKey = 'rev_note_id';

    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(TrxRequest::class, 'req_id', 'req_id');
    }

    public function request_document()
    {
        return $this->belongsTo(TrxRequestDocument::class, 'req_doc_id', 'req_doc_id')->with('doctype');
    }
}
