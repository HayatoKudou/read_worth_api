<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Client.
 *
 * @property int $id
 * @property string $name
 * @property Collection|User[] $users
 * @property Book[]|Collection $books
 * @property Plan $plan
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @mixin Builder
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property null|int $users_count
 * @method static Builder|Client whereCreatedAt($value)
 * @method static Builder|Client whereId($value)
 * @method static Builder|Client whereName($value)
 * @method static Builder|Client whereUpdatedAt($value)
 * @property int $plan_id
 * @method static Builder|Client wherePlanId($value)
 * @property int $purchase_limit
 * @property string $purchase_limit_unit
 * @property null|int $books_count
 * @method static Builder|Client wherePurchaseLimit($value)
 * @method static Builder|Client wherePurchaseLimitUnit($value)
 * @property int $private_ownership_allow
 * @method static Builder|Client wherePrivateOwnershipAllow($value)
 * @property null|\App\Models\SlackCredential $credential
 * @property int $enable_purchase_limit
 * @property null|\App\Models\SlackCredential $slackCredential
 * @method static Builder|Client whereEnablePurchaseLimit($value)
 * @method static \Database\Factories\ClientFactory factory(...$parameters)
 */
class Client extends Authenticate
{
    use HasFactory;

    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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
