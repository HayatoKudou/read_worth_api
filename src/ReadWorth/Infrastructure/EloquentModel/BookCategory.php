<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ReadWorth\Infrastructure\EloquentModel\BookCategory.
 *
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\ReadWorth\Infrastructure\EloquentModel\Book[] $books
 *
 * @method static Builder|BookCategory newModelQuery()
 * @method static Builder|BookCategory newQuery()
 * @method static Builder|BookCategory organization(string $workspaceId)
 * @method static Builder|BookCategory query()
 * @mixin \Eloquent
 */
class BookCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'book_category';

    public function scopeOrganization(Builder $query, string $workspaceId): Builder
    {
        return $query->where('workspace_id', $workspaceId);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
