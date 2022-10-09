<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ReadWorth\Infrastructure\EloquentModel\BookHistory.
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $action
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \ReadWorth\Infrastructure\EloquentModel\Book $book
 * @property \ReadWorth\Infrastructure\EloquentModel\User $user
 *
 * @method static Builder|BookHistory newModelQuery()
 * @method static Builder|BookHistory newQuery()
 * @method static Builder|BookHistory query()
 * @mixin \Eloquent
 */
class BookHistory extends Model
{
    protected $guarded = [];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
