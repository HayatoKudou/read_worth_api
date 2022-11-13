<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ReadWorth\Infrastructure\EloquentModel\BookRentalApply.
 *
 * @property int $id
 * @property int $user_id
 * @property int $workspace_id
 * @property int $book_id
 * @property string $reason 申請理由
 * @property string $rental_date 貸出日
 * @property string $expected_return_date 返却予定日
 * @property null|string $return_date 返却日
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \ReadWorth\Infrastructure\EloquentModel\User $user
 *
 * @method static Builder|BookRentalApply newModelQuery()
 * @method static Builder|BookRentalApply newQuery()
 * @method static Builder|BookRentalApply organization(string $workspaceId)
 * @method static Builder|BookRentalApply query()
 * @mixin \Eloquent
 */
class BookRentalApply extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'book_rental_applies';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOrganization(Builder $query, string $workspaceId): Builder
    {
        return $query->where('workspace_id', $workspaceId);
    }
}
