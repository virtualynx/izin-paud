<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuidPrimaryKey
{
    public function initializeHasUuidPrimaryKey()
    {
        $this->setIncrementing(false);
        $this->setKeyType('string');
    }

    protected static function bootHasUuidPrimaryKey()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}