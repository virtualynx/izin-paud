<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Masters\Position;
use App\Models\TrxRequest;
use App\Models\TrxRequestApproval;
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
    public const STATUS_TEXT_REVISION = 'Revisi';
    public const STATUS_TEXT_VERIFICATION_WAITING = 'Belum Diverifikasi';
    public const STATUS_TEXT_VERIFICATION = 'Sedang Diverifikasi';
    public const STATUS_TEXT_PUBLISHING = 'Menunggu penerbitan Izin';
    public const STATUS_TEXT_PUBLISHED = 'Izin terbit';

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function listRequestForUser()
    {
        $userinfo = userinfo();

        $query = $this->_listRequestSelectQuery();

        $results = $query
            ->where('is_disabled', 0)
            ->where('created_by', $userinfo->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $results;
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

        $query = $this->_listRequestSelectQuery();
        
        $query = $query
            ->where('is_disabled', 0)
            ->whereIn('status', [TrxRequest::STATUS_SUBMITTED, TrxRequest::STATUS_VERIFIED]);

        $results = $query
            ->where('is_disabled', 0)
            ->whereIn('status', [TrxRequest::STATUS_SUBMITTED, TrxRequest::STATUS_VERIFIED])
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

    public function resetApproval($req_id){
        DB::beginTransaction();
        try{
            $approval_rs = TrxRequestApproval::query()
                ->where('is_disabled', 0)
                ->where('req_id', $req_id)
                ->get()
                ;

            foreach($approval_rs as $row){
                $row->approval_status = null;
                $row->save();
            }

            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function getRequestApprovalMap(array $req_ids){
        $approval_rs = TrxRequestApproval::query()
            ->where('is_disabled', 0)
            ->whereIn('req_id', $req_ids)
            ->orderBy('level', 'desc')
            ->get()
            ;

        $req_approval_map = [];
        foreach($approval_rs as $row){
            if(empty($req_approval_map[$row->req_id])){
                $req_approval_map[$row->req_id] = [];
            }

            $req_approval_map[$row->req_id] []= $row;
        }

        $req_currentapproval_map = [];
        foreach($req_approval_map as $req_id => $approvals){
            // if(!empty($req_currentapproval_map[$req_id])){
            //     continue;
            // }

            $req_currentapproval_map[$req_id] = null;

            foreach($approvals as $appr){
                if(empty($appr->approval_status)){
                    $req_currentapproval_map[$req_id] = $appr;
                    break;
                }
            }
        }

        return $req_currentapproval_map;
    }

    public function updateRequestApproval($req_id, $user_id, $status){
        $mainPosition = $this->userService->getMainPosition($user_id);
        $req_approval_map = $this->getRequestApprovalMap([$req_id]);
        $approval = $req_approval_map[$req_id];

        if(empty($approval)){
            throw new \Exception("Request with id: $req_id is already fully-approved", 1);
        }

        if($approval->approver_position_id != $mainPosition->position_id){
            throw new \Exception("The main-position of the user does not have privilege to sign current approval", 1);
        }

        $approval->approval_status = $status;
        $approval->save();
    }

    private function _listRequestSelectQuery(){
        $query = TrxRequest::query()
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
                        '".self::STATUS_TEXT_REVISION."'
                    
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
                        '".self::STATUS_TEXT_VERIFICATION."'
                    
                    -- If status is 'submitted'
                    WHEN trx_request.status = 'submitted' THEN 
                        '".self::STATUS_TEXT_VERIFICATION_WAITING."'

                    -- If status is 'verified' and any of approval_status is null
                    WHEN 
                        trx_request.status = 'verified' 
                        AND EXISTS (
                            SELECT 1 FROM trx_request_approval 
                            WHERE 
                                trx_request_approval.req_id = trx_request.req_id 
                                AND trx_request_approval.approval_status IS NULL 
                        ) 
                    THEN 
                        CONCAT(
                            'Menunggu Disetujui ', 
                            (
                                SELECT p.name 
                                FROM 
                                    trx_request_approval tra
                                    JOIN position p ON tra.approver_position_id = p.position_id
                                WHERE 
                                    tra.req_id = trx_request.req_id
                                    AND tra.approval_status IS NULL
                                ORDER BY tra.level DESC 
                                LIMIT 1
                            )
                        )
                    
                    -- If status is 'verified', all approval_status are 'approved', and has decree
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
                        AND EXISTS (
                            SELECT 1 FROM trx_permit_decree 
                            WHERE 
                                trx_permit_decree.req_id = trx_request.req_id 
                                and trx_permit_decree.is_disabled = 0
                        )
                    THEN 
                        '".self::STATUS_TEXT_PUBLISHED."'
                    
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
                        '".self::STATUS_TEXT_PUBLISHING."'
                    
                    -- Default case for other scenarios
                    ELSE 'Status Error'
                END as approval_status
            "))
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
            "))
            ->with('decree');
        
        return $query;
    }
}