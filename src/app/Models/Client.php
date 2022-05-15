<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticate;

/**
 * App\Models\Client.
 *
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @mixin \Eloquent
 */

class Client extends Authenticate
{

    protected $fillable = [
        'name',
    ];
}
