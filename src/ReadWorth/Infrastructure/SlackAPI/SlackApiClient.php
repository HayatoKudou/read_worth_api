<?php

namespace ReadWorth\Infrastructure\SlackAPI;

use GuzzleHttp\Client;

class SlackApiClient
{
    private Client $httpClient;
    private string $slackApiToken;

    public function __construct(Client $httpClient, string $slackApiToken = '')
    {
        $this->httpClient = $httpClient;
        $this->slackApiToken = $slackApiToken;
    }

    public function userInfo(string $userId)
    {
        $response = $this->httpClient->get(
            'https://slack.com/api/users.info',
            [
                'headers' => [
                        'Authorization' => 'Bearer ' . $this->slackApiToken,
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                'query' => [
                        'user' => $userId,
                    ],
            ]
        );
        $json = $response->getBody()->getContents();
        return json_decode($json, true);
    }

    public function postMessage(string $channel, string $title, string $message, string $imagePath = null): void
    {
        $response = $this->httpClient->post(
            'https://slack.com/api/chat.postMessage',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->slackApiToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'channel' => $channel,
                    'attachments' => [
                        [
                            'title' => $title,
                            'text' => $message,
                            'image_url' => $imagePath,
                        ],
                    ],
                ],
            ]
        );
        $json = $response->getBody()->getContents();
        $body = json_decode($json, true);

        if (!$body['ok']) {
            \Log::error('Slack通知に失敗', [
                'message' => $message,
                'channel' => $channel,
                'statusCode' => $response->getStatusCode(),
                'responseBody' => $json,
            ]);

            throw new \RuntimeException($body['error']);
        }
    }
}
