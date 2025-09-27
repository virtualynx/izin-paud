<?php

namespace App\Http\Api;

use App\Dto\ApiResponse;
use App\Models\Masters\DoctypeRequirement;
use App\Models\TrxPermitDecree;
use App\Models\TrxRequest;
use App\Models\TrxRequestApproval;
use App\Models\TrxRequestDocument;
use App\Models\TrxRevisionNotes;
use App\Services\PermitService;
use App\Services\PositionService;
use App\Services\UserService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PermitApi extends Controller
{
    public const REQUEST_UPDATE_MODE_SUBMIT = 'submit';
    public const REQUEST_UPDATE_MODE_APPROVE = 'approve';
    public const REQUEST_UPDATE_MODE_VERIFY = 'verify';

    public function __construct(){
    }

    public function docrec_list(){
        $params = request()->all();

        $results = DoctypeRequirement::where('is_disabled', 0)->get();

        return response()->json(new ApiResponse($results));
    }

    public function request_submit(PositionService $positionService){
        $params = request()->all();

        $validator_rules = [
            'name' => 'required|string|max:255',
            'foundation_type' => 'required|string',
            'phone' => 'required|string|max:15',
            'email' => 'required|email',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'required|string',
            'pic_name' => 'required|string|max:255',
            'founded_year' => 'required|integer|min:1900|max:'.date('Y'),
            'vision_mission' => 'required|string',
            // 'doc_ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 'doc_akta' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 'doc_kuasa' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // Add validation for other documents
        ];

        $doctypes = DoctypeRequirement::where('is_disabled', 0)->get();
        $maxSizeInBytes = 10 * 1024;
        foreach($doctypes as $row){
            if($row->is_optional == 1 && empty($params[$row->doctypereq_id])){
                continue;
            }

            $basicRules = "file|mimes:pdf,jpg,jpeg,png|max:$maxSizeInBytes";
            if($row->is_multiple_file ==1){
                // 'doc_ktp' => 'required|array|min:1', // Multiple files, at least 1 required
                // 'doc_ktp.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                $validator_rules[$row->doctypereq_id] = 'array'.($row->is_optional==0? '|required|min:1': '');
                $validator_rules[$row->doctypereq_id.'.*'] = $basicRules.($row->is_optional==0? '|required': '');
            }else{
                $validator_rules[$row->doctypereq_id] = $basicRules.($row->is_optional==0? '|required': '');
            }
        }

        // Validate the request
        $validator = Validator::make($params, $validator_rules);
        $is_fails = $validator->fails();
        $errors = $validator->errors();

        if ($validator->fails()) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Validasi gagal',
            //     'errors' => $validator->errors()
            // ], 422);

            return response()->json(
                new ApiResponse($validator->errors(), 422, 'validation fail')
            );
        }

        $response = new ApiResponse();
        DB::beginTransaction();
        try {
            // Generate registration number
            $reg_num = 'PAUD-' . date('Ymd') . '-' . strtoupper(uniqid());

            $params['reg_num'] = $reg_num;
            $params['status'] = TrxRequest::STATUS_SUBMITTED;

            //unset all files from params
            $uploadedFiles = request()->allFiles();
            foreach($uploadedFiles as $key => $file){
                unset($params[$key]);
            }

            $new_request = new TrxRequest($params);
            $new_request->save();

            // Store uploaded files
            $savedFolder = "request_documents/$new_request->req_id";
            $savedFiles = [];
            foreach($uploadedFiles as $key => $files){
                $array_of_file = [];
                if(!is_array($files)){
                    $array_of_file []= $files;
                }else{
                    $array_of_file = $files;
                }

                foreach($array_of_file as $file){
                    $savedPath = $file->storeAs($savedFolder, $file->getClientOriginalName());
                    $savedFiles[$key] = $savedPath;

                    $new_request->documents()->create([
                        'req_id' => $new_request->req_id,
                        'doctypereq_id' => $key,
                        'file_path' => $savedPath,
                    ]);
                }
            }

            //generate approval sequence
            $approvalSequence = $positionService->getPositionSequence(true); 
            foreach($approvalSequence as $sequence){
                $new_request->approvals()->create([
                    'req_id' => $new_request->req_id,
                    'level' => $sequence['level'],
                    'approver_user_id' => $sequence['supervisor_user_id'],
                    'approver_position_id' => $sequence['supervisor_position_id'],
                ]);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = $e->getCode();
            $response->message = $e->getMessage();
        }

        return response()->json($response);
    }

    public function list(){
        $params = request()->all();

        $query = TrxRequest::query()
            ->where('is_disabled', 0);

        if(!empty($params['own_request']) && filter_var($params['own_request'], FILTER_VALIDATE_BOOLEAN)){
            $userinfo = userinfo();
            $a = 1;
        }

        $order_by = 'asc';
        if(!empty($params['order'])){
            $order_by = $params['order'];
        }

        $query = $query->orderBy('created_at', $order_by);

        $results = $query->get();

        return response()->json(new ApiResponse($results));
    }
    
    public function dt_request_list(PermitService $permitService){
        $params = request()->all();

        $rs = $permitService->listRequestForUser();
        // $rs = TrxRequest::query()
        //     ->where('is_disabled', 0)
        //     ->where('status', TrxRequest::STATUS_SUBMITTED)
        //     ->orderBy('created_at', 'asc')
        //     ->get();

        $totalRecords = $filteredRecords = count($rs);

        $data = [];
        foreach ($rs as $row) {
            $decree_fileinfo = null;
            if(!empty($row->decree)){
                $decree_fileinfo = [
                    'permit_decree_id' => $row->decree->permit_decree_id
                ];

                $path_arr = explode("/", $row->decree->file_path);
                $filename = $path_arr[count($path_arr)-1];
                $decree_fileinfo['filename'] = $filename;

                $decree_fileinfo['mime'] = Storage::disk()->mimeType($row->decree->file_path);
            }

            $data[] = [
                'req_id' => $row->req_id,
                'reg_num' => $row->reg_num,
                'name' => $row->name,
                'request_date' => $row->created_at->format('d-m-Y H:i'),
                'status' => $row->status,
                'status_text' => $row->approval_status,
                'decree' => $decree_fileinfo,
                // 'status_text' => $row->approval_status.(!empty($row->approval_time)? " (".$row->approval_time.")": ''),
                'actions' => null, // Will be filled by JS
            ];
        }

        return response()->json([
            'draw' => $params['draw'],
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
    
    public function dt_request_officer_list(PermitService $permitService, UserService $userService){
        $params = request()->all();

        $rs = $permitService->listRequestForOfficer();
        // $rs = TrxRequest::query()
        //     ->where('is_disabled', 0)
        //     ->where('status', TrxRequest::STATUS_SUBMITTED)
        //     ->orderBy('created_at', 'asc')
        //     ->get();

        $req_ids = [];
        foreach ($rs as $row) {
            $req_ids []= $row->req_id;
        }

        $user = userinfo();
        $mainPosition = $userService->getMainPosition($user->user_id);
        $req_approval_map = $permitService->getRequestApprovalMap($req_ids);

        $data = [];
        foreach ($rs as $row) {
            $approval = $req_approval_map[$row->req_id];

            $isMyAppoval = !empty($approval) && $approval->approver_position_id == $mainPosition->position_id? true: false;
            
            $decree_fileinfo = null;
            if(!empty($row->decree)){
                $decree_fileinfo = [
                    'permit_decree_id' => $row->decree->permit_decree_id
                ];

                $path_arr = explode("/", $row->decree->file_path);
                $filename = $path_arr[count($path_arr)-1];
                $decree_fileinfo['filename'] = $filename;

                $decree_fileinfo['mime'] = Storage::disk()->mimeType($row->decree->file_path);
                $decree_fileinfo['created_at'] = $row->decree->created_at->format('d-m-Y H:i');
                $decree_fileinfo['issued_date'] = $row->decree->issued_date;
            }

            $data[] = [
                'req_id' => $row->req_id,
                'reg_num' => $row->reg_num,
                'name' => $row->name,
                'request_date' => $row->created_at->format('d-m-Y H:i'),
                'status' => $row->status,
                'status_text' => $row->approval_status,
                'decree' => $decree_fileinfo,
                // 'status_text' => $row->approval_status.(!empty($row->approval_time)? " (".$row->approval_time.")": ''),
                'status_text' => $row->approval_status,
                'actions' => null, // Will be filled by JS
                'is_my_approval' => $isMyAppoval
            ];
        }
        
        $totalRecords = $filteredRecords = count($rs);

        return response()->json([
            'draw' => $params['draw'],
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function list_document($req_id){
        $params = request()->all();

        $request = TrxRequest::query()
            ->with('documents.doctype')
            ->where('is_disabled', 0)
            ->where('req_id', $req_id)
            ->first();

        $documents = $request->documents;

        return response()->json(new ApiResponse($documents));
    }

    public function revision_notes_list($req_id){
        $params = request()->all();

        $response = new ApiResponse();

        $req = TrxRequest::query()
            ->with('documents')
            ->where('req_id', $req_id)
            ->first();

        $req_note = TrxRevisionNotes::query()
            ->where('req_id', $req_id)
            ->whereNull('req_doc_id')
            ->orderBy('created_at', 'desc')
            ->first();

        $results = [
            'request_note' => !empty($req_note)? [ 'rev_note_id' => $req_note->rev_note_id, 'notes' => $req_note->notes ]: null,
            'docreq_notes' => []
        ];

        $docreq_notes = TrxRevisionNotes::query()
            ->with('request_document.doctype')
            ->where('req_id', $req_id)
            ->whereNotNull('req_doc_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $temp_assoc = [];
        foreach($docreq_notes as $row){
            if(empty($temp_assoc[$row->req_doc_id])){
                $temp_assoc[$row->req_doc_id] = $row;
            }
        }

        foreach($temp_assoc as $key => $row){
            $results['docreq_notes'] []= $row;
        }

        $response->data = $results;

        return response()->json($response);
    }

    public function revision_notes_update(PermitService $permitService){
        $params = request()->all();

        $response = new ApiResponse();

        DB::beginTransaction();
        try {
            $note = null;

            if(empty($params['req_doc_id'])){
                $note = TrxRevisionNotes::query()
                        ->where('is_disabled', 0)
                        ->where('req_id', $params['req_id'])
                        ->whereNull('req_doc_id')
                        ->orderBy('created_at', 'desc')
                        ->first();

                if(empty($note)){
                    $note = new TrxRevisionNotes([
                        'req_id' => $params['req_id']
                    ]);
                }
            }else{
                // edit existing revision-note
                if(!empty($params['rev_note_id'])){
                    $note = TrxRevisionNotes::query()
                        ->where('is_disabled', 0)
                        ->where('rev_note_id', $params['rev_note_id'])
                        ->first();
                }

                // create new revision-note
                if(empty($note)){
                    $note = new TrxRevisionNotes([
                        'req_id' => $params['req_id'],
                        'req_doc_id' => $params['req_doc_id'],
                    ]);
                }

            }

            $note->notes = $params['revision_notes'];

            if(!empty($params['is_resolved'])){
                $note->is_resolved = $params['is_resolved'];
            }

            $note->save();

            $req = TrxRequest::query()
                ->where('req_id', $params['req_id'])
                ->first();

            if($req->status == TrxRequest::STATUS_VERIFIED){
                $req->status = TrxRequest::STATUS_SUBMITTED;
                $permitService->resetApproval($params['req_id']);
            }
            $req->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = $e->getCode();
            $response->message = $e->getMessage();
        }

        return response()->json($response);
    }

    public function revision_documents_list($req_id){
        $request = TrxRequest::query()
            ->with(['documents.doctype', 'documents.revision_note'])
            ->where('req_id', $req_id)
            ->first();

        $documents = $request->documents->filter(function($doc) {
            return !empty($doc->revision_note) && $doc->revision_note->is_resolved == 0;
        });

        // Transform data untuk response
        $results = $documents->map(function($doc) {
            $path_arr = explode("/", $doc->file_path);
            $filename = end($path_arr);
            
            return [
                'req_doc_id' => $doc->req_doc_id,
                'doctype' => $doc->doctype,
                'filename' => $filename,
                'mime' => Storage::mimeType($doc->file_path),
                'revision_note' => $doc->revision_note
            ];
        });

        return response()->json(new ApiResponse($results->values()));
    }

    public function reqdoc_update(){
        $params = request()->all();

        $response = new ApiResponse();
        DB::beginTransaction();
        try {
            $doc = TrxRequestDocument::query()
                ->with('revision_note')
                ->where('req_doc_id', $params['req_doc_id'])
                ->first();

            if(!empty($params['verify_status'])){
                $verify_status = $params['verify_status'];

                if($verify_status == TrxRequestDocument::STATUS_VERIFIED){
                    $notes = TrxRevisionNotes::query()
                        ->where('is_disabled', 0)
                        ->where('req_doc_id', $params['req_doc_id'])
                        ->get();

                    foreach($notes as $row){
                        $row->is_resolved = 1;
                        $row->save();
                    }
                }

                if($doc->verify_status != $verify_status){
                    $doc->verify_status = $verify_status;
                }
            }

            //condition of file-revision
            if(!empty($params['file'])){
                // Delete the old file if it exists
                // if (!empty($doc->file_path) && Storage::exists($doc->file_path)) {
                //     Storage::delete($doc->file_path);
                // }

                $file = $params['file'];
                $savedFolder = "request_documents/$doc->req_id";
                $savedPath = $file->storeAs($savedFolder, $file->getClientOriginalName());
                $doc->file_path = $savedPath;
            }

            $doc->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = 500;
            $response->message = $e->getMessage();
        }

        return response()->json($response);
    }

    public function request_update(PermitService $permitService){
        $params = request()->all();

        $response = new ApiResponse();
        DB::beginTransaction();
        try {
            $req = TrxRequest::where('req_id', $params['req_id'])->first();

            $params_temp = $params;
            unset($params_temp['req_id']);
            unset($params_temp['mode']);

            if(!empty($params['mode'])){
                if($params['mode'] == self::REQUEST_UPDATE_MODE_SUBMIT){
                    $req->status = TrxRequest::STATUS_SUBMITTED;

                    $docs = TrxRequestDocument::query()
                        ->with('revision_note')
                        ->where('is_disabled', 0)
                        ->where('verify_status', TrxRequestDocument::STATUS_REVISION)
                        ->where('req_id', $params['req_id'])
                        ->get();
                    
                    foreach($docs as $row){
                        $row->verify_status = TrxRequestDocument::STATUS_PENDING;
                        $row->save();
                        
                        if(!empty($row->revision_note)){
                            $row->revision_note->is_resolved = 1;
                            $row->revision_note->save();
                        }
                    }
                }
                
                if($params['mode'] == self::REQUEST_UPDATE_MODE_VERIFY){
                    $req->status = TrxRequest::STATUS_VERIFIED;
                }
                
                if($params['mode'] == self::REQUEST_UPDATE_MODE_APPROVE){
                    $userinfo = userinfo();
                    $permitService->updateRequestApproval($params['req_id'], $userinfo->user_id, TrxRequestApproval::STATUS_APPROVED);
                }
            }

            $req->fill($params_temp);

            $req->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = 500;
            $response->message = $e->getMessage();
        }

        return response()->json($response);
    }

    public function decree_upload(PermitService $permitService){
        $params = request()->all();

        $response = new ApiResponse();
        DB::beginTransaction();
        try {
            $req = TrxRequest::query()
                ->with('decree')
                ->where('req_id', $params['req_id'])
                ->first();

            $params_data = $params;
            unset($params_data['decree_file']);

            $decree_type = null;
            if(empty($req->decree)){
                $decree_type = TrxPermitDecree::TYPE_NEW;
                if(!empty($req->ext_of_decree)){
                    $decree_type = TrxPermitDecree::TYPE_EXTENSION;
                }
            }else{
                $decree_type = TrxPermitDecree::TYPE_REVISION;
            }
            
            $decree = new TrxPermitDecree($params_data);
            $decree->decree_type = $decree_type;

            $file = $params['decree_file'];
            $savedFolder = "decree/$req->req_id";
            $savedPath = $file->storeAs($savedFolder, $file->getClientOriginalName());

            $decree->file_path = $savedPath;

            $decree->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = 500;
            $response->message = $e->getMessage();
        }

        return response()->json($response);
    }
}