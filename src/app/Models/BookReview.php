<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookReview.
 *
 * @property number $id
 * @property number $user_id
 * @property number $book_id
 * @property int rate
 * @property string $review
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
 * @method static Builder|BookReview whereRate($value)
 *
 * @property int $rate
 */
class BookReview extends Model
{
    protected $guarded = [];
    protected $table = 'book_reviews';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
