<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $name プラン名
 * @property int $price プラン価格
 * @property int $max_members メンバー上限数
 * @property int $max_books 書籍上限数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PlanFactory factory(...$parameters)
 * @method static Builder|Plan newModelQuery()
 * @method static Builder|Plan newQuery()
 * @method static Builder|Plan query()
 * @method static Builder|Plan whereCreatedAt($value)
 * @method static Builder|Plan whereId($value)
 * @method static Builder|Plan whereMaxBooks($value)
 * @method static Builder|Plan whereMaxMembers($value)
 * @method static Builder|Plan whereName($value)
 * @method static Builder|Plan wherePrice($value)
 * @method static Builder|Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Plan extends Authenticate
{
    use HasFactory;

    public const PLANS = ['free', 'demo'];
    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
