<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookReview
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $review
 * @property int $rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|BookReview newModelQuery()
 * @method static Builder|BookReview newQuery()
 * @method static Builder|BookReview query()
 * @method static Builder|BookReview whereBookId($value)
 * @method static Builder|BookReview whereCreatedAt($value)
 * @method static Builder|BookReview whereId($value)
 * @method static Builder|BookReview whereRate($value)
 * @method static Builder|BookReview whereReview($value)
 * @method static Builder|BookReview whereUpdatedAt($value)
 * @method static Builder|BookReview whereUserId($value)
 * @mixin \Eloquent
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
