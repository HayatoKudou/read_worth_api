<?php

namespace App\Models;

use Carbon\Carbon;
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
 * @property string $review
 * @property Carbon $rental_date
 * @property Carbon $expected_return_date
 * @property Carbon $return_date
 * @property User $user
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
 * @method static Builder|BookRentalApply whereBookId($value)
 * @method static Builder|BookRentalApply whereClientId($value)
 * @method static Builder|BookRentalApply whereCreatedAt($value)
 * @method static Builder|BookRentalApply whereExpectedReturnDate($value)
 * @method static Builder|BookRentalApply whereId($value)
 * @method static Builder|BookRentalApply whereReason($value)
 * @method static Builder|BookRentalApply whereRentalDate($value)
 * @method static Builder|BookRentalApply whereReturnDate($value)
 * @method static Builder|BookRentalApply whereReview($value)
 * @method static Builder|BookRentalApply whereUpdatedAt($value)
 * @method static Builder|BookRentalApply whereUserId($value)
 */
class BookRentalApply extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'book_rental_applies';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }
}
