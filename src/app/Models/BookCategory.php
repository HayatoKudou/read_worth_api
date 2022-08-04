<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\BookCategory.
 *
 * @property number $id
 * @property number $client_id
 * @property string $name
 * @method static Builder|BookCategory newModelQuery()
 * @method static Builder|BookCategory newQuery()
 * @method static Builder|BookCategory query()
 * @method static Builder|BookCategory organization()
 * @mixin Builder
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @method static Builder|BookCategory whereClientId($value)
 * @method static Builder|BookCategory whereCreatedAt($value)
 * @method static Builder|BookCategory whereId($value)
 * @method static Builder|BookCategory whereName($value)
 * @method static Builder|BookCategory whereUpdatedAt($value)
 * @method static \Database\Factories\BookCategoryFactory factory(...$parameters)
 */
class BookCategory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'book_category';

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }
}
