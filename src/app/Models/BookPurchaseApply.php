<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 *
 * @method static Builder|BookCategory newModelQuery()
 * @method static Builder|BookCategory newQuery()
 * @method static Builder|BookCategory query()
 * @method static Builder|BookCategory organization()
 * @mixin Builder
 *
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 *
 * @method static Builder|BookPurchaseApply whereBookId($value)
 * @method static Builder|BookPurchaseApply whereClientId($value)
 * @method static Builder|BookPurchaseApply whereCreatedAt($value)
 * @method static Builder|BookPurchaseApply whereId($value)
 * @method static Builder|BookPurchaseApply whereReason($value)
 * @method static Builder|BookPurchaseApply whereUpdatedAt($value)
 * @method static Builder|BookPurchaseApply whereUserId($value)
 */
class BookPurchaseApply extends Model
{
    use HasFactory;

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

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }
}
