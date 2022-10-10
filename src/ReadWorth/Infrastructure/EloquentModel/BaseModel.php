<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected $guarded = [];
}
