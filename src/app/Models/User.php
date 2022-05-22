<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User.
 *
 * @property number $id
 * @property number $client_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $api_token
 * @property Client $client
 *
 * @method static Builder|\App\Models\User newModelQuery()
 * @method static Builder|\App\Models\User newQuery()
 * @method static Builder|\App\Models\User query()
 * @mixin Builder
 */

class User extends Authenticate
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
