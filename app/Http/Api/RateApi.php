<?php

namespace App\Http\Api;

use App\Dto\ApiResponse;
use App\Models\Masters\DoctypeRequirement;
use App\Models\TrxPermitDecree;
use App\Models\TrxRequest;
use App\Models\TrxRequestApproval;
use App\Models\TrxRequestDocument;
use App\Models\TrxRevisionNotes;
use App\Models\TrxServiceRate;
use App\Services\PermitService;
use App\Services\PositionService;
use App\Services\UserService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RateApi extends Controller
{
    public function __construct(){
    }

    public function get($req_id){
        $params = request()->all();

        $req = TrxRequest::query()
            ->with('rating')
            ->where('req_id', $req_id)
            ->first();

        return response()->json(new ApiResponse([
            'name' => $req->name,
            'rating' => $req->rating
        ]));
    }

    public function send(){
        $params = request()->all();

        $req = TrxRequest::query()
            ->with('rating')
            ->where('req_id', $params['req_id'])
            ->first();

        if(!empty($req->rating)){
            $req->rating->rating = $params['rating'];
            $req->rating->notes = $params['notes'];
            $req->rating->save();
        }else{
            $req->rating()->create([
                'req_id' => $params['req_id'],
                'rating' => $params['rating'],
                'notes' => $params['notes'],
            ]);
        }

        return response()->json(new ApiResponse());
    }
}