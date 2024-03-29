<?php

namespace ReadWorth\Application\UseCase\Slack;

use GuzzleHttp\Client;
use ReadWorth\Infrastructure\SlackAPI\SlackApiClient;
use ReadWorth\Infrastructure\Repository\SlackCredentialRepository;

class SlackNotification
{
    public function __construct(
        private readonly SlackCredentialRepository $slackCredentialRepository
    ) {
    }

    public function notification(string $title, string $message, int $workspaceId, string $imagePath = null): void
    {
        $slackCredential = $this->slackCredentialRepository->findByWorkspaceId($workspaceId);

        if ($slackCredential) {
            $slackClient = new SlackApiClient(new Client(), $slackCredential->access_token);
            $slackClient->postMessage($slackCredential->channel_id, $title, $message, $imagePath);
        }
    }
}
