<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Workspace.
 *
 * @property int $id
 * @property int $plan_id
 * @property string $name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Book[]|Collection $books
 * @property null|int $books_count
 * @property \App\Models\Plan $plan
 * @property null|\App\Models\SlackCredential $slackCredential
 * @property \App\Models\User[]|Collection $users
 * @property null|int $users_count
 *
 * @method static Builder|Workspace newModelQuery()
 * @method static Builder|Workspace newQuery()
 * @method static Builder|Workspace query()
 * @method static Builder|Workspace whereCreatedAt($value)
 * @method static Builder|Workspace whereId($value)
 * @method static Builder|Workspace whereName($value)
 * @method static Builder|Workspace wherePlanId($value)
 * @method static Builder|Workspace whereUpdatedAt($value)
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
