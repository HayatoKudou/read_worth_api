<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BookRentalApply.
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
 * @property \App\Models\User $user
 *
 * @method static Builder|BookRentalApply newModelQuery()
 * @method static Builder|BookRentalApply newQuery()
 * @method static Builder|BookRentalApply organization(string $workspaceId)
 * @method static Builder|BookRentalApply query()
 * @method static Builder|BookRentalApply whereBookId($value)
 * @method static Builder|BookRentalApply whereCreatedAt($value)
 * @method static Builder|BookRentalApply whereExpectedReturnDate($value)
 * @method static Builder|BookRentalApply whereId($value)
 * @method static Builder|BookRentalApply whereReason($value)
 * @method static Builder|BookRentalApply whereRentalDate($value)
 * @method static Builder|BookRentalApply whereReturnDate($value)
 * @method static Builder|BookRentalApply whereUpdatedAt($value)
 * @method static Builder|BookRentalApply whereUserId($value)
 * @method static Builder|BookRentalApply whereWorkspaceId($value)
 * @mixin \Eloquent
 */
class BookRentalApply extends Model
{
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
