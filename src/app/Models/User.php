<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property \App\Models\Belonging[]|\Illuminate\Database\Eloquent\Collection $belongings
 * @property null|int $belongings_count
 * @property null|\App\Models\BookPurchaseApply $bookPurchaseApply
 * @property null|\App\Models\BookRentalApply $bookRentalApply
 * @property null|\App\Models\BookReview $bookReview
 * @property \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property null|int $tokens_count
 * @property \App\Models\Workspace[]|\Illuminate\Database\Eloquent\Collection $workspaces
 * @property null|int $workspaces_count
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
