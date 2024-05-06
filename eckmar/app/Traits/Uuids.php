<?php

namespace App\Traits;

use App\DigitalProduct;
use App\PhysicalProduct;
use Webpatser\Uuid\Uuid;

trait Uuids
{
    /**
     * Boot function from laravel.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            // digital and physical products doesnt generate separate ids
            // if key is not already defined
            if (!($model instanceof PhysicalProduct) && !($model instanceof DigitalProduct) && null === $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = Uuid::generate()->string;
            }
        });
    }
}
