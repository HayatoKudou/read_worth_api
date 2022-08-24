<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookPurchaseApply.
 *
 * @property int $id
 * @property int $user_id
 * @property int $client_id
 * @property int $book_id
 * @property string $reason
 * @property int $price
 * @property int $step
 * @property null|string $location
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Book $book
 * @property \App\Models\Client $client
 * @property \App\Models\User $user
 *
 * @method static Builder|BookPurchaseApply newModelQuery()
 * @method static Builder|BookPurchaseApply newQuery()
 * @method static Builder|BookPurchaseApply organization(string $clientId)
 * @method static Builder|BookPurchaseApply query()
 * @method static Builder|BookPurchaseApply whereBookId($value)
 * @method static Builder|BookPurchaseApply whereClientId($value)
 * @method static Builder|BookPurchaseApply whereCreatedAt($value)
 * @method static Builder|BookPurchaseApply whereId($value)
 * @method static Builder|BookPurchaseApply whereLocation($value)
 * @method static Builder|BookPurchaseApply wherePrice($value)
 * @method static Builder|BookPurchaseApply whereReason($value)
 * @method static Builder|BookPurchaseApply whereStep($value)
 * @method static Builder|BookPurchaseApply whereUpdatedAt($value)
 * @method static Builder|BookPurchaseApply whereUserId($value)
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
        return $this->belongsTo(Client::class);
    }

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }
}
