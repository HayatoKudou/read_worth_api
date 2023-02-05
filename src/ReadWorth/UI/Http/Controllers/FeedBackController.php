<?php

namespace ReadWorth\UI\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\FeedBack\SendRequest;
use ReadWorth\Infrastructure\SlackAPI\SlackApiClient;

class FeedBackController
{
    public function send(SendRequest $request): JsonResponse
    {
        $slackClient = new SlackApiClient(new Client(), config('slack.feedBackChannelAccessToken'));
        $slackClient->postMessage(config('slack.feedBackChannelId'), '', $request->get('message'));
        return response()->json();
    }
}
