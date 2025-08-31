<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Masters\Position;
use App\Models\TrxRequest;
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
        $results = TrxRequest::query()
            ->where('is_disabled', 0)
            ->whereHas('documents', function($q) {
                $q->whereNull('verify_status');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $results;
    }
    
    public function listVerifiedRequest()
    {
        $results = TrxRequest::query()
            ->where('is_disabled', 0)
            ->whereHas('documents') // Ensure at least one document exists
            ->whereDoesntHave('documents', function($q) {
                $q->where('verify_status', '!=', 'verified')
                ->orWhereNull('verify_status');
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