<?php

namespace App\Slack;

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
}
