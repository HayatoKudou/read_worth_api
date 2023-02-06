<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ReadWorth\Infrastructure\EloquentModel\Workspace.
 *
 * @property int $id
 * @property int $plan_id
 * @property string $name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property Collection|\ReadWorth\Infrastructure\EloquentModel\Book[] $books
 * @property \ReadWorth\Infrastructure\EloquentModel\Plan $plan
 * @property Collection|\ReadWorth\Infrastructure\EloquentModel\Role[] $roles
 * @property null|\ReadWorth\Infrastructure\EloquentModel\SlackCredential $slackCredential
 * @property Collection|\ReadWorth\Infrastructure\EloquentModel\User[] $users
 *
 * @method static \Database\Factories\ReadWorth\Infrastructure\EloquentModel\WorkspaceFactory factory(...$parameters)
 * @method static Builder|Workspace newModelQuery()
 * @method static Builder|Workspace newQuery()
 * @method static Builder|Workspace query()
 * @mixin \Eloquent
 */
class Workspace extends Authenticate
{
    use HasFactory;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'belongings',
        );
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'belongings',
        );
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function slackCredential(): HasOne
    {
        return $this->hasOne(SlackCredential::class);
    }
}
