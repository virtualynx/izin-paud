<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Masters\Position;
use App\Models\TrxRequest;
use App\Models\TrxRequestDocument;
use App\Models\UserProfile;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PermitService
{
    public function __construct()
    {
        
    }
    
    public function listUnverifiedRequest()
    {
        // $results = TrxRequest::query()
        //     ->where('is_disabled', 0)
        //     ->where('status', TrxRequest::STATUS_SUBMITTED)
        //     ->whereHas('documents', function($q) {
        //         $q->where('verify_status', TrxRequestDocument::STATUS_PENDING)
        //         ->orWhere('verify_status', TrxRequestDocument::STATUS_REVISION);
        //     })
        //     ->orderBy('created_at', 'asc')
        //     ->get();

        $results = TrxRequest::query()
            ->where('is_disabled', 0)
            ->where('status', TrxRequest::STATUS_SUBMITTED)
            ->orderBy('created_at', 'asc')
            ->get();

        return $results;
    }
    
    public function listVerifiedRequest()
    {
        $results = TrxRequest::query()
            ->where('is_disabled', 0)
            ->where('status', TrxRequest::STATUS_VERIFIED)
            ->whereHas('documents') // Ensure at least one document exists
            ->whereDoesntHave('documents', function($q) {
                $q->where('verify_status', '!=', TrxRequestDocument::STATUS_VERIFIED);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $results;
    }
    
    public function listApprovalForUser($user_id)
    {
        $results = TrxRequest::query()
            ->where('is_disabled', 0)
            ->whereHas('approvals', function($q) use($user_id) {
                $q->query()
                    ->where('approver_user_id', $user_id)
                    ->WhereNull('approval_status')
                    ;
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $results;
    }
    
    public function listApprovalDone()
    {
        $results = TrxRequest::query()
            ->where('is_disabled', 0)
            ->whereHas('approvals') // Ensure at least one document exists
            ->whereDoesntHave('approvals', function($q) {
                $q->where('approval_status', '!=', 'approved')
                ->orWhereNull('approval_status');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $results;
    }
}