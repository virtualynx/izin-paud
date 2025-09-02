<?php

namespace App\Http\Api;

use App\Dto\ApiResponse;
use App\Models\Masters\DoctypeRequirement;
use App\Models\TrxRequest;
use App\Services\PermitService;
use App\Services\PositionService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PermitApi extends Controller
{
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
        $maxSizeInBytes = 2048;
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
            $approvalSequence = $positionService->generateApprovalSequence(); 
            foreach($approvalSequence as $sequence){
                $new_request->approvals()->create([
                    'req_id' => $new_request->req_id,
                    'level' => $sequence['level'],
                    'approver_user_id' => $sequence['approver_user_id'],
                    'approver_position_id' => $sequence['approver_position_id'],
                ]);
            }
            
            DB::commit();
            
            $response->message = 'Pengajuan izin operasional PAUD berhasil dikirim.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response->status = $e->getCode();
            $response->message = $e->getMessage();
        }

        return response()->json(new ApiResponse($response));
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
    
    public function dt_to_verify_list(PermitService $permitService){
        $params = request()->all();

        $rs = $permitService->listUnverifiedRequest();

        $totalRecords = $filteredRecords = count($rs);

        $user = userinfo();

        $data = [];
        foreach ($rs as $row) {
            $data[] = [
                'req_id' => $row->req_id,
                'reg_num' => $row->reg_num,
                'name' => $row->name,
                'request_date' => $row->created_at->format('d-m-Y H:i'),
                'actions' => null, // Will be filled by JS
                'is_own' => $row->created_by == $user->user_id
            ];
        }

        return response()->json([
            'draw' => $params['draw'],
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
}