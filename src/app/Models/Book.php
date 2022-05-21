<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Book.
 *
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
}
