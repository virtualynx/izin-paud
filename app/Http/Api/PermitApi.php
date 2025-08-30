<?php

namespace App\Http\Api;

use App\Dto\ApiResponse;
use App\Models\Masters\DoctypeRequirement;
use App\Models\TrxRequest;
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
            if($row->is_optional == 1 && empty($params[$row->doctypereq_id]))continue;

            $rule = "file|mimes:pdf,jpg,jpeg,png|max:$maxSizeInBytes";
            if($row->is_optional == 0){
                $rule .= "|required";
            }

            $validator_rules[$row->doctypereq_id] = $rule;
        }

        // Validate the request
        $validator = Validator::make($params, $validator_rules);
        $is_fails = $validator->fails();
        $errors = $validator->errors();

        // if ($validator->fails()) {
        //     // return response()->json([
        //     //     'success' => false,
        //     //     'message' => 'Validasi gagal',
        //     //     'errors' => $validator->errors()
        //     // ], 422);

        //     return response()->json(
        //         new ApiResponse($validator->errors(), 422, 'validation fail')
        //     );
        // }

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
}