<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply.
 *
 * @property int $id
 * @property int $user_id
 * @property int $workspace_id
 * @property int $book_id
 * @property string $reason
 * @property int $price
 * @property int $step
 * @property null|string $location
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \ReadWorth\Infrastructure\EloquentModel\Book $book
 * @property null|\ReadWorth\Infrastructure\EloquentModel\Workspace $client
 * @property \ReadWorth\Infrastructure\EloquentModel\User $user
 *
 * @method static Builder|BookPurchaseApply newModelQuery()
 * @method static Builder|BookPurchaseApply newQuery()
 * @method static Builder|BookPurchaseApply organization(string $workspaceId)
 * @method static Builder|BookPurchaseApply query()
 * @mixin \Eloquent
 */
class BookPurchaseApply extends Model
{
    public const REFUSED = 0;
    public const NEED_ACCEPT = 1;
    public const NEED_BUY = 2;
    public const NEED_NOTIFICATION = 3;
    protected $guarded = [];
    protected $table = 'book_purchase_applies';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function scopeOrganization(Builder $query, string $workspaceId): Builder
    {
        return $query->where('workspace_id', $workspaceId);
    }
}
