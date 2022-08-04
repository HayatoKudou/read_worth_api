<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookPurchaseApply.
 *
 * @property number $id
 * @property number $user_id
 * @property number $client_id
 * @property number $book_id
 * @property string $reason
 * @property User $user
 * @property Book $book
 * @method static Builder|BookCategory newModelQuery()
 * @method static Builder|BookCategory newQuery()
 * @method static Builder|BookCategory query()
 * @method static Builder|BookCategory organization()
 * @mixin Builder
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @method static Builder|BookPurchaseApply whereBookId($value)
 * @method static Builder|BookPurchaseApply whereClientId($value)
 * @method static Builder|BookPurchaseApply whereCreatedAt($value)
 * @method static Builder|BookPurchaseApply whereId($value)
 * @method static Builder|BookPurchaseApply whereReason($value)
 * @method static Builder|BookPurchaseApply whereUpdatedAt($value)
 * @method static Builder|BookPurchaseApply whereUserId($value)
 * @property int $price
 * @method static Builder|BookPurchaseApply wherePrice($value)
 * @property int $step
 * @method static Builder|BookPurchaseApply whereStep($value)
 * @property null|string $location
 * @method static Builder|BookPurchaseApply whereLocation($value)
 * @property \App\Models\Client $client
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
        return $this->belongsTo(Client::class);
    }

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }
}
