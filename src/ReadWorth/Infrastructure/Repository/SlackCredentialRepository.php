<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\SlackCredential;

class SlackCredentialRepository
{
    public function findByWorkspaceId(int $workspaceId): SlackCredential|null
    {
        return SlackCredential::where('workspace_id', $workspaceId)->first();
    }
}
