<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\SlackCredential.
 *
 * @property int $id
 * @property int $workspace_id
 * @property int $connected_user_id
 * @property null|string $access_token
 * @property null|string $channel_id
 * @property null|string $channel_name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 *
 * @method static Builder|SlackCredential newModelQuery()
 * @method static Builder|SlackCredential newQuery()
 * @method static Builder|SlackCredential query()
 * @method static Builder|SlackCredential whereAccessToken($value)
 * @method static Builder|SlackCredential whereChannelId($value)
 * @method static Builder|SlackCredential whereChannelName($value)
 * @method static Builder|SlackCredential whereConnectedUserId($value)
 * @method static Builder|SlackCredential whereCreatedAt($value)
 * @method static Builder|SlackCredential whereId($value)
 * @method static Builder|SlackCredential whereUpdatedAt($value)
 * @method static Builder|SlackCredential whereWorkspaceId($value)
 * @mixin \Eloquent
 */
class SlackCredential extends Model
{
    protected $guarded = [];
}
