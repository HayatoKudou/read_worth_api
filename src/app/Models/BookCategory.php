<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\BookCategory
 *
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|BookCategory newModelQuery()
 * @method static Builder|BookCategory newQuery()
 * @method static Builder|BookCategory organization(string $workspaceId)
 * @method static Builder|BookCategory query()
 * @method static Builder|BookCategory whereCreatedAt($value)
 * @method static Builder|BookCategory whereId($value)
 * @method static Builder|BookCategory whereName($value)
 * @method static Builder|BookCategory whereUpdatedAt($value)
 * @method static Builder|BookCategory whereWorkspaceId($value)
 * @mixin \Eloquent
 */
class BookCategory extends Model
{
    protected $guarded = [];
    protected $table = 'book_category';

    public function scopeOrganization(Builder $query, string $workspaceId): Builder
    {
        return $query->where('workspace_id', $workspaceId);
    }
}
