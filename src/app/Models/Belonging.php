<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Belonging.
 *
 * @property int $id
 * @property int $user_id
 * @property int $workspace_id
 * @property int $role_id
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property null|\App\Models\Workspace $client
 * @property \App\Models\Role $role
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging query()
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Belonging whereWorkspaceId($value)
 * @mixin \Eloquent
 */
class Belonging extends Authenticate
{
    use HasFactory;
    protected $guarded = [];

    public function client(): HasOne
    {
        return $this->hasOne(Workspace::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
