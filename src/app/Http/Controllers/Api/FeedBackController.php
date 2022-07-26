<?php

namespace App\Http\Controllers\Api;

use App\Slack\SlackApiClient;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\FeedBack\SendRequest;

class FeedBackController
{
    public function send(SendRequest $request): JsonResponse
    {
        $slackClient = new SlackApiClient(new \GuzzleHttp\Client(), config('slack.feedBackChannelAccessToken'));
        $slackClient->postMessage(config('slack.feedBackChannelId'), '', $request->get('message'));
        return response()->json();
    }
}
