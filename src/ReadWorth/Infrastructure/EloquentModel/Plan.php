<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ReadWorth\Infrastructure\EloquentModel\Plan.
 *
 * @property int $id
 * @property string $name プラン名
 * @property int $price プラン価格
 * @property int $max_members メンバー上限数
 * @property int $max_books 書籍上限数
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Collection|\ReadWorth\Infrastructure\EloquentModel\User[] $users
 *
 * @method static Builder|Plan newModelQuery()
 * @method static Builder|Plan newQuery()
 * @method static Builder|Plan query()
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
