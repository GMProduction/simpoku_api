<?php
namespace App\Traits;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid as RamseyUUID;

trait Uuid {

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = RamseyUUID::uuid4()->toString();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
