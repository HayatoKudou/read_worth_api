<?php

namespace App\Notifications;

use GuzzleHttp\Client;

class SlackNotification
{
    private Client $httpClient;
    private string $slackApiToken;

    public function __construct(Client $httpClient, string $slackApiToken = '')
    {
        $this->httpClient = $httpClient;
        $this->slackApiToken = $slackApiToken ?: \Config('const.slack.api_token');
    }

    private function notify(
        string $message,
        string $channel,
        ?string $title,
    ): void {
        $response = $this->httpClient->post(
            'https://slack.com/api/chat.postMessage',
            [
                'json' => [
                    'channel' => '#' . $channel,
                    'attachments' => [
                        [
                            'title' => $title,
                            'text' => $message,
                        ],
                    ],
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->slackApiToken,
                ],
            ]
        );
        $json = $response->getBody()->getContents();
        $body = json_decode($json, true);

        if (!$body['ok']) {
            \Log::error('Slack通知に失敗', [
                'message' => $message,
                'channel' => $channel,
                'statusCode' => isset($response) ? $response->getStatusCode() : '',
                'responseBody' => $json,
            ]);

            throw new \RuntimeException(sprintf('Failed to slack notify. error=%s', $body['error'] ?? ''));
        }
    }
}
