<?php

return [
    'clientId' => env('SLACK_CLIENT_ID'),
    'clientSecret' => env('SLACK_CLIENT_SECRET'),
    'redirectUri' => env('SLACK_REDIRECT_URI'),
    'errorChannelAccessToken' => env('SLACK_ERROR_CHANNEL_ACCESS_TOKEN'),
    'errorChannelId' => env('SLACK_ERROR_CHANNEL_ID'),
    'feedBackChannelAccessToken' => env('SLACK_FEEDBACK_CHANNEL_ACCESS_TOKEN'),
    'feedBackChannelId' => env('SLACK_FEEDBACK_CHANNEL_ID'),
];
