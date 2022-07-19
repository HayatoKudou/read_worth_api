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
 *
 * @method static Builder|SlackCredentials newModelQuery()
 * @method static Builder|SlackCredentials newQuery()
 * @method static Builder|SlackCredentials query()
 * @method static Builder|SlackCredentials whereAccessToken($value)
 * @method static Builder|SlackCredentials whereChannelName($value)
 * @method static Builder|SlackCredentials whereCreatedAt($value)
 * @method static Builder|SlackCredentials whereId($value)
 * @method static Builder|SlackCredentials whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @property int $client_id
 *
 * @method static Builder|SlackCredentials whereClientId($value)
 */
class SlackCredentials extends Model
{
    protected $guarded = [];
}
