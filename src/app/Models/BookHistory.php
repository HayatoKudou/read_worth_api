<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookHistory
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $action
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book $book
 * @property-read \App\Models\User $user
 * @method static Builder|BookHistory newModelQuery()
 * @method static Builder|BookHistory newQuery()
 * @method static Builder|BookHistory query()
 * @method static Builder|BookHistory whereAction($value)
 * @method static Builder|BookHistory whereBookId($value)
 * @method static Builder|BookHistory whereCreatedAt($value)
 * @method static Builder|BookHistory whereId($value)
 * @method static Builder|BookHistory whereUpdatedAt($value)
 * @method static Builder|BookHistory whereUserId($value)
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
