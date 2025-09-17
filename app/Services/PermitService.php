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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PermitService
{
    public function __construct()
    {
        
    }
    
    public function listRequestForOfficer()
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
            ->select("*")
            ->addSelect(DB::raw("
                CASE 
                    -- status revisi
                    WHEN 
                        trx_request.status = 'submitted' 
                        AND EXISTS (
                            SELECT 1 
                            FROM trx_request_document 
                            WHERE 
                                trx_request_document.req_id = trx_request.req_id 
                                AND trx_request_document.verify_status = '".TrxRequestDocument::STATUS_REVISION."'
                        )
                    THEN 
                        'Revisi'
                    
                    -- status process
                    WHEN 
                        trx_request.status = 'submitted' 
                        AND EXISTS (
                            SELECT 1 
                            FROM trx_request_document 
                            WHERE 
                                trx_request_document.req_id = trx_request.req_id 
                                AND trx_request_document.verify_status <> '".TrxRequestDocument::STATUS_PENDING."'
                        )
                    THEN 
                        'Verifikasi'
                    
                    -- If status is 'submitted'
                    WHEN trx_request.status = 'submitted' THEN 
                        'Menunggu Verifikasi'

                    -- If status is 'verified' and no approval_status is filled
                    WHEN 
                        trx_request.status = 'verified' 
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM trx_request_approval 
                            WHERE 
                                trx_request_approval.req_id = trx_request.req_id 
                                AND trx_request_approval.approval_status IS NOT NULL
                        )
                    THEN 
                        CONCAT(
                            'Menunggu Disetujui ', 
                            (
                                SELECT p.name 
                                FROM 
                                    trx_request_approval tra
                                    JOIN position p ON tra.approver_position_id = p.position_id
                                WHERE tra.req_id = trx_request.req_id
                                ORDER BY tra.level DESC 
                                LIMIT 1
                            )
                        )
                    
                    -- If status is 'verified' and all approval_status are 'approved'
                    WHEN 
                        trx_request.status = 'verified' 
                        AND NOT EXISTS (
                            SELECT 1 FROM trx_request_approval 
                            WHERE trx_request_approval.req_id = trx_request.req_id 
                            AND (
                                trx_request_approval.approval_status IS NULL 
                                OR trx_request_approval.approval_status != 'approved'
                            )
                        ) 
                    THEN 
                        'Menunggu penerbitan Izin'
                    
                    -- Default case for other scenarios
                    ELSE 'Dalam Proses'
                END as approval_status
            "
            ))
            ->addSelect(DB::raw("
                CASE 
                    -- For verified status with no approvals, use current timestamp or NULL
                    WHEN trx_request.status = 'verified' AND NOT EXISTS (
                        SELECT 1 FROM trx_request_approval 
                        WHERE trx_request_approval.req_id = trx_request.req_id 
                        AND trx_request_approval.approval_status IS NOT NULL
                    ) THEN 
                        NULL
                    
                    -- For other cases, use the latest approval timestamp
                    ELSE (
                        SELECT MAX(tra.updated_at) 
                        FROM trx_request_approval tra
                        WHERE tra.req_id = trx_request.req_id
                        AND tra.approval_status IS NOT NULL
                    )
                END as approval_time
            "
            ))
            ->where('is_disabled', 0)
            ->whereIn('status', [TrxRequest::STATUS_SUBMITTED, TrxRequest::STATUS_VERIFIED])
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

    public function fillPermitStatuses($requestList){
        $req_ids = [];
        foreach($requestList as $row){
            $req_ids []= $row->req_id;
        }


    }
}