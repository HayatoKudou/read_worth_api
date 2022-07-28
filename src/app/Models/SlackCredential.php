<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\SlackCredentials.
 *
 * @property int $id
 * @property string $access_token
 * @property string $channel_name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @method static Builder|SlackCredential newModelQuery()
 * @method static Builder|SlackCredential newQuery()
 * @method static Builder|SlackCredential query()
 * @method static Builder|SlackCredential whereAccessToken($value)
 * @method static Builder|SlackCredential whereChannelName($value)
 * @method static Builder|SlackCredential whereCreatedAt($value)
 * @method static Builder|SlackCredential whereId($value)
 * @method static Builder|SlackCredential whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $client_id
 * @method static Builder|SlackCredential whereClientId($value)
 * @property string $channel_id
 * @method static Builder|SlackCredential whereChannelId($value)
 */
class SlackCredential extends Model
{
    protected $guarded = [];
}
