<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * ReadWorth\Infrastructure\EloquentModel\SlackCredential.
 *
 * @property int $id
 * @property int $workspace_id
 * @property null|string $access_token
 * @property null|string $channel_id
 * @property null|string $channel_name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 *
 * @method static Builder|SlackCredential newModelQuery()
 * @method static Builder|SlackCredential newQuery()
 * @method static Builder|SlackCredential query()
 * @mixin \Eloquent
 */
class SlackCredential extends Model
{
    protected $guarded = [];
}
