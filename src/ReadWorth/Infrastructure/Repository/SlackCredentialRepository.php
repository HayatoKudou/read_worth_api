<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\SlackCredential;

class SlackCredentialRepository
{
    public function update(int $workspace_id, string $accessToken, string $channelName, int $channelId): void
    {
        SlackCredential::updateOrCreate([
            'workspace_id' => $workspace_id,
        ], [
            'access_token' => $accessToken,
            'channel_name' => $channelName,
            'channel_id' => $channelId,
        ]);
    }

    public function findByWorkspaceId(int $workspaceId): SlackCredential|null
    {
        return SlackCredential::where('workspace_id', $workspaceId)->first();
    }
}
