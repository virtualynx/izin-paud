<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Masters\Position;
use App\Models\UserProfile;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private $positionService;
    
    public function __construct(
        PositionService $positionService
    ){
        $this->positionService = $positionService;
    }
    
    public function getUserProfile($user_id)
    {
        $result = UserProfile::query()
            ->with('positions')
            ->where('user_id', $user_id)
            ->first();

        return $result;
    }

    public function isVerificator($user_id){
        $mainPosition = $this->getMainPosition($user_id);

        if(!empty($mainPosition)){
            $subordinates = Position::query()
                ->where('is_disabled', 0)
                ->where('parent_position_id', $mainPosition->position_id)
                ->get()
                ;

            return count($subordinates) == 0;
        }

        return false;
    }

    public function isApprover($user_id){
        $mainPosition = $this->getMainPosition($user_id);

        if(!empty($mainPosition)){
            $subordinates = Position::query()
                ->where('is_disabled', 0)
                ->where('parent_position_id', $mainPosition->position_id)
                ->get()
                ;

            return count($subordinates) > 0;
        }

        return false;
    }

    public function getMainPosition($user_id){
        $profile = $this->getUserProfile($user_id);

        if(!empty($profile) && count($profile->positions) > 0){
            $mainPosition = $profile->positions[0];

            return $mainPosition;
        }

        return null;
    }
}