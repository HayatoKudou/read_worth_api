<?php

return [
    'clientId' => env('CLIENT_ID'),
    'clientSecret' => env('CLIENT_SECRET'),
    'redirectUri' => env('REDIRECT_URI'),
    'errorChannelAccessToken' => env('SLACK_ERROR_CHANNEL_ACCESS_TOKEN'),
    'errorChannelId' => env('SLACK_ERROR_CHANNEL_ID'),
    'feedBackChannelAccessToken' => env('SLACK_FEEDBACK_CHANNEL_ACCESS_TOKEN'),
    'feedBackChannelId' => env('SLACK_FEEDBACK_CHANNEL_ID'),
];
