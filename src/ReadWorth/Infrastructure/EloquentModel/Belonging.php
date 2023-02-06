<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ReadWorth\Infrastructure\EloquentModel\Belonging.
 *
 * @property int $id
 * @property int $user_id
 * @property int $workspace_id
 * @property int $role_id
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property null|\ReadWorth\Infrastructure\EloquentModel\Workspace $client
 * @property \ReadWorth\Infrastructure\EloquentModel\Role $role
 *
 * @method static \Database\Factories\ReadWorth\Infrastructure\EloquentModel\BelongingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging query()
 * @mixin \Eloquent
 */
class Belonging extends Authenticate
{
    use HasFactory;
    protected $guarded = [];

    public function client(): HasOne
    {
        return $this->hasOne(Workspace::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
