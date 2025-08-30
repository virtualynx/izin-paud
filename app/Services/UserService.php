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
    public function __construct()
    {
        
    }
    
    public function getUserProfile($user_id): array
    {
        $result = UserProfile::where('user_id', $user_id)->first();

        return $result;
    }
}