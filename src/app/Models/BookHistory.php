<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookHistory.
 *
 * @property int $id
 * @property int $book_id
 * @property string $action
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Book $book
 * @property \App\Models\User $user
 *
 * @method static Builder|BookHistory newModelQuery()
 * @method static Builder|BookHistory newQuery()
 * @method static Builder|BookHistory query()
 * @method static Builder|BookHistory whereAction($value)
 * @method static Builder|BookHistory whereBookId($value)
 * @method static Builder|BookHistory whereCreatedAt($value)
 * @method static Builder|BookHistory whereId($value)
 * @method static Builder|BookHistory whereUpdatedAt($value)
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
