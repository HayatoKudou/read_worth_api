<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\BookApplication.
 *
 * @property number $id
 * @property number $user_id
 * @property number $client_id
 * @property number $book_id
 * @property string $reason
 *
 * @method static Builder|BookCategory newModelQuery()
 * @method static Builder|BookCategory newQuery()
 * @method static Builder|BookCategory query()
 * @method static Builder|BookCategory organization()
 * @mixin Builder
 */
class BookApplication extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'book_applications';

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }
}