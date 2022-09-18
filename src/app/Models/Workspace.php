<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Workspace
 *
 * @property int $id
 * @property int $plan_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|\App\Models\Book[] $books
 * @property-read int|null $books_count
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\SlackCredential|null $slackCredential
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
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
    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
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
