<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Role.
 *
 * @property int $id
 * @property int $user_id
 * @property int $is_account_manager
 * @property int $is_book_manager
 * @property int $is_client_manager
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\User $user
 *
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereIsAccountManager($value)
 * @method static Builder|Role whereIsBookManager($value)
 * @method static Builder|Role whereIsClientManager($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role whereUserId($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
