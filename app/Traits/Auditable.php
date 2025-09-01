<?php

namespace App\Traits;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    protected static function bootAuditable(): void
    {
        static::creating(function ($model) {
            $userinfo = userinfo();
            if(!empty($userinfo)){
                $model->created_by = $userinfo->user_id;
            }
        });

        static::updating(function ($model) {
            $userinfo = userinfo();
            if(!empty($userinfo)){
                $model->updated_by = $userinfo->user_id;
            }
        });
    }
}