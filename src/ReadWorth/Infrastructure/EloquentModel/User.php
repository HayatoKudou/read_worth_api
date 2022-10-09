<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ReadWorth\Infrastructure\EloquentModel\User.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property null|string $google_access_token
 * @property null|string $api_token
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\ReadWorth\Infrastructure\EloquentModel\Belonging[] $belongings
 * @property null|\ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply $bookPurchaseApply
 * @property null|\ReadWorth\Infrastructure\EloquentModel\BookRentalApply $bookRentalApply
 * @property null|\ReadWorth\Infrastructure\EloquentModel\BookReview $bookReview
 * @property \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property \Illuminate\Database\Eloquent\Collection|\ReadWorth\Infrastructure\EloquentModel\Workspace[] $workspaces
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 */
class User extends Authenticate
{
    use HasApiTokens;
    use HasFactory;

    protected $guarded = [];

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(
            Workspace::class,
            'belongings'
        );
    }

    public function role(string $workspaceId)
    {
        return $this->belongsToMany(
            Role::class,
            'belongings',
        )->where('workspace_id', $workspaceId)->first();
    }

    public function bookPurchaseApply(): HasOne
    {
        return $this->hasOne(BookPurchaseApply::class);
    }

    public function bookRentalApply(): HasOne
    {
        return $this->hasOne(BookRentalApply::class);
    }

    public function bookReview(): HasOne
    {
        return $this->hasOne(BookReview::class);
    }

    public function belongings(): HasMany
    {
        return $this->hasMany(Belonging::class);
    }
}
