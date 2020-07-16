<?php
/**
 * Created by PhpStorm.
 * User: Adriano
 * Date: 16/07/20
 * Time: 01:04
 */

namespace App\Model\Traits;

use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{
    public static function boot()
    {
        parent::boot();
        static::creating(function($obj) {
            $obj->id = RamseyUuid::uuid4();
        });
    }

}