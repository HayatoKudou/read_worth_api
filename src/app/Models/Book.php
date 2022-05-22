<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Book.
 *
 * @property number $client_id
 * @property number $book_category_id
 * @property string $title
 * @property string $description
 * @property string $image_path
 *
 * @method static Builder|\App\Models\User newModelQuery()
 * @method static Builder|\App\Models\User newQuery()
 * @method static Builder|\App\Models\User query()
 * @mixin Builder
 */

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
