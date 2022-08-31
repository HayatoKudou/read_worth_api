<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property null|string $google_access_token
 * @property null|string $api_token
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Client[]|\Illuminate\Database\Eloquent\Collection $clients
 * @property null|int $clients_count
 * @property \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property null|int $tokens_count
 *
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereApiToken($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereGoogleAccessToken($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticate
{
    use HasApiTokens;

    protected $guarded = [];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'belongings'
        );
    }

    public function role(string $clientId)
    {
        return $this->belongsToMany(
            Role::class,
            'belongings',
        )->where('client_id', $clientId)->first();
    }
}
