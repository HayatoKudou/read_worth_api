<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ReadWorth\Infrastructure\EloquentModel\BookReview.
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $review
 * @property int $rate
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \ReadWorth\Infrastructure\EloquentModel\User $user
 *
 * @method static Builder|BookReview newModelQuery()
 * @method static Builder|BookReview newQuery()
 * @method static Builder|BookReview query()
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
