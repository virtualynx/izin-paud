<?php

namespace App\Http\Api;

use App\Dto\ApiResponse;
use App\Models\Masters\DoctypeRequirement;
use Illuminate\Routing\Controller;

class PermitApi extends Controller
{
    public function __construct(){
    }

    public function docrec_list(){
        $params = request()->all();

        $results = DoctypeRequirement::where('is_disabled', 0)->get();

        return response()->json(new ApiResponse($results));
    }
}